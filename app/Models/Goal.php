<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'description',
        'icon',
        'color',
        'category',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the features associated with this goal.
     */
    public function features(): HasMany
    {
        return $this->hasMany(Feature::class, 'goal_key', 'key');
    }

    /**
     * Get the workspaces that have enabled this goal.
     */
    public function workspaces(): BelongsToMany
    {
        return $this->belongsToMany(Workspace::class, 'workspace_goals', 'goal_key', 'workspace_id', 'key')
            ->withPivot(['is_enabled', 'settings'])
            ->withTimestamps();
    }

    /**
     * Scope to get only active goals.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Get the default goals for the system.
     */
    public static function getDefaultGoals(): array
    {
        return [
            [
                'key' => 'instagram',
                'name' => 'Instagram Management',
                'description' => 'Manage Instagram accounts, posts, and analytics',
                'icon' => 'instagram',
                'color' => '#E4405F',
                'category' => 'social_media',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'key' => 'link_bio',
                'name' => 'Link in Bio',
                'description' => 'Create professional bio pages and landing pages',
                'icon' => 'link',
                'color' => '#00D4AA',
                'category' => 'websites',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'key' => 'courses',
                'name' => 'Course Creation',
                'description' => 'Build and sell online courses',
                'icon' => 'academic-cap',
                'color' => '#F59E0B',
                'category' => 'education',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'key' => 'ecommerce',
                'name' => 'E-commerce',
                'description' => 'Sell products and manage orders',
                'icon' => 'shopping-cart',
                'color' => '#8B5CF6',
                'category' => 'commerce',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'key' => 'crm',
                'name' => 'CRM & Email Marketing',
                'description' => 'Manage contacts and email campaigns',
                'icon' => 'users',
                'color' => '#EF4444',
                'category' => 'marketing',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'key' => 'website',
                'name' => 'Website Builder',
                'description' => 'Create professional websites with drag-and-drop',
                'icon' => 'globe',
                'color' => '#3B82F6',
                'category' => 'websites',
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'key' => 'analytics',
                'name' => 'Advanced Analytics',
                'description' => 'Comprehensive reporting and insights',
                'icon' => 'chart-bar',
                'color' => '#10B981',
                'category' => 'analytics',
                'sort_order' => 7,
                'is_active' => true,
            ],
            [
                'key' => 'ai_tools',
                'name' => 'AI-Powered Tools',
                'description' => 'Content generation and optimization',
                'icon' => 'sparkles',
                'color' => '#F97316',
                'category' => 'ai',
                'sort_order' => 8,
                'is_active' => true,
            ],
        ];
    }

    /**
     * Get the count of active features for this goal.
     */
    public function getActiveFeatureCountAttribute(): int
    {
        return $this->features()->where('is_active', true)->count();
    }

    /**
     * Check if this goal has a specific feature.
     */
    public function hasFeature(string $featureKey): bool
    {
        return $this->features()->where('key', $featureKey)->exists();
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'key';
    }
}