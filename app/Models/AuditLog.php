<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AuditLog extends Model
{
    protected $table = 'audit_logs';
    
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'user_id',
        'workspace_id',
        'action',
        'resource_type',
        'resource_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'metadata'
    ];
    
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'metadata' => 'array'
    ];
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }
    
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
    
    public function scopeByWorkspace($query, $workspaceId)
    {
        return $query->where('workspace_id', $workspaceId);
    }
    
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }
    
    public function scopeByResourceType($query, $resourceType)
    {
        return $query->where('resource_type', $resourceType);
    }
    
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
    
    public static function logAction(array $data): self
    {
        return self::create([
            'user_id' => $data['user_id'] ?? auth()->id(),
            'workspace_id' => $data['workspace_id'] ?? null,
            'action' => $data['action'],
            'resource_type' => $data['resource_type'],
            'resource_id' => $data['resource_id'] ?? null,
            'old_values' => $data['old_values'] ?? null,
            'new_values' => $data['new_values'] ?? null,
            'ip_address' => $data['ip_address'] ?? request()->ip(),
            'user_agent' => $data['user_agent'] ?? request()->userAgent(),
            'metadata' => $data['metadata'] ?? []
        ]);
    }
    
    public function getChangesAttribute(): array
    {
        $changes = [];
        
        if ($this->old_values && $this->new_values) {
            foreach ($this->new_values as $key => $newValue) {
                $oldValue = $this->old_values[$key] ?? null;
                if ($oldValue !== $newValue) {
                    $changes[$key] = [
                        'old' => $oldValue,
                        'new' => $newValue
                    ];
                }
            }
        }
        
        return $changes;
    }
    
    public function getFormattedActionAttribute(): string
    {
        $actions = [
            'create' => 'Created',
            'update' => 'Updated',
            'delete' => 'Deleted',
            'login' => 'Logged In',
            'logout' => 'Logged Out',
            'view' => 'Viewed',
            'export' => 'Exported',
            'import' => 'Imported'
        ];
        
        return $actions[$this->action] ?? ucfirst($this->action);
    }
    
    public function getFormattedResourceTypeAttribute(): string
    {
        $types = [
            'user' => 'User',
            'workspace' => 'Workspace',
            'team' => 'Team',
            'department' => 'Department',
            'bio_site' => 'Bio Site',
            'product' => 'Product',
            'order' => 'Order',
            'campaign' => 'Campaign'
        ];
        
        return $types[$this->resource_type] ?? ucfirst(str_replace('_', ' ', $this->resource_type));
    }
}