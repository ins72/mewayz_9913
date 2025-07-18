<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkspaceFeature extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspace_id',
        'feature_id',
        'is_active',
        'enabled_at',
        'disabled_at',
        'settings',
        'usage_data'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'enabled_at' => 'datetime',
        'disabled_at' => 'datetime',
        'settings' => 'array',
        'usage_data' => 'array'
    ];

    /**
     * Get the workspace this feature belongs to
     */
    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    /**
     * Get the feature
     */
    public function feature()
    {
        return $this->belongsTo(Feature::class);
    }

    /**
     * Scope for active features
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive features
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Enable this feature
     */
    public function enable()
    {
        $this->update([
            'is_active' => true,
            'enabled_at' => now(),
            'disabled_at' => null
        ]);
    }

    /**
     * Disable this feature
     */
    public function disable()
    {
        $this->update([
            'is_active' => false,
            'disabled_at' => now()
        ]);
    }

    /**
     * Get the usage statistics
     */
    public function getUsageStatsAttribute()
    {
        return $this->usage_data['stats'] ?? [];
    }

    /**
     * Update usage data
     */
    public function updateUsage($data)
    {
        $currentUsage = $this->usage_data ?? [];
        $currentUsage['stats'] = array_merge($currentUsage['stats'] ?? [], $data);
        $currentUsage['last_updated'] = now();

        $this->update(['usage_data' => $currentUsage]);
    }
}