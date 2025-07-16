<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkspaceFeature extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspace_id',
        'feature_id',
        'is_enabled',
        'configuration',
        'enabled_at',
        'disabled_at',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'configuration' => 'array',
        'enabled_at' => 'datetime',
        'disabled_at' => 'datetime',
    ];

    /**
     * Get the workspace that owns this feature
     */
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    /**
     * Get the feature
     */
    public function feature(): BelongsTo
    {
        return $this->belongsTo(Feature::class);
    }

    /**
     * Enable the feature
     */
    public function enable()
    {
        $this->update([
            'is_enabled' => true,
            'enabled_at' => now(),
            'disabled_at' => null,
        ]);
    }

    /**
     * Disable the feature
     */
    public function disable()
    {
        $this->update([
            'is_enabled' => false,
            'disabled_at' => now(),
        ]);
    }

    /**
     * Update feature configuration
     */
    public function updateConfiguration(array $configuration)
    {
        $this->update([
            'configuration' => array_merge($this->configuration ?? [], $configuration),
        ]);
    }

    /**
     * Get configuration value
     */
    public function getConfiguration(string $key, $default = null)
    {
        return data_get($this->configuration, $key, $default);
    }

    /**
     * Set configuration value
     */
    public function setConfiguration(string $key, $value)
    {
        $configuration = $this->configuration ?? [];
        data_set($configuration, $key, $value);
        $this->update(['configuration' => $configuration]);
    }

    /**
     * Check if feature is currently enabled
     */
    public function isEnabled(): bool
    {
        return $this->is_enabled;
    }

    /**
     * Scope to get enabled features
     */
    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    /**
     * Scope to get disabled features
     */
    public function scopeDisabled($query)
    {
        return $query->where('is_enabled', false);
    }
}