<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class DashboardTest extends TestCase
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
    public function user_can_access_dashboard()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/dashboard');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'task_statistics',
                     'recent_activity',
                     'performance_metrics',
                     'priority_distribution',
                     'status_distribution',
                     'overdue_tasks',
                     'upcoming_deadlines'
                 ]);
    }

    /** @test */
    public function dashboard_shows_correct_task_statistics_for_user()
    {
        // Create tasks for the user
        Task::factory()->create([
            'created_by' => $this->user->id,
            'assigned_to' => $this->user->id,
            'status' => 'pending'
        ]);

        Task::factory()->create([
            'created_by' => $this->user->id,
            'assigned_to' => $this->user->id,
            'status' => 'completed'
        ]);

        Task::factory()->create([
            'created_by' => $this->user->id,
            'assigned_to' => $this->user->id,
            'status' => 'in_progress'
        ]);

        // Create tasks for admin (should not be counted for user)
        Task::factory()->create([
            'created_by' => $this->admin->id,
            'assigned_to' => $this->admin->id,
            'status' => 'completed'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/dashboard');

        $response->assertStatus(200);

        $statistics = $response->json('task_statistics');
        $this->assertEquals(3, $statistics['total_tasks']);
        $this->assertEquals(1, $statistics['completed_tasks']);
        $this->assertEquals(1, $statistics['pending_tasks']);
        $this->assertEquals(1, $statistics['in_progress_tasks']);
        $this->assertEquals(33.33, $statistics['completion_rate']);
    }

    /** @test */
    public function dashboard_shows_correct_task_statistics_for_admin()
    {
        // Create tasks for both users
        Task::factory()->create([
            'created_by' => $this->user->id,
            'assigned_to' => $this->user->id,
            'status' => 'pending'
        ]);

        Task::factory()->create([
            'created_by' => $this->admin->id,
            'assigned_to' => $this->admin->id,
            'status' => 'completed'
        ]);

        Task::factory()->create([
            'created_by' => $this->user->id,
            'assigned_to' => $this->admin->id,
            'status' => 'in_progress'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->getJson('/api/dashboard');

        $response->assertStatus(200);

        $statistics = $response->json('task_statistics');
        $this->assertEquals(3, $statistics['total_tasks']);
        $this->assertEquals(1, $statistics['completed_tasks']);
        $this->assertEquals(1, $statistics['pending_tasks']);
        $this->assertEquals(1, $statistics['in_progress_tasks']);
    }

    /** @test */
    public function dashboard_shows_recent_activity()
    {
        // Create recent tasks
        Task::factory()->create([
            'created_by' => $this->user->id,
            'assigned_to' => $this->user->id,
            'title' => 'Recent Task 1',
            'updated_at' => now()->subHours(1)
        ]);

        Task::factory()->create([
            'created_by' => $this->user->id,
            'assigned_to' => $this->user->id,
            'title' => 'Recent Task 2',
            'updated_at' => now()->subHours(2)
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/dashboard');

        $response->assertStatus(200);

        $recentActivity = $response->json('recent_activity');
        $this->assertCount(2, $recentActivity);
        $this->assertEquals('Recent Task 1', $recentActivity[0]['title']);
        $this->assertEquals('Recent Task 2', $recentActivity[1]['title']);
    }

    /** @test */
    public function dashboard_shows_performance_metrics()
    {
        // Create tasks this month
        Task::factory()->count(5)->create([
            'created_by' => $this->user->id,
            'assigned_to' => $this->user->id,
            'created_at' => now()
        ]);

        Task::factory()->count(2)->create([
            'created_by' => $this->user->id,
            'assigned_to' => $this->user->id,
            'status' => 'completed',
            'created_at' => now(),
            'updated_at' => now()->addDays(3)
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/dashboard');

        $response->assertStatus(200);

        $metrics = $response->json('performance_metrics');
        $this->assertEquals(7, $metrics['tasks_created_this_month']);
        $this->assertEquals(2, $metrics['tasks_completed_this_month']);
        $this->assertEquals(28.57, $metrics['completion_rate_this_month']);
    }

    /** @test */
    public function dashboard_shows_priority_distribution()
    {
        Task::factory()->create([
            'created_by' => $this->user->id,
            'assigned_to' => $this->user->id,
            'priority' => 'high'
        ]);

        Task::factory()->create([
            'created_by' => $this->user->id,
            'assigned_to' => $this->user->id,
            'priority' => 'low'
        ]);

        Task::factory()->create([
            'created_by' => $this->user->id,
            'assigned_to' => $this->user->id,
            'priority' => 'medium'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/dashboard');

        $response->assertStatus(200);

        $priorityDistribution = $response->json('priority_distribution');
        $this->assertEquals(1, $priorityDistribution['high']);
        $this->assertEquals(1, $priorityDistribution['medium']);
        $this->assertEquals(1, $priorityDistribution['low']);
        $this->assertEquals(0, $priorityDistribution['urgent']);
    }

    /** @test */
    public function dashboard_shows_status_distribution()
    {
        Task::factory()->create([
            'created_by' => $this->user->id,
            'assigned_to' => $this->user->id,
            'status' => 'pending'
        ]);

        Task::factory()->create([
            'created_by' => $this->user->id,
            'assigned_to' => $this->user->id,
            'status' => 'completed'
        ]);

        Task::factory()->create([
            'created_by' => $this->user->id,
            'assigned_to' => $this->user->id,
            'status' => 'in_progress'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/dashboard');

        $response->assertStatus(200);

        $statusDistribution = $response->json('status_distribution');
        $this->assertEquals(1, $statusDistribution['pending']);
        $this->assertEquals(1, $statusDistribution['completed']);
        $this->assertEquals(1, $statusDistribution['in_progress']);
        $this->assertEquals(0, $statusDistribution['cancelled']);
    }

    /** @test */
    public function dashboard_shows_overdue_tasks()
    {
        Task::factory()->create([
            'created_by' => $this->user->id,
            'assigned_to' => $this->user->id,
            'due_date' => now()->subDays(5),
            'status' => 'pending'
        ]);

        Task::factory()->create([
            'created_by' => $this->user->id,
            'assigned_to' => $this->user->id,
            'due_date' => now()->subDays(1),
            'status' => 'in_progress'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/dashboard');

        $response->assertStatus(200);

        $overdueTasks = $response->json('overdue_tasks');
        $this->assertCount(2, $overdueTasks);
    }

    /** @test */
    public function dashboard_shows_upcoming_deadlines()
    {
        Task::factory()->create([
            'created_by' => $this->user->id,
            'assigned_to' => $this->user->id,
            'due_date' => now()->addDays(1),
            'status' => 'pending'
        ]);

        Task::factory()->create([
            'created_by' => $this->user->id,
            'assigned_to' => $this->user->id,
            'due_date' => now()->addDays(3),
            'status' => 'in_progress'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/dashboard');

        $response->assertStatus(200);

        $upcomingDeadlines = $response->json('upcoming_deadlines');
        $this->assertCount(2, $upcomingDeadlines);
        $this->assertEquals(1, $upcomingDeadlines[0]['days_until_due']);
        $this->assertEquals(3, $upcomingDeadlines[1]['days_until_due']);
    }

    /** @test */
    public function dashboard_does_not_show_other_users_tasks_for_regular_user()
    {
        // Create tasks for admin
        Task::factory()->count(3)->create([
            'created_by' => $this->admin->id,
            'assigned_to' => $this->admin->id
        ]);

        // Create one task assigned to user by admin
        Task::factory()->create([
            'created_by' => $this->admin->id,
            'assigned_to' => $this->user->id
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/dashboard');

        $response->assertStatus(200);

        $statistics = $response->json('task_statistics');
        $this->assertEquals(1, $statistics['total_tasks']); // Only the assigned task
    }

    /** @test */
    public function dashboard_shows_all_tasks_for_admin()
    {
        // Create tasks for both users
        Task::factory()->count(2)->create([
            'created_by' => $this->user->id,
            'assigned_to' => $this->user->id
        ]);

        Task::factory()->count(3)->create([
            'created_by' => $this->admin->id,
            'assigned_to' => $this->admin->id
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->getJson('/api/dashboard');

        $response->assertStatus(200);

        $statistics = $response->json('task_statistics');
        $this->assertEquals(5, $statistics['total_tasks']);
    }

    /** @test */
    public function dashboard_handles_empty_data_correctly()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/dashboard');

        $response->assertStatus(200);

        $statistics = $response->json('task_statistics');
        $this->assertEquals(0, $statistics['total_tasks']);
        $this->assertEquals(0, $statistics['completed_tasks']);
        $this->assertEquals(0, $statistics['completion_rate']);

        $recentActivity = $response->json('recent_activity');
        $this->assertEmpty($recentActivity);

        $overdueTasks = $response->json('overdue_tasks');
        $this->assertEmpty($overdueTasks);

        $upcomingDeadlines = $response->json('upcoming_deadlines');
        $this->assertEmpty($upcomingDeadlines);
    }

    /** @test */
    public function dashboard_calculates_average_completion_time()
    {
        // Create completed tasks with different completion times
        Task::factory()->create([
            'created_by' => $this->user->id,
            'assigned_to' => $this->user->id,
            'status' => 'completed',
            'created_at' => now()->subDays(10),
            'updated_at' => now()->subDays(5) // 5 days to complete
        ]);

        Task::factory()->create([
            'created_by' => $this->user->id,
            'assigned_to' => $this->user->id,
            'status' => 'completed',
            'created_at' => now()->subDays(8),
            'updated_at' => now()->subDays(2) // 6 days to complete
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/dashboard');

        $response->assertStatus(200);

        $metrics = $response->json('performance_metrics');
        $this->assertEquals(5.5, $metrics['average_completion_time_days']);
    }

    /** @test */
    public function dashboard_requires_authentication()
    {
        $response = $this->getJson('/api/dashboard');

        $response->assertStatus(401)
                 ->assertJson(['message' => 'Unauthenticated.']);
    }

    /** @test */
    public function dashboard_handles_tasks_without_due_dates()
    {
        Task::factory()->create([
            'created_by' => $this->user->id,
            'assigned_to' => $this->user->id,
            'due_date' => null
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/dashboard');

        $response->assertStatus(200);

        $statistics = $response->json('task_statistics');
        $this->assertEquals(1, $statistics['total_tasks']);

        $overdueTasks = $response->json('overdue_tasks');
        $this->assertEmpty($overdueTasks);

        $upcomingDeadlines = $response->json('upcoming_deadlines');
        $this->assertEmpty($upcomingDeadlines);
    }
} 