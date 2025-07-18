<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'pricing_type',
        'base_price',
        'feature_price_monthly',
        'feature_price_yearly',
        'max_features',
        'includes_whitelabel',
        'is_active',
        'sort_order',
        'metadata',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'feature_price_monthly' => 'decimal:2',
        'feature_price_yearly' => 'decimal:2',
        'max_features' => 'integer',
        'includes_whitelabel' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'metadata' => 'array',
    ];

    /**
     * Get the features included in this plan.
     */
    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 'plan_features', 'plan_id', 'feature_key', 'id', 'key')
            ->withPivot(['is_included', 'quota_limit', 'overage_price'])
            ->withTimestamps();
    }

    /**
     * Get the subscriptions for this plan.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'plan_id');
    }

    /**
     * Scope to get only active plans.
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
     * Get the default subscription plans.
     */
    public static function getDefaultPlans(): array
    {
        return [
            [
                'name' => 'Free Plan',
                'description' => 'Perfect for getting started',
                'pricing_type' => 'feature_based',
                'base_price' => 0.00,
                'feature_price_monthly' => 0.00,
                'feature_price_yearly' => 0.00,
                'max_features' => 10,
                'includes_whitelabel' => false,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Professional Plan',
                'description' => 'For growing businesses',
                'pricing_type' => 'feature_based',
                'base_price' => 0.00,
                'feature_price_monthly' => 1.00,
                'feature_price_yearly' => 10.00,
                'max_features' => null,
                'includes_whitelabel' => false,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Enterprise Plan',
                'description' => 'For large organizations',
                'pricing_type' => 'feature_based',
                'base_price' => 0.00,
                'feature_price_monthly' => 1.50,
                'feature_price_yearly' => 15.00,
                'max_features' => null,
                'includes_whitelabel' => true,
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];
    }

    /**
     * Check if this plan includes a specific feature.
     */
    public function hasFeature(string $featureKey): bool
    {
        return $this->features()->where('key', $featureKey)->where('is_included', true)->exists();
    }

    /**
     * Get the quota limit for a specific feature.
     */
    public function getFeatureQuotaLimit(string $featureKey): ?int
    {
        $feature = $this->features()->where('key', $featureKey)->first();
        return $feature ? $feature->pivot->quota_limit : null;
    }

    /**
     * Calculate the monthly price for this plan with selected features.
     */
    public function calculateMonthlyPrice(array $featureKeys = []): float
    {
        if ($this->pricing_type === 'flat_monthly') {
            return (float) $this->base_price;
        }

        $basePrice = (float) $this->base_price;
        $featurePrice = (float) $this->feature_price_monthly;
        $featureCount = count($featureKeys);

        return $basePrice + ($featurePrice * $featureCount);
    }

    /**
     * Calculate the yearly price for this plan with selected features.
     */
    public function calculateYearlyPrice(array $featureKeys = []): float
    {
        if ($this->pricing_type === 'flat_monthly') {
            return (float) $this->base_price * 12;
        }

        $basePrice = (float) $this->base_price * 12;
        $featurePrice = (float) $this->feature_price_yearly;
        $featureCount = count($featureKeys);

        return $basePrice + ($featurePrice * $featureCount);
    }

    /**
     * Get the included features for this plan.
     */
    public function getIncludedFeatures(): array
    {
        return $this->features()->where('is_included', true)->pluck('key')->toArray();
    }

    /**
     * Check if this plan is feature-based pricing.
     */
    public function isFeatureBasedPricing(): bool
    {
        return $this->pricing_type === 'feature_based';
    }

    /**
     * Check if this plan is flat monthly pricing.
     */
    public function isFlatMonthlyPricing(): bool
    {
        return $this->pricing_type === 'flat_monthly';
    }

    /**
     * Get the display name for this plan.
     */
    public function getDisplayName(): string
    {
        return $this->name;
    }

    /**
     * Get the plan type badge color.
     */
    public function getBadgeColor(): string
    {
        return match ($this->name) {
            'Free Plan' => 'bg-gray-100 text-gray-800',
            'Professional Plan' => 'bg-blue-100 text-blue-800',
            'Enterprise Plan' => 'bg-purple-100 text-purple-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}