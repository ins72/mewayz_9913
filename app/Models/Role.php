<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Role extends Model
{
    protected $table = 'roles';
    
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'workspace_id',
        'name',
        'description',
        'level',
        'template_id',
        'settings',
        'status',
        'created_by'
    ];

    protected $casts = [
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
     * Get the workspace that owns the role
     */
    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    /**
     * Get the role permissions
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions', 'role_id', 'permission_id')
                    ->withTimestamps();
    }

    /**
     * Get users with this role
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles', 'role_id', 'user_id')
                    ->withPivot(['assigned_at', 'assigned_by', 'status'])
                    ->withTimestamps();
    }

    /**
     * Get the user who created the role
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the role template
     */
    public function template()
    {
        return $this->belongsTo(RoleTemplate::class, 'template_id');
    }

    /**
     * Check if role is active
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Check if role has permission
     */
    public function hasPermission($permission)
    {
        if (is_string($permission)) {
            return $this->permissions()->where('name', $permission)->exists();
        }
        
        return $this->permissions()->where('id', $permission->id)->exists();
    }

    /**
     * Grant permission to role
     */
    public function grantPermission($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->first();
        }
        
        if ($permission && !$this->hasPermission($permission)) {
            $this->permissions()->attach($permission->id);
        }
    }

    /**
     * Revoke permission from role
     */
    public function revokePermission($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->first();
        }
        
        if ($permission && $this->hasPermission($permission)) {
            $this->permissions()->detach($permission->id);
        }
    }

    /**
     * Sync permissions
     */
    public function syncPermissions(array $permissions)
    {
        $permissionIds = [];
        
        foreach ($permissions as $permission) {
            if (is_string($permission)) {
                $perm = Permission::where('name', $permission)->first();
                if ($perm) {
                    $permissionIds[] = $perm->id;
                }
            } else {
                $permissionIds[] = $permission->id;
            }
        }
        
        $this->permissions()->sync($permissionIds);
    }

    /**
     * Get role hierarchy level
     */
    public function getHierarchyLevel()
    {
        return $this->level;
    }

    /**
     * Check if role is higher than another role
     */
    public function isHigherThan(Role $role)
    {
        return $this->level > $role->level;
    }

    /**
     * Check if role is lower than another role
     */
    public function isLowerThan(Role $role)
    {
        return $this->level < $role->level;
    }

    /**
     * Get role capabilities
     */
    public function getCapabilities()
    {
        return $this->permissions()->pluck('name')->toArray();
    }

    /**
     * Check if role can perform action
     */
    public function can($action)
    {
        return $this->hasPermission($action);
    }

    /**
     * Get users count with this role
     */
    public function getUsersCount()
    {
        return $this->users()->count();
    }
}