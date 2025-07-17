<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Department extends Model
{
    protected $table = 'departments';
    
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'workspace_id',
        'name',
        'description',
        'parent_department_id',
        'manager_id',
        'budget',
        'settings',
        'status',
        'created_by'
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'settings' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

    /**
     * Get the workspace that owns the department
     */
    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    /**
     * Get the department manager
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get the parent department
     */
    public function parent()
    {
        return $this->belongsTo(Department::class, 'parent_department_id');
    }

    /**
     * Get child departments
     */
    public function children()
    {
        return $this->hasMany(Department::class, 'parent_department_id');
    }

    /**
     * Get all descendant departments
     */
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    /**
     * Get department users
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'department_users', 'department_id', 'user_id')
                    ->withPivot(['role', 'joined_at', 'status'])
                    ->withTimestamps();
    }

    /**
     * Get department teams
     */
    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    /**
     * Get the user who created the department
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if department is active
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Get department hierarchy path
     */
    public function getHierarchyPath()
    {
        $path = [$this->name];
        $parent = $this->parent;
        
        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parent;
        }
        
        return implode(' > ', $path);
    }

    /**
     * Get department level in hierarchy
     */
    public function getLevel()
    {
        $level = 0;
        $parent = $this->parent;
        
        while ($parent) {
            $level++;
            $parent = $parent->parent;
        }
        
        return $level;
    }

    /**
     * Get total users count (including sub-departments)
     */
    public function getTotalUsersCount()
    {
        $count = $this->users()->count();
        
        foreach ($this->children as $child) {
            $count += $child->getTotalUsersCount();
        }
        
        return $count;
    }

    /**
     * Check if user is department manager
     */
    public function isManager(User $user)
    {
        return $this->manager_id === $user->id;
    }

    /**
     * Check if user belongs to department
     */
    public function hasUser(User $user)
    {
        return $this->users()->where('user_id', $user->id)->exists();
    }

    /**
     * Add user to department
     */
    public function addUser(User $user, $role = 'member')
    {
        return $this->users()->attach($user->id, [
            'role' => $role,
            'joined_at' => now(),
            'status' => 'active'
        ]);
    }

    /**
     * Remove user from department
     */
    public function removeUser(User $user)
    {
        return $this->users()->detach($user->id);
    }

    /**
     * Update user role in department
     */
    public function updateUserRole(User $user, $role)
    {
        return $this->users()->updateExistingPivot($user->id, ['role' => $role]);
    }
}