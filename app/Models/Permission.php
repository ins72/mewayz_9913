<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Permission extends Model
{
    protected $table = 'permissions';
    
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'category',
        'resource',
        'action',
        'conditions',
        'settings'
    ];

    protected $casts = [
        'conditions' => 'array',
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
     * Get roles that have this permission
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions', 'permission_id', 'role_id')
                    ->withTimestamps();
    }

    /**
     * Get users that have this permission (through roles)
     */
    public function users()
    {
        return $this->hasManyThrough(User::class, Role::class, 'id', 'id', 'id', 'id')
                    ->join('role_permissions', 'roles.id', '=', 'role_permissions.role_id')
                    ->join('user_roles', 'roles.id', '=', 'user_roles.role_id')
                    ->where('role_permissions.permission_id', $this->id);
    }

    /**
     * Get permission category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Get permission resource
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Get permission action
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Check if permission has conditions
     */
    public function hasConditions()
    {
        return !empty($this->conditions);
    }

    /**
     * Get permission conditions
     */
    public function getConditions()
    {
        return $this->conditions ?? [];
    }

    /**
     * Check if permission matches criteria
     */
    public function matches($resource, $action)
    {
        return $this->resource === $resource && $this->action === $action;
    }

    /**
     * Get permission scope
     */
    public function getScope()
    {
        return $this->settings['scope'] ?? 'workspace';
    }

    /**
     * Check if permission is global
     */
    public function isGlobal()
    {
        return $this->getScope() === 'global';
    }

    /**
     * Check if permission is workspace-specific
     */
    public function isWorkspaceSpecific()
    {
        return $this->getScope() === 'workspace';
    }

    /**
     * Get permission weight/priority
     */
    public function getWeight()
    {
        return $this->settings['weight'] ?? 0;
    }

    /**
     * Get permission dependencies
     */
    public function getDependencies()
    {
        return $this->settings['dependencies'] ?? [];
    }

    /**
     * Check if permission has dependencies
     */
    public function hasDependencies()
    {
        return !empty($this->getDependencies());
    }

    /**
     * Get permission conflicts
     */
    public function getConflicts()
    {
        return $this->settings['conflicts'] ?? [];
    }

    /**
     * Check if permission has conflicts
     */
    public function hasConflicts()
    {
        return !empty($this->getConflicts());
    }

    /**
     * Get display name
     */
    public function getDisplayName()
    {
        return $this->display_name ?? $this->name;
    }

    /**
     * Get full permission name
     */
    public function getFullName()
    {
        return $this->resource . '.' . $this->action;
    }

    /**
     * Create permission from resource and action
     */
    public static function createFromResourceAction($resource, $action, $options = [])
    {
        return self::create([
            'name' => $resource . '.' . $action,
            'display_name' => $options['display_name'] ?? ucfirst($action) . ' ' . ucfirst($resource),
            'description' => $options['description'] ?? "Allow {$action} on {$resource}",
            'category' => $options['category'] ?? 'general',
            'resource' => $resource,
            'action' => $action,
            'conditions' => $options['conditions'] ?? [],
            'settings' => $options['settings'] ?? []
        ]);
    }

    /**
     * Get permissions by category
     */
    public static function getByCategory($category)
    {
        return self::where('category', $category)->get();
    }

    /**
     * Get permissions by resource
     */
    public static function getByResource($resource)
    {
        return self::where('resource', $resource)->get();
    }

    /**
     * Get system permissions
     */
    public static function getSystemPermissions()
    {
        return self::where('category', 'system')->get();
    }

    /**
     * Get workspace permissions
     */
    public static function getWorkspacePermissions()
    {
        return self::where('category', 'workspace')->get();
    }

    /**
     * Get user permissions
     */
    public static function getUserPermissions()
    {
        return self::where('category', 'user')->get();
    }
}