<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class AdminUser extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'permissions', 'restrictions',
        'is_active', 'last_login', 'two_factor_secret', 'two_factor_enabled'
    ];

    protected $hidden = [
        'password', 'remember_token', 'two_factor_secret'
    ];

    protected $casts = [
        'permissions' => 'array',
        'restrictions' => 'array',
        'is_active' => 'boolean',
        'two_factor_enabled' => 'boolean',
        'last_login' => 'datetime'
    ];

    public function activityLogs(): HasMany
    {
        return $this->hasMany(AdminActivityLog::class);
    }

    public function bulkOperations(): HasMany
    {
        return $this->hasMany(BulkOperation::class);
    }

    public function hasPermission(string $permission): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->role === 'super_admin') {
            return true;
        }

        $permissions = $this->permissions ?? [];
        return in_array($permission, $permissions) || in_array('*', $permissions);
    }

    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    public function canAccessModule(string $module): bool
    {
        $modulePermissions = [
            'users' => ['users.view', 'users.create', 'users.edit', 'users.delete'],
            'plans' => ['plans.view', 'plans.create', 'plans.edit', 'plans.delete'],
            'analytics' => ['analytics.view'],
            'settings' => ['settings.view', 'settings.edit'],
            'system' => ['system.view', 'system.edit'],
            'bulk_operations' => ['bulk.view', 'bulk.create', 'bulk.execute'],
            'feature_flags' => ['features.view', 'features.edit'],
            'api_keys' => ['api.view', 'api.create', 'api.edit', 'api.delete']
        ];

        if (!isset($modulePermissions[$module])) {
            return false;
        }

        return $this->hasAnyPermission($modulePermissions[$module]);
    }

    public function logActivity(string $action, string $entityType = null, int $entityId = null, array $oldValues = null, array $newValues = null, string $description = null): void
    {
        $this->activityLogs()->create([
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }

    public function getAvailablePermissions(): array
    {
        return [
            'users' => [
                'users.view' => 'View Users',
                'users.create' => 'Create Users',
                'users.edit' => 'Edit Users',
                'users.delete' => 'Delete Users',
                'users.impersonate' => 'Impersonate Users',
                'users.export' => 'Export Users',
                'users.import' => 'Import Users'
            ],
            'plans' => [
                'plans.view' => 'View Plans',
                'plans.create' => 'Create Plans',
                'plans.edit' => 'Edit Plans',
                'plans.delete' => 'Delete Plans',
                'plans.pricing' => 'Manage Pricing'
            ],
            'analytics' => [
                'analytics.view' => 'View Analytics',
                'analytics.export' => 'Export Analytics'
            ],
            'settings' => [
                'settings.view' => 'View Settings',
                'settings.edit' => 'Edit Settings',
                'settings.env' => 'Manage Environment'
            ],
            'system' => [
                'system.view' => 'View System Info',
                'system.edit' => 'Edit System Settings',
                'system.maintenance' => 'Maintenance Mode',
                'system.backup' => 'Backup System'
            ],
            'bulk_operations' => [
                'bulk.view' => 'View Bulk Operations',
                'bulk.create' => 'Create Bulk Operations',
                'bulk.execute' => 'Execute Bulk Operations'
            ],
            'feature_flags' => [
                'features.view' => 'View Feature Flags',
                'features.edit' => 'Edit Feature Flags'
            ],
            'api_keys' => [
                'api.view' => 'View API Keys',
                'api.create' => 'Create API Keys',
                'api.edit' => 'Edit API Keys',
                'api.delete' => 'Delete API Keys'
            ]
        ];
    }
}