<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'price', 'currency', 'billing_cycle',
        'trial_days', 'is_popular', 'is_featured', 'status', 'features',
        'limits', 'restrictions', 'pricing_tiers', 'geographic_pricing',
        'deprecated_at', 'sort_order'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'trial_days' => 'integer',
        'is_popular' => 'boolean',
        'is_featured' => 'boolean',
        'features' => 'array',
        'limits' => 'array',
        'restrictions' => 'array',
        'pricing_tiers' => 'array',
        'geographic_pricing' => 'array',
        'deprecated_at' => 'datetime',
        'sort_order' => 'integer'
    ];

    public function planFeatures(): BelongsToMany
    {
        return $this->belongsToMany(PlanFeature::class, 'plan_feature_assignments')
                    ->withPivot(['is_enabled', 'limits', 'config'])
                    ->withTimestamps();
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(PlanFeatureAssignment::class, 'plan_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function hasFeature(string $featureSlug): bool
    {
        return $this->planFeatures()->where('slug', $featureSlug)->exists();
    }

    public function getFeatureConfig(string $featureSlug): array
    {
        $assignment = $this->assignments()
                          ->whereHas('feature', function($q) use ($featureSlug) {
                              $q->where('slug', $featureSlug);
                          })
                          ->first();
        
        return $assignment ? ($assignment->config ?? []) : [];
    }

    public function getFeatureLimits(string $featureSlug): array
    {
        $assignment = $this->assignments()
                          ->whereHas('feature', function($q) use ($featureSlug) {
                              $q->where('slug', $featureSlug);
                          })
                          ->first();
        
        return $assignment ? ($assignment->limits ?? []) : [];
    }

    public function getPriceForRegion(string $regionCode = null): float
    {
        if (!$regionCode || !$this->geographic_pricing) {
            return $this->price;
        }

        $geoPricing = $this->geographic_pricing;
        
        if (isset($geoPricing[$regionCode])) {
            return $geoPricing[$regionCode]['price'] ?? $this->price;
        }

        return $this->price;
    }

    public function getFormattedPrice(string $regionCode = null): string
    {
        $price = $this->getPriceForRegion($regionCode);
        $currency = $this->currency;
        
        if ($regionCode && $this->geographic_pricing && isset($this->geographic_pricing[$regionCode])) {
            $currency = $this->geographic_pricing[$regionCode]['currency'] ?? $this->currency;
        }

        return number_format($price, 2) . ' ' . $currency;
    }

    public function canUpgradeTo(SubscriptionPlan $plan): bool
    {
        return $plan->price > $this->price;
    }

    public function canDowngradeTo(SubscriptionPlan $plan): bool
    {
        return $plan->price < $this->price;
    }

    public function getBillingCycleLabel(): string
    {
        $labels = [
            'monthly' => 'Monthly',
            'yearly' => 'Yearly',
            'quarterly' => 'Quarterly',
            'weekly' => 'Weekly',
            'daily' => 'Daily'
        ];

        return $labels[$this->billing_cycle] ?? ucfirst($this->billing_cycle);
    }

    public function getStatusColor(): string
    {
        $colors = [
            'active' => 'success',
            'inactive' => 'secondary',
            'deprecated' => 'warning',
            'beta' => 'info'
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    public function getTotalFeatures(): int
    {
        return $this->assignments()->where('is_enabled', true)->count();
    }

    public function getComparisonData(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'currency' => $this->currency,
            'billing_cycle' => $this->billing_cycle,
            'trial_days' => $this->trial_days,
            'features' => $this->assignments()->with('feature')->get()->map(function($assignment) {
                return [
                    'feature' => $assignment->feature->name,
                    'enabled' => $assignment->is_enabled,
                    'limits' => $assignment->limits,
                    'config' => $assignment->config
                ];
            })
        ];
    }
}