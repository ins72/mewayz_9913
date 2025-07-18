<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkspaceGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'sort_order',
        'metadata',
        'is_active'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * Get the features related to this goal
     */
    public function features()
    {
        return $this->belongsToMany(Feature::class, 'goal_features', 'goal_id', 'feature_id');
    }

    /**
     * Get workspaces that have selected this goal
     */
    public function workspaces()
    {
        return $this->belongsToMany(Workspace::class, 'workspace_goal_selections', 'goal_id', 'workspace_id');
    }

    /**
     * Scope for active goals
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered goals
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Get the features count for this goal
     */
    public function getFeaturesCountAttribute()
    {
        return $this->features()->count();
    }

    /**
     * Get the integrations from metadata
     */
    public function getIntegrationsAttribute()
    {
        return $this->metadata['integrations'] ?? [];
    }

    /**
     * Get the goal features from metadata
     */
    public function getGoalFeaturesAttribute()
    {
        return $this->metadata['features'] ?? [];
    }
}