<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Workspace extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'description',
        'logo_url',
        'brand_color',
        'settings',
        'subscription_plan_id',
        'subscription_status',
        'trial_ends_at',
    ];

    protected $casts = [
        'settings' => 'array',
        'trial_ends_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Workspace $workspace) {
            $workspace->uuid = Str::uuid();
            $workspace->slug = Str::slug($workspace->name);
        });
    }

    /**
     * Get the users that belong to this workspace.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'workspace_users')
            ->withPivot(['role', 'permissions', 'invited_at', 'joined_at'])
            ->withTimestamps();
    }

    /**
     * Get the goals enabled for this workspace.
     */
    public function goals(): BelongsToMany
    {
        return $this->belongsToMany(Goal::class, 'workspace_goals', 'workspace_id', 'goal_key', 'id', 'key')
            ->withPivot(['is_enabled', 'settings'])
            ->withTimestamps();
    }

    /**
     * Get the features enabled for this workspace.
     */
    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 'workspace_features', 'workspace_id', 'feature_key', 'id', 'key')
            ->withPivot(['is_enabled', 'quota_limit', 'usage_count', 'last_used_at'])
            ->withTimestamps();
    }

    /**
     * Get the subscription plan for this workspace.
     */
    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    /**
     * Get the subscription for this workspace.
     */
    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class);
    }

    /**
     * Check if this workspace has a specific feature enabled.
     */
    public function hasFeature(string $featureKey): bool
    {
        return $this->features()
            ->where('key', $featureKey)
            ->where('is_enabled', true)
            ->exists();
    }

    /**
     * Check if this workspace has reached the quota limit for a feature.
     */
    public function hasReachedQuotaLimit(string $featureKey): bool
    {
        $feature = $this->features()
            ->where('key', $featureKey)
            ->first();

        if (!$feature || !$feature->pivot->quota_limit) {
            return false;
        }

        return $feature->pivot->usage_count >= $feature->pivot->quota_limit;
    }

    /**
     * Get the remaining quota for a feature.
     */
    public function getRemainingQuota(string $featureKey): int
    {
        $feature = $this->features()
            ->where('key', $featureKey)
            ->first();

        if (!$feature || !$feature->pivot->quota_limit) {
            return PHP_INT_MAX;
        }

        return max(0, $feature->pivot->quota_limit - $feature->pivot->usage_count);
    }

    /**
     * Increment the usage count for a feature.
     */
    public function incrementFeatureUsage(string $featureKey, int $amount = 1): bool
    {
        $feature = $this->features()
            ->where('key', $featureKey)
            ->first();

        if (!$feature) {
            return false;
        }

        $this->features()->updateExistingPivot($featureKey, [
            'usage_count' => $feature->pivot->usage_count + $amount,
            'last_used_at' => now(),
        ]);

        return true;
    }

    /**
     * Reset the usage count for a feature.
     */
    public function resetFeatureUsage(string $featureKey): bool
    {
        $feature = $this->features()
            ->where('key', $featureKey)
            ->first();

        if (!$feature) {
            return false;
        }

        $this->features()->updateExistingPivot($featureKey, [
            'usage_count' => 0,
        ]);

        return true;
    }

    /**
     * Enable a feature for this workspace.
     */
    public function enableFeature(string $featureKey, ?int $quotaLimit = null): void
    {
        $this->features()->syncWithoutDetaching([
            $featureKey => [
                'is_enabled' => true,
                'quota_limit' => $quotaLimit,
                'usage_count' => 0,
                'last_used_at' => null,
            ]
        ]);
    }

    /**
     * Disable a feature for this workspace.
     */
    public function disableFeature(string $featureKey): void
    {
        $this->features()->updateExistingPivot($featureKey, [
            'is_enabled' => false,
        ]);
    }

    /**
     * Get the workspace owner.
     */
    public function owner(): ?User
    {
        return $this->users()->wherePivot('role', 'owner')->first();
    }

    /**
     * Check if a user is the owner of this workspace.
     */
    public function isOwner(User $user): bool
    {
        return $this->users()
            ->where('user_id', $user->id)
            ->wherePivot('role', 'owner')
            ->exists();
    }

    /**
     * Check if a user is a member of this workspace.
     */
    public function isMember(User $user): bool
    {
        return $this->users()
            ->where('user_id', $user->id)
            ->exists();
    }

    /**
     * Get the workspace statistics.
     */
    public function getStats(): array
    {
        return [
            'active_features' => $this->features()->where('is_enabled', true)->count(),
            'total_posts' => 0, // This would be calculated based on actual posts
            'monthly_revenue' => '$0', // This would be calculated based on actual revenue
            'users_count' => $this->users()->count(),
            'goals_count' => $this->goals()->where('is_enabled', true)->count(),
        ];
    }

    /**
     * Check if the workspace is on trial.
     */
    public function isOnTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    /**
     * Check if the workspace trial has expired.
     */
    public function trialExpired(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isPast();
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}