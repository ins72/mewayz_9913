<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'base_price',
        'feature_price_monthly',
        'feature_price_yearly',
        'max_features',
        'has_branding',
        'has_priority_support',
        'has_custom_domain',
        'has_api_access',
        'included_features',
        'metadata',
        'is_active',
        // New admin dashboard fields
        'price_monthly',
        'price_yearly',
        'feature_limit',
        'is_whitelabel',
        'features',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'feature_price_monthly' => 'decimal:2',
        'feature_price_yearly' => 'decimal:2',
        'has_branding' => 'boolean',
        'has_priority_support' => 'boolean',
        'has_custom_domain' => 'boolean',
        'has_api_access' => 'boolean',
        'included_features' => 'array',
        'metadata' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get workspaces using this plan
     */
    public function workspaces(): HasMany
    {
        return $this->hasMany(Workspace::class);
    }

    /**
     * Get features included for free in this plan
     */
    public function getIncludedFeatures()
    {
        if (empty($this->included_features)) {
            return collect([]);
        }

        return Feature::whereIn('id', $this->included_features)->get();
    }

    /**
     * Calculate total price for given features
     */
    public function calculatePrice(array $featureIds, string $interval = 'monthly'): float
    {
        $includedFeatures = $this->included_features ?? [];
        $paidFeatures = array_diff($featureIds, $includedFeatures);
        
        $featurePrice = $interval === 'yearly' ? $this->feature_price_yearly : $this->feature_price_monthly;
        
        return $this->base_price + (count($paidFeatures) * $featurePrice);
    }

    /**
     * Calculate monthly price for given features
     */
    public function calculateMonthlyPrice(array $featureIds): float
    {
        return $this->calculatePrice($featureIds, 'monthly');
    }

    /**
     * Calculate yearly price for given features
     */
    public function calculateYearlyPrice(array $featureIds): float
    {
        return $this->calculatePrice($featureIds, 'yearly');
    }

    /**
     * Check if plan includes specific feature
     */
    public function includesFeature(int $featureId): bool
    {
        return in_array($featureId, $this->included_features ?? []);
    }

    /**
     * Check if plan has feature limit
     */
    public function hasFeatureLimit(): bool
    {
        return $this->max_features !== null;
    }

    /**
     * Get maximum number of features allowed
     */
    public function getMaxFeatures(): ?int
    {
        return $this->max_features;
    }

    /**
     * Check if plan allows given number of features
     */
    public function allowsFeatureCount(int $count): bool
    {
        return $this->max_features === null || $count <= $this->max_features;
    }

    /**
     * Get formatted feature price
     */
    public function getFormattedFeaturePrice(string $interval = 'monthly'): string
    {
        $price = $interval === 'yearly' ? $this->feature_price_yearly : $this->feature_price_monthly;
        return '$' . number_format($price, 2);
    }

    /**
     * Get formatted base price
     */
    public function getFormattedBasePrice(): string
    {
        return '$' . number_format($this->base_price, 2);
    }

    /**
     * Check if this is a free plan
     */
    public function isFree(): bool
    {
        return $this->type === 'free';
    }

    /**
     * Check if this is a professional plan
     */
    public function isProfessional(): bool
    {
        return $this->type === 'professional';
    }

    /**
     * Check if this is an enterprise plan
     */
    public function isEnterprise(): bool
    {
        return $this->type === 'enterprise';
    }

    /**
     * Scope to get active plans
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get plans by type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }
}