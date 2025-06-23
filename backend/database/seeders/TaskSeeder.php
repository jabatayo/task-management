<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Task;
use Carbon\Carbon;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tasks = [
            // Completed tasks (for completion rate)
            [
                'title' => 'Complete project documentation',
                'description' => 'Write comprehensive documentation for the new feature',
                'status' => 'completed',
                'priority' => 'high',
                'due_date' => Carbon::now()->subDays(2),
                'created_by' => 1,
                'assigned_to' => 1,
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'title' => 'Review code changes',
                'description' => 'Review pull request for the authentication module',
                'status' => 'completed',
                'priority' => 'medium',
                'due_date' => Carbon::now()->subDays(1),
                'created_by' => 1,
                'assigned_to' => 1,
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'title' => 'Update dependencies',
                'description' => 'Update all npm packages to latest versions',
                'status' => 'completed',
                'priority' => 'low',
                'due_date' => Carbon::now()->subDays(3),
                'created_by' => 1,
                'assigned_to' => 1,
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'title' => 'Fix login bug',
                'description' => 'Resolve authentication issue in mobile app',
                'status' => 'completed',
                'priority' => 'urgent',
                'due_date' => Carbon::now()->subDays(1),
                'created_by' => 1,
                'assigned_to' => 1,
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'title' => 'Setup CI/CD pipeline',
                'description' => 'Configure automated testing and deployment',
                'status' => 'completed',
                'priority' => 'high',
                'due_date' => Carbon::now()->subDays(5),
                'created_by' => 1,
                'assigned_to' => 1,
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(4),
            ],

            // In Progress tasks
            [
                'title' => 'Implement user dashboard',
                'description' => 'Create comprehensive dashboard with charts and metrics',
                'status' => 'in_progress',
                'priority' => 'high',
                'due_date' => Carbon::now()->addDays(3),
                'created_by' => 1,
                'assigned_to' => 1,
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Design mobile app UI',
                'description' => 'Create wireframes and mockups for mobile interface',
                'status' => 'in_progress',
                'priority' => 'medium',
                'due_date' => Carbon::now()->addDays(7),
                'created_by' => 1,
                'assigned_to' => 1,
                'created_at' => Carbon::now()->subDays(4),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Optimize database queries',
                'description' => 'Improve performance of slow database operations',
                'status' => 'in_progress',
                'priority' => 'high',
                'due_date' => Carbon::now()->addDays(2),
                'created_by' => 1,
                'assigned_to' => 1,
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now(),
            ],

            // Pending tasks
            [
                'title' => 'Write unit tests',
                'description' => 'Add comprehensive test coverage for new features',
                'status' => 'pending',
                'priority' => 'medium',
                'due_date' => Carbon::now()->addDays(5),
                'created_by' => 1,
                'assigned_to' => 1,
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'title' => 'Plan next sprint',
                'description' => 'Organize tasks and estimate effort for upcoming sprint',
                'status' => 'pending',
                'priority' => 'low',
                'due_date' => Carbon::now()->addDays(2),
                'created_by' => 1,
                'assigned_to' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Update API documentation',
                'description' => 'Document new endpoints and update existing docs',
                'status' => 'pending',
                'priority' => 'medium',
                'due_date' => Carbon::now()->addDays(4),
                'created_by' => 1,
                'assigned_to' => 1,
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'title' => 'Security audit',
                'description' => 'Conduct comprehensive security review of the application',
                'status' => 'pending',
                'priority' => 'urgent',
                'due_date' => Carbon::now()->addDays(1),
                'created_by' => 1,
                'assigned_to' => 1,
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],

            // Overdue tasks (for overdue metrics)
            [
                'title' => 'Fix production bug',
                'description' => 'Critical bug affecting user registration',
                'status' => 'pending',
                'priority' => 'urgent',
                'due_date' => Carbon::now()->subDays(2),
                'created_by' => 1,
                'assigned_to' => 1,
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'title' => 'Update privacy policy',
                'description' => 'Update privacy policy to comply with new regulations',
                'status' => 'pending',
                'priority' => 'high',
                'due_date' => Carbon::now()->subDays(1),
                'created_by' => 1,
                'assigned_to' => 1,
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(1),
            ],

            // Cancelled tasks
            [
                'title' => 'Old feature implementation',
                'description' => 'Feature that was cancelled due to scope changes',
                'status' => 'cancelled',
                'priority' => 'medium',
                'due_date' => Carbon::now()->addDays(10),
                'created_by' => 1,
                'assigned_to' => 1,
                'created_at' => Carbon::now()->subDays(15),
                'updated_at' => Carbon::now()->subDays(5),
            ],

            // Upcoming deadlines (for dashboard)
            [
                'title' => 'Release version 2.0',
                'description' => 'Prepare and deploy major version update',
                'status' => 'pending',
                'priority' => 'urgent',
                'due_date' => Carbon::now()->addDays(1),
                'created_by' => 1,
                'assigned_to' => 1,
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(3),
            ],
            [
                'title' => 'Client presentation',
                'description' => 'Prepare presentation for client meeting',
                'status' => 'pending',
                'priority' => 'high',
                'due_date' => Carbon::now()->addDays(2),
                'created_by' => 1,
                'assigned_to' => 1,
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'title' => 'Team retrospective',
                'description' => 'Conduct team retrospective meeting',
                'status' => 'pending',
                'priority' => 'medium',
                'due_date' => Carbon::now()->addDays(3),
                'created_by' => 1,
                'assigned_to' => 1,
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ],
        ];

        foreach ($tasks as $task) {
            Task::create($task);
        }

        $this->command->info('Tasks seeded successfully!');
        $this->command->info('Created ' . count($tasks) . ' tasks for user_id = 1');
    }
}
