<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class TaskTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $admin;
    protected $token;
    protected $adminToken;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles
        Role::create(['name' => 'Administrator']);
        Role::create(['name' => 'Regular User']);

        // Create users
        $this->user = User::factory()->create();
        $this->user->assignRole('Regular User');
        
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Administrator');

        // Create tokens
        $this->token = $this->user->createToken('test-token')->plainTextToken;
        $this->adminToken = $this->admin->createToken('admin-token')->plainTextToken;
    }

    /** @test */
    public function user_can_create_task()
    {
        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'priority' => 'high',
            'due_date' => '2025-12-31',
            'tags' => ['test', 'important']
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'task' => [
                         'id',
                         'title',
                         'description',
                         'status',
                         'priority',
                         'due_date',
                         'tags',
                         'created_by',
                         'assigned_to',
                         'created_at',
                         'updated_at'
                     ]
                 ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'priority' => 'high',
            'created_by' => $this->user->id
        ]);
    }

    /** @test */
    public function user_can_create_task_with_minimal_data()
    {
        $taskData = [
            'title' => 'Minimal Task'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/tasks', $taskData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Minimal Task',
            'status' => 'pending',
            'priority' => 'medium'
        ]);
    }

    /** @test */
    public function user_cannot_create_task_without_title()
    {
        $taskData = [
            'description' => 'Test Description',
            'priority' => 'high'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/tasks', $taskData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['title']);
    }

    /** @test */
    public function user_cannot_create_task_with_invalid_priority()
    {
        $taskData = [
            'title' => 'Test Task',
            'priority' => 'invalid_priority'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/tasks', $taskData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['priority']);
    }

    /** @test */
    public function user_cannot_create_task_with_past_due_date()
    {
        $taskData = [
            'title' => 'Test Task',
            'due_date' => '2020-01-01'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/tasks', $taskData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['due_date']);
    }

    /** @test */
    public function user_can_list_their_own_tasks()
    {
        // Create tasks for the user
        Task::factory()->count(3)->create([
            'created_by' => $this->user->id,
            'assigned_to' => $this->user->id
        ]);

        // Create tasks for another user
        Task::factory()->count(2)->create([
            'created_by' => $this->admin->id,
            'assigned_to' => $this->admin->id
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/tasks');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data',
                     'links',
                     'meta'
                 ]);

        $this->assertCount(3, $response->json('data'));
    }

    /** @test */
    public function admin_can_list_all_tasks()
    {
        // Create tasks for different users
        Task::factory()->count(3)->create([
            'created_by' => $this->user->id
        ]);

        Task::factory()->count(2)->create([
            'created_by' => $this->admin->id
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->getJson('/api/tasks');

        $response->assertStatus(200);

        $this->assertCount(5, $response->json('data'));
    }

    /** @test */
    public function user_can_filter_tasks_by_status()
    {
        Task::factory()->create([
            'created_by' => $this->user->id,
            'status' => 'pending'
        ]);

        Task::factory()->create([
            'created_by' => $this->user->id,
            'status' => 'completed'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/tasks?status=pending');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('pending', $response->json('data.0.status'));
    }

    /** @test */
    public function user_can_filter_tasks_by_priority()
    {
        Task::factory()->create([
            'created_by' => $this->user->id,
            'priority' => 'high'
        ]);

        Task::factory()->create([
            'created_by' => $this->user->id,
            'priority' => 'low'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/tasks?priority=high');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('high', $response->json('data.0.priority'));
    }

    /** @test */
    public function user_can_search_tasks()
    {
        Task::factory()->create([
            'created_by' => $this->user->id,
            'title' => 'API Documentation Task',
            'description' => 'Write comprehensive API docs'
        ]);

        Task::factory()->create([
            'created_by' => $this->user->id,
            'title' => 'Database Setup',
            'description' => 'Configure database'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/tasks?search=API');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertStringContainsString('API', $response->json('data.0.title'));
    }

    /** @test */
    public function user_can_get_specific_task()
    {
        $task = Task::factory()->create([
            'created_by' => $this->user->id,
            'assigned_to' => $this->user->id
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'task' => [
                         'id',
                         'title',
                         'description',
                         'status',
                         'priority',
                         'due_date',
                         'tags',
                         'created_by',
                         'assigned_to',
                         'created_at',
                         'updated_at'
                     ]
                 ]);

        $this->assertEquals($task->id, $response->json('task.id'));
    }

    /** @test */
    public function user_cannot_access_task_they_dont_own()
    {
        $task = Task::factory()->create([
            'created_by' => $this->admin->id,
            'assigned_to' => $this->admin->id
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_access_any_task()
    {
        $task = Task::factory()->create([
            'created_by' => $this->user->id,
            'assigned_to' => $this->user->id
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200);
        $this->assertEquals($task->id, $response->json('task.id'));
    }

    /** @test */
    public function user_can_update_their_own_task()
    {
        $task = Task::factory()->create([
            'created_by' => $this->user->id,
            'assigned_to' => $this->user->id
        ]);

        $updateData = [
            'title' => 'Updated Task Title',
            'status' => 'completed',
            'priority' => 'high'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson("/api/tasks/{$task->id}", $updateData);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Task updated successfully']);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Task Title',
            'status' => 'completed',
            'priority' => 'high'
        ]);
    }

    /** @test */
    public function user_cannot_update_task_they_dont_own()
    {
        $task = Task::factory()->create([
            'created_by' => $this->admin->id,
            'assigned_to' => $this->admin->id
        ]);

        $updateData = [
            'title' => 'Updated Task Title'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson("/api/tasks/{$task->id}", $updateData);

        $response->assertStatus(403);
    }

    /** @test */
    public function user_can_delete_their_own_task()
    {
        $task = Task::factory()->create([
            'created_by' => $this->user->id,
            'assigned_to' => $this->user->id
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Task deleted successfully']);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    /** @test */
    public function user_cannot_delete_task_they_dont_own()
    {
        $task = Task::factory()->create([
            'created_by' => $this->admin->id,
            'assigned_to' => $this->admin->id
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_delete_any_task()
    {
        $task = Task::factory()->create([
            'created_by' => $this->user->id,
            'assigned_to' => $this->user->id
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    /** @test */
    public function tasks_are_paginated()
    {
        Task::factory()->count(25)->create([
            'created_by' => $this->user->id
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/tasks?per_page=10');

        $response->assertStatus(200);
        $this->assertCount(10, $response->json('data'));
        $this->assertEquals(25, $response->json('meta.total'));
        $this->assertEquals(3, $response->json('meta.last_page'));
    }

    /** @test */
    public function tasks_can_be_sorted()
    {
        Task::factory()->create([
            'created_by' => $this->user->id,
            'title' => 'A Task',
            'created_at' => now()->subDays(2)
        ]);

        Task::factory()->create([
            'created_by' => $this->user->id,
            'title' => 'Z Task',
            'created_at' => now()->subDays(1)
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/tasks?sort_by=title&sort_order=asc');

        $response->assertStatus(200);
        $this->assertEquals('A Task', $response->json('data.0.title'));
        $this->assertEquals('Z Task', $response->json('data.1.title'));
    }

    /** @test */
    public function user_can_assign_task_to_another_user()
    {
        $taskData = [
            'title' => 'Assigned Task',
            'assigned_to' => $this->admin->id
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/tasks', $taskData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Assigned Task',
            'assigned_to' => $this->admin->id,
            'created_by' => $this->user->id
        ]);
    }

    /** @test */
    public function user_can_see_tasks_assigned_to_them()
    {
        // Task created by user and assigned to admin
        Task::factory()->create([
            'created_by' => $this->user->id,
            'assigned_to' => $this->admin->id
        ]);

        // Task created by admin and assigned to user
        Task::factory()->create([
            'created_by' => $this->admin->id,
            'assigned_to' => $this->user->id
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/tasks');

        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data'));
    }

    /** @test */
    public function task_not_found_returns_404()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/tasks/99999');

        $response->assertStatus(404);
    }
} 