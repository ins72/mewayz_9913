<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class BiometricCredential extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'credential_id',
        'public_key',
        'authenticator_data',
        'client_data_json',
        'attestation_object',
        'device_name',
        'device_type',
        'counter',
        'is_active',
        'last_used_at',
    ];

    protected $casts = [
        'id' => 'string',
        'counter' => 'integer',
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    protected $hidden = [
        'public_key',
        'authenticator_data',
        'client_data_json',
        'attestation_object',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Accessors
    public function getIsRecentlyUsedAttribute(): bool
    {
        return $this->last_used_at && $this->last_used_at->diffInDays(now()) <= 7;
    }

    public function getDeviceInfoAttribute(): string
    {
        return $this->device_name . ' (' . ucfirst($this->device_type) . ')';
    }

    // Business Logic Methods
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    public function recordUsage(): void
    {
        $this->update([
            'counter' => $this->counter + 1,
            'last_used_at' => now(),
        ]);
    }

    public function isExpired(): bool
    {
        // Consider credential expired if not used for 90 days
        return $this->last_used_at && $this->last_used_at->diffInDays(now()) > 90;
    }
}