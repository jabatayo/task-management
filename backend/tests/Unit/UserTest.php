<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Task;
use App\Models\Contact;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class UserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles
        Role::create(['name' => 'Administrator']);
        Role::create(['name' => 'Regular User']);
    }

    /** @test */
    public function user_can_have_roles()
    {
        $user = User::factory()->create();
        $role = Role::where('name', 'Administrator')->first();

        $user->roles()->attach($role->id);

        $this->assertTrue($user->hasRole('Administrator'));
        $this->assertFalse($user->hasRole('Regular User'));
    }

    /** @test */
    public function user_can_have_multiple_roles()
    {
        $user = User::factory()->create();
        $adminRole = Role::where('name', 'Administrator')->first();
        $regularRole = Role::where('name', 'Regular User')->first();

        $user->roles()->attach([$adminRole->id, $regularRole->id]);

        $this->assertTrue($user->hasRole('Administrator'));
        $this->assertTrue($user->hasRole('Regular User'));
    }

    /** @test */
    public function user_can_assign_role()
    {
        $user = User::factory()->create();
        
        $user->assignRole('Administrator');

        $this->assertTrue($user->hasRole('Administrator'));
    }

    /** @test */
    public function user_can_remove_role()
    {
        $user = User::factory()->create();
        $user->assignRole('Administrator');

        $this->assertTrue($user->hasRole('Administrator'));

        $user->removeRole('Administrator');

        $this->assertFalse($user->hasRole('Administrator'));
    }

    /** @test */
    public function user_can_have_created_tasks()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['created_by' => $user->id]);

        $this->assertTrue($user->createdTasks->contains($task));
        $this->assertEquals(1, $user->createdTasks->count());
    }

    /** @test */
    public function user_can_have_assigned_tasks()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['assigned_to' => $user->id]);

        $this->assertTrue($user->assignedTasks->contains($task));
        $this->assertEquals(1, $user->assignedTasks->count());
    }

    /** @test */
    public function user_can_have_contacts()
    {
        $user = User::factory()->create();
        $contact = Contact::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->contacts->contains($contact));
        $this->assertEquals(1, $user->contacts->count());
    }

    /** @test */
    public function user_password_is_hashed()
    {
        $user = User::factory()->create([
            'password' => 'plaintext_password'
        ]);

        $this->assertNotEquals('plaintext_password', $user->password);
        $this->assertTrue(Hash::check('plaintext_password', $user->password));
    }

    /** @test */
    public function user_email_is_unique()
    {
        $email = 'test@example.com';
        
        User::factory()->create(['email' => $email]);

        $this->expectException(\Illuminate\Database\QueryException::class);
        
        User::factory()->create(['email' => $email]);
    }

    /** @test */
    public function user_can_create_token()
    {
        $user = User::factory()->create();
        
        $token = $user->createToken('test-token');

        $this->assertNotNull($token);
        $this->assertNotNull($token->plainTextToken);
    }

    /** @test */
    public function user_can_revoke_all_tokens()
    {
        $user = User::factory()->create();
        
        $token1 = $user->createToken('token1');
        $token2 = $user->createToken('token2');

        $user->tokens()->delete();

        $this->assertEquals(0, $user->tokens()->count());
    }

    /** @test */
    public function user_has_fillable_fields()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ]);

        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
    }

    /** @test */
    public function user_has_hidden_fields()
    {
        $user = User::factory()->create();
        
        $userArray = $user->toArray();

        $this->assertArrayNotHasKey('password', $userArray);
        $this->assertArrayNotHasKey('remember_token', $userArray);
    }

    /** @test */
    public function user_can_be_admin()
    {
        $user = User::factory()->create();
        $user->assignRole('Administrator');

        $this->assertTrue($user->isAdmin());
    }

    /** @test */
    public function user_can_be_regular_user()
    {
        $user = User::factory()->create();
        $user->assignRole('Regular User');

        $this->assertTrue($user->isRegularUser());
    }

    /** @test */
    public function user_can_get_all_tasks()
    {
        $user = User::factory()->create();
        $user->assignRole('Administrator');

        // Create tasks by different users
        Task::factory()->create(['created_by' => $user->id]);
        Task::factory()->create(['assigned_to' => $user->id]);
        Task::factory()->create(['created_by' => $user->id, 'assigned_to' => $user->id]);

        $this->assertEquals(3, $user->getAllTasks()->count());
    }

    /** @test */
    public function regular_user_gets_only_their_tasks()
    {
        $user = User::factory()->create();
        $user->assignRole('Regular User');
        
        $otherUser = User::factory()->create();

        // Create tasks
        Task::factory()->create(['created_by' => $user->id]);
        Task::factory()->create(['assigned_to' => $user->id]);
        Task::factory()->create(['created_by' => $otherUser->id, 'assigned_to' => $otherUser->id]);

        $this->assertEquals(2, $user->getAllTasks()->count());
    }

    /** @test */
    public function user_can_get_task_statistics()
    {
        $user = User::factory()->create();
        
        Task::factory()->create([
            'created_by' => $user->id,
            'assigned_to' => $user->id,
            'status' => 'completed'
        ]);

        Task::factory()->create([
            'created_by' => $user->id,
            'assigned_to' => $user->id,
            'status' => 'pending'
        ]);

        $stats = $user->getTaskStatistics();

        $this->assertEquals(2, $stats['total_tasks']);
        $this->assertEquals(1, $stats['completed_tasks']);
        $this->assertEquals(1, $stats['pending_tasks']);
        $this->assertEquals(50.0, $stats['completion_rate']);
    }

    /** @test */
    public function user_can_get_overdue_tasks()
    {
        $user = User::factory()->create();
        
        Task::factory()->create([
            'created_by' => $user->id,
            'assigned_to' => $user->id,
            'due_date' => now()->subDays(1),
            'status' => 'pending'
        ]);

        Task::factory()->create([
            'created_by' => $user->id,
            'assigned_to' => $user->id,
            'due_date' => now()->addDays(1),
            'status' => 'pending'
        ]);

        $overdueTasks = $user->getOverdueTasks();

        $this->assertEquals(1, $overdueTasks->count());
    }

    /** @test */
    public function user_can_get_upcoming_deadlines()
    {
        $user = User::factory()->create();
        
        Task::factory()->create([
            'created_by' => $user->id,
            'assigned_to' => $user->id,
            'due_date' => now()->addDays(1),
            'status' => 'pending'
        ]);

        Task::factory()->create([
            'created_by' => $user->id,
            'assigned_to' => $user->id,
            'due_date' => now()->addDays(5),
            'status' => 'pending'
        ]);

        $upcomingTasks = $user->getUpcomingDeadlines();

        $this->assertEquals(2, $upcomingTasks->count());
    }

    /** @test */
    public function user_model_has_correct_table_name()
    {
        $user = new User();
        
        $this->assertEquals('users', $user->getTable());
    }

    /** @test */
    public function user_model_has_correct_primary_key()
    {
        $user = new User();
        
        $this->assertEquals('id', $user->getKeyName());
    }

    /** @test */
    public function user_model_uses_timestamps()
    {
        $user = new User();
        
        $this->assertTrue($user->usesTimestamps());
    }

    /** @test */
    public function user_can_be_created_with_factory()
    {
        $user = User::factory()->create();
        
        $this->assertInstanceOf(User::class, $user);
        $this->assertNotNull($user->id);
        $this->assertNotNull($user->name);
        $this->assertNotNull($user->email);
    }

    /** @test */
    public function user_factory_creates_unique_emails()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $this->assertNotEquals($user1->email, $user2->email);
    }
} 