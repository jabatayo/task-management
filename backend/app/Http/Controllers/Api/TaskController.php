<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();
        $user->load('roles');
        $query = Task::with(['createdBy.roles', 'assignedTo.roles']);

        // Role-based filtering
        if (!$user->roles->contains('name', 'Administrator')) {
            // Regular users can only see tasks they created or are assigned to
            $query->where(function ($q) use ($user) {
                $q->where('created_by', $user->id)
                  ->orWhere('assigned_to', $user->id);
            });
        }

        // Apply filters
        if ($request->has('status') && !empty($request->status)) {
            $allowedStatuses = ['pending', 'in_progress', 'completed', 'cancelled'];
            if (in_array($request->status, $allowedStatuses)) {
                $query->byStatus($request->status);
            }
        }

        if ($request->has('priority') && !empty($request->priority)) {
            $allowedPriorities = ['low', 'medium', 'high', 'urgent'];
            if (in_array($request->priority, $allowedPriorities)) {
                $query->byPriority($request->priority);
            }
        }

        if ($request->has('assigned_to') && !empty($request->assigned_to)) {
            $query->assignedTo($request->assigned_to);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'asc');
        
        // Validate sort_by parameter and provide fallback
        $allowedSortColumns = ['created_at', 'due_date', 'priority', 'title', 'status', 'updated_at'];
        if (empty($sortBy) || !in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'created_at'; // Default fallback
        }
        
        // Validate sort order
        $sortOrder = strtolower($sortOrder) === 'desc' ? 'desc' : 'asc';
        
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $tasks = $query->paginate($perPage);

        return TaskResource::collection($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();

        // Set the creator
        $data['created_by'] = $user->id;

        // If no assignee is specified, assign to creator
        if (!isset($data['assigned_to'])) {
            $data['assigned_to'] = $user->id;
        }

        $task = Task::create($data);

        return response()->json([
            'message' => 'Task created successfully',
            'task' => new TaskResource($task->load(['createdBy.roles', 'assignedTo.roles']))
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        $user->load('roles');
        $task = Task::with(['createdBy.roles', 'assignedTo.roles'])->findOrFail($id);

        // Authorization check
        if (!$this->canAccessTask($user, $task)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'task' => new TaskResource($task)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskRequest $request, string $id): JsonResponse
    {
        $user = $request->user();
        $task = Task::findOrFail($id);

        // Authorization check
        if (!$this->canModifyTask($user, $task)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $data = $request->validated();
        $task->update($data);

        return response()->json([
            'message' => 'Task updated successfully',
            'task' => new TaskResource($task->load(['createdBy.roles', 'assignedTo.roles']))
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        $user->load('roles');
        $task = Task::findOrFail($id);

        // Authorization check - only creator or admin can delete
        if (!$user->roles->contains('name', 'Administrator') && $task->created_by !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->delete();

        return response()->json([
            'message' => 'Task deleted successfully'
        ]);
    }

    /**
     * Check if user can access a task
     */
    private function canAccessTask($user, $task): bool
    {        
        // Check if user has Administrator role using loaded relationship
        $isAdmin = $user->roles->contains('name', 'Administrator');
        
        return $isAdmin ||
               $task->created_by === $user->id ||   
               $task->assigned_to === $user->id;
    }

    /**
     * Check if user can modify a task
     */
    private function canModifyTask($user, $task): bool
    {
        return $user->roles->contains('name', 'Administrator') ||
               $task->created_by === $user->id ||
               $task->assigned_to === $user->id;
    }
}
