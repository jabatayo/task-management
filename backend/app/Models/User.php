<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Contact;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the roles that belong to the user.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole($role)
    {
        return $this->roles()->where('name', $role)->exists();
    }

    /**
     * Check if user has any of the given roles.
     */
    public function hasAnyRole($roles)
    {
        return $this->roles()->whereIn('name', (array) $roles)->exists();
    }

    /**
     * Get tasks created by this user.
     */
    public function createdTasks()
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    /**
     * Get tasks assigned to this user.
     */
    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    /**
     * Get contacts submitted by this user.
     */
    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    /**
     * Assign a role to the user.
     */
    public function assignRole($roleName)
    {
        $role = Role::where('name', $roleName)->first();
        if ($role && !$this->hasRole($roleName)) {
            $this->roles()->attach($role->id);
        }
        return $this;
    }

    /**
     * Remove a role from the user.
     */
    public function removeRole($roleName)
    {
        $role = Role::where('name', $roleName)->first();
        if ($role) {
            $this->roles()->detach($role->id);
        }
        return $this;
    }

    /**
     * Check if user is an administrator.
     */
    public function isAdmin()
    {
        return $this->hasRole('Administrator');
    }

    /**
     * Check if user is a regular user.
     */
    public function isRegularUser()
    {
        return $this->hasRole('Regular User');
    }

    /**
     * Get all tasks for this user (based on role).
     */
    public function getAllTasks()
    {
        if ($this->isAdmin()) {
            return Task::with(['createdBy', 'assignedTo'])->get();
        }
        
        return Task::with(['createdBy', 'assignedTo'])
            ->where(function ($query) {
                $query->where('created_by', $this->id)
                      ->orWhere('assigned_to', $this->id);
            })
            ->get();
    }

    /**
     * Get task statistics for this user.
     */
    public function getTaskStatistics()
    {
        $tasks = $this->getAllTasks();
        
        $total = $tasks->count();
        $completed = $tasks->where('status', 'completed')->count();
        $pending = $tasks->where('status', 'pending')->count();
        $inProgress = $tasks->where('status', 'in_progress')->count();
        $cancelled = $tasks->where('status', 'cancelled')->count();
        
        $completionRate = $total > 0 ? round(($completed / $total) * 100, 2) : 0;
        
        return [
            'total_tasks' => $total,
            'completed_tasks' => $completed,
            'pending_tasks' => $pending,
            'in_progress_tasks' => $inProgress,
            'cancelled_tasks' => $cancelled,
            'completion_rate' => $completionRate
        ];
    }

    /**
     * Get overdue tasks for this user.
     */
    public function getOverdueTasks()
    {
        return $this->getAllTasks()
            ->where('due_date', '<', now())
            ->where('status', '!=', 'completed')
            ->where('status', '!=', 'cancelled');
    }

    /**
     * Get upcoming deadlines for this user.
     */
    public function getUpcomingDeadlines()
    {
        return $this->getAllTasks()
            ->where('due_date', '>=', now())
            ->where('due_date', '<=', now()->addDays(7))
            ->where('status', '!=', 'completed')
            ->where('status', '!=', 'cancelled');
    }
}
