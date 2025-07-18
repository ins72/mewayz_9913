<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BulkOperation extends Model
{
    protected $fillable = [
        'admin_user_id', 'operation_type', 'entity_type', 'parameters', 'filters',
        'status', 'total_records', 'processed_records', 'success_records', 'failed_records',
        'results', 'errors', 'started_at', 'completed_at'
    ];

    protected $casts = [
        'parameters' => 'array',
        'filters' => 'array',
        'results' => 'array',
        'errors' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByAdminUser($query, int $adminUserId)
    {
        return $query->where('admin_user_id', $adminUserId);
    }

    public function scopeByEntityType($query, string $entityType)
    {
        return $query->where('entity_type', $entityType);
    }

    public function getProgressPercentage(): float
    {
        if ($this->total_records === 0) {
            return 0;
        }

        return ($this->processed_records / $this->total_records) * 100;
    }

    public function getSuccessRate(): float
    {
        if ($this->processed_records === 0) {
            return 0;
        }

        return ($this->success_records / $this->processed_records) * 100;
    }

    public function getDuration(): ?int
    {
        if (!$this->started_at || !$this->completed_at) {
            return null;
        }

        return $this->completed_at->diffInSeconds($this->started_at);
    }

    public function isCompleted(): bool
    {
        return in_array($this->status, ['completed', 'failed']);
    }

    public function isRunning(): bool
    {
        return $this->status === 'processing';
    }

    public function canRetry(): bool
    {
        return $this->status === 'failed' && $this->failed_records > 0;
    }
}