<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'category',
        'goals',
        'monthly_price',
        'yearly_price',
        'is_free',
        'sort_order',
        'metadata',
        'is_active'
    ];

    protected $casts = [
        'goals' => 'array',
        'monthly_price' => 'decimal:2',
        'yearly_price' => 'decimal:2',
        'is_free' => 'boolean',
        'metadata' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * Get the workspace goals this feature belongs to
     */
    public function workspaceGoals()
    {
        return $this->belongsToMany(WorkspaceGoal::class, 'goal_features', 'feature_id', 'goal_id');
    }

    /**
     * Get workspaces that have this feature enabled
     */
    public function workspaces()
    {
        return $this->belongsToMany(Workspace::class, 'workspace_features', 'feature_id', 'workspace_id')
            ->withPivot('is_active', 'enabled_at', 'settings')
            ->withTimestamps();
    }

    /**
     * Scope for active features
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for free features
     */
    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }

    /**
     * Scope for paid features
     */
    public function scopePaid($query)
    {
        return $query->where('is_free', false);
    }

    /**
     * Scope for features by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for features by goal
     */
    public function scopeByGoal($query, $goalId)
    {
        return $query->whereJsonContains('goals', $goalId);
    }

    /**
     * Scope for ordered features
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Get the yearly savings percentage
     */
    public function getYearlySavingsPercentageAttribute()
    {
        if ($this->monthly_price <= 0) {
            return 0;
        }

        $yearlyEquivalent = $this->monthly_price * 12;
        return round((($yearlyEquivalent - $this->yearly_price) / $yearlyEquivalent) * 100);
    }

    /**
     * Get the price for a given billing cycle
     */
    public function getPriceFor($billingCycle)
    {
        return $billingCycle === 'yearly' ? $this->yearly_price : $this->monthly_price;
    }

    /**
     * Check if feature is available for a specific goal
     */
    public function isAvailableForGoal($goalId)
    {
        return in_array($goalId, $this->goals ?? []);
    }

    /**
     * Get the integration requirements from metadata
     */
    public function getIntegrationRequirementsAttribute()
    {
        return $this->metadata['integrations'] ?? [];
    }

    /**
     * Get the feature limits from metadata
     */
    public function getLimitsAttribute()
    {
        return $this->metadata['limits'] ?? [];
    }
}