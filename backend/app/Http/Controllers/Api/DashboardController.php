<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display dashboard metrics and analytics
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load('roles');
        $query = Task::query();

        // Role-based data filtering
        if (!$user->roles->contains('name', 'Administrator')) {
            $query->where(function ($q) use ($user) {
                $q->where('created_by', $user->id)
                  ->orWhere('assigned_to', $user->id);
            });
        }

        // Get current date for calculations
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        // Task Statistics
        $taskStats = $this->getTaskStatistics($query);
        
        // Recent Activity
        $recentActivity = $this->getRecentActivity($query);
        
        // Performance Metrics
        $performanceMetrics = $this->getPerformanceMetrics($query, $startOfMonth, $endOfMonth);
        
        // Priority Distribution
        $priorityDistribution = $this->getPriorityDistribution($query);
        
        // Status Distribution
        $statusDistribution = $this->getStatusDistribution($query);
        
        // Overdue Tasks
        $overdueTasks = $this->getOverdueTasks($query);
        
        // Upcoming Deadlines
        $upcomingDeadlines = $this->getUpcomingDeadlines($query);

        return response()->json([
            'task_statistics' => $taskStats,
            'recent_activity' => $recentActivity,
            'performance_metrics' => $performanceMetrics,
            'priority_distribution' => $priorityDistribution,
            'status_distribution' => $statusDistribution,
            'overdue_tasks' => $overdueTasks,
            'upcoming_deadlines' => $upcomingDeadlines,
        ]);
    }

    /**
     * Get overall task statistics
     */
    private function getTaskStatistics($query): array
    {
        $totalTasks = $query->count();
        $completedTasks = (clone $query)->where('status', 'completed')->count();
        $pendingTasks = (clone $query)->where('status', 'pending')->count();
        $inProgressTasks = (clone $query)->where('status', 'in_progress')->count();
        $cancelledTasks = (clone $query)->where('status', 'cancelled')->count();

        return [
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'pending_tasks' => $pendingTasks,
            'in_progress_tasks' => $inProgressTasks,
            'cancelled_tasks' => $cancelledTasks,
            'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0,
        ];
    }

    /**
     * Get recent activity (last 10 tasks created/updated)
     */
    private function getRecentActivity($query): array
    {
        return (clone $query)
            ->with(['createdBy', 'assignedTo'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'status' => $task->status,
                    'priority' => $task->priority,
                    'updated_at' => $task->updated_at->format('Y-m-d H:i:s'),
                    'creator' => [
                        'id' => $task->createdBy->id,
                        'name' => $task->createdBy->name,
                    ],
                    'assignee' => $task->assignedTo ? [
                        'id' => $task->assignedTo->id,
                        'name' => $task->assignedTo->name,
                    ] : null,
                ];
            })
            ->toArray();
    }

    /**
     * Get performance metrics for current month
     */
    private function getPerformanceMetrics($query, $startOfMonth, $endOfMonth): array
    {
        $baseMonthlyQuery = clone $query;
        $baseMonthlyQuery->whereBetween('created_at', [$startOfMonth, $endOfMonth]);

        $tasksCreatedThisMonth = (clone $baseMonthlyQuery)->count();
        $tasksCompletedThisMonth = (clone $baseMonthlyQuery)
            ->where('status', 'completed')
            ->whereBetween('updated_at', [$startOfMonth, $endOfMonth])
            ->whereColumn('created_at', '!=', 'updated_at')
            ->count();
        
        // Calculate average completion time for completed tasks
        $avgCompletionTime = (clone $baseMonthlyQuery)
            ->where('status', 'completed')
            ->whereNotNull('updated_at')
            ->get()
            ->avg(function ($task) {
                return $task->created_at->diffInDays($task->updated_at);
            });

        return [
            'tasks_created_this_month' => $tasksCreatedThisMonth,
            'tasks_completed_this_month' => $tasksCompletedThisMonth,
            'completion_rate_this_month' => $tasksCreatedThisMonth > 0 ? round(($tasksCompletedThisMonth / $tasksCreatedThisMonth) * 100, 2) : 0,
            'average_completion_time_days' => round($avgCompletionTime ?? 0, 1),
        ];
    }

    /**
     * Get priority distribution
     */
    private function getPriorityDistribution($query): array
    {
        $priorities = ['low', 'medium', 'high', 'urgent'];
        $distribution = [];

        foreach ($priorities as $priority) {
            $count = (clone $query)->where('priority', $priority)->count();
            $distribution[$priority] = $count;
        }

        return $distribution;
    }

    /**
     * Get status distribution
     */
    private function getStatusDistribution($query): array
    {
        $statuses = ['pending', 'in_progress', 'completed', 'cancelled'];
        $distribution = [];

        foreach ($statuses as $status) {
            $count = (clone $query)->where('status', $status)->count();
            $distribution[$status] = $count;
        }

        return $distribution;
    }

    /**
     * Get overdue tasks
     */
    private function getOverdueTasks($query): array
    {
        return (clone $query)
            ->overdue()
            ->with(['createdBy', 'assignedTo'])
            ->orderBy('due_date', 'asc')
            ->limit(5)
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'priority' => $task->priority,
                    'due_date' => $task->due_date->format('Y-m-d'),
                    'days_overdue' => $task->due_date->diffInDays(now()),
                    'assignee' => $task->assignedTo ? [
                        'id' => $task->assignedTo->id,
                        'name' => $task->assignedTo->name,
                    ] : null,
                ];
            })
            ->toArray();
    }

    /**
     * Get upcoming deadlines (next 7 days)
     */
    private function getUpcomingDeadlines($query): array
    {
        $nextWeek = now()->addDays(7);
        
        return (clone $query)
            ->whereNotNull('due_date')
            ->where('due_date', '>=', now()->toDateString())
            ->where('due_date', '<=', $nextWeek->toDateString())
            ->where('status', '!=', 'completed')
            ->with(['createdBy', 'assignedTo'])
            ->orderBy('due_date', 'asc')
            ->limit(5)
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'priority' => $task->priority,
                    'due_date' => $task->due_date->format('Y-m-d'),
                    'days_until_due' => now()->startOfDay()->diffInDays($task->due_date->startOfDay(), false),
                    'assignee' => $task->assignedTo ? [
                        'id' => $task->assignedTo->id,
                        'name' => $task->assignedTo->name,
                    ] : null,
                ];
            })
            ->toArray();
    }
}
