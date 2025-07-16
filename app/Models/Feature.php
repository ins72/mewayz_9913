<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'is_active',
        'is_free',
        'sort_order',
        'dependencies',
        'metadata',
    ];

    protected $casts = [
        'goals' => 'array',
        'is_active' => 'boolean',
        'is_free' => 'boolean',
        'dependencies' => 'array',
        'metadata' => 'array',
        'monthly_price' => 'decimal:2',
        'yearly_price' => 'decimal:2',
    ];

    /**
     * Get workspaces that have this feature enabled
     */
    public function workspaces(): BelongsToMany
    {
        return $this->belongsToMany(Workspace::class, 'workspace_features')
                    ->withPivot('is_enabled', 'configuration', 'enabled_at', 'disabled_at')
                    ->withTimestamps();
    }

    /**
     * Get workspace features pivot records
     */
    public function workspaceFeatures(): HasMany
    {
        return $this->hasMany(WorkspaceFeature::class);
    }

    /**
     * Check if feature supports a specific goal
     */
    public function supportsGoal(string $goalSlug): bool
    {
        return in_array($goalSlug, $this->goals ?? []);
    }

    /**
     * Get features that this feature depends on
     */
    public function getDependentFeatures()
    {
        if (empty($this->dependencies)) {
            return collect([]);
        }

        return static::whereIn('id', $this->dependencies)->get();
    }

    /**
     * Get features that depend on this feature
     */
    public function getDependsOnFeatures()
    {
        return static::whereJsonContains('dependencies', $this->id)->get();
    }

    /**
     * Scope to get active features
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get features by category
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to get features by goal
     */
    public function scopeByGoal($query, string $goalSlug)
    {
        return $query->whereJsonContains('goals', $goalSlug);
    }

    /**
     * Scope to get free features
     */
    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }

    /**
     * Get formatted price for display
     */
    public function getFormattedPrice(string $interval = 'monthly'): string
    {
        $price = $interval === 'yearly' ? $this->yearly_price : $this->monthly_price;
        return '$' . number_format($price, 2);
    }

    /**
     * Get price for specific interval
     */
    public function getPrice(string $interval = 'monthly'): float
    {
        return $interval === 'yearly' ? $this->yearly_price : $this->monthly_price;
    }
}