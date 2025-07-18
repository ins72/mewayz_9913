<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkspaceSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspace_id',
        'subscription_plan_id',
        'status',
        'billing_cycle',
        'base_price',
        'feature_price',
        'total_price',
        'feature_count',
        'enabled_features',
        'transaction_fees',
        'limits',
        'metadata',
        'current_period_start',
        'current_period_end',
        'trial_start',
        'trial_end',
        'cancelled_at',
        'stripe_subscription_id',
        'stripe_customer_id'
    ];

    protected $casts = [
        'enabled_features' => 'array',
        'transaction_fees' => 'array',
        'limits' => 'array',
        'metadata' => 'array',
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'trial_start' => 'datetime',
        'trial_end' => 'datetime',
        'cancelled_at' => 'datetime',
        'base_price' => 'decimal:2',
        'feature_price' => 'decimal:2',
        'total_price' => 'decimal:2'
    ];

    /**
     * Get the workspace this subscription belongs to
     */
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    /**
     * Get the subscription plan
     */
    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    /**
     * Get the transaction fees for this subscription
     */
    public function transactionFees(): HasMany
    {
        return $this->hasMany(TransactionFee::class, 'workspace_id', 'workspace_id');
    }

    /**
     * Get the features enabled for this subscription
     */
    public function features()
    {
        return Feature::whereIn('id', $this->enabled_features ?? [])
            ->orderBy('category')
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Check if subscription is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && 
               ($this->current_period_end === null || $this->current_period_end > now());
    }

    /**
     * Check if subscription is in trial
     */
    public function isInTrial(): bool
    {
        return $this->trial_end && $this->trial_end > now();
    }

    /**
     * Get transaction fee percentage for a specific transaction type
     */
    public function getTransactionFeePercentage(string $transactionType): float
    {
        $fees = $this->transaction_fees ?? [];
        return $fees[$transactionType] ?? $this->getDefaultTransactionFee($transactionType);
    }

    /**
     * Get default transaction fee based on subscription plan
     */
    private function getDefaultTransactionFee(string $transactionType): float
    {
        $planSlug = $this->subscriptionPlan->slug ?? 'free';
        
        $defaultFees = [
            'free' => [
                'escrow' => 5.0,
                'ecommerce' => 4.0,
                'booking' => 3.5,
                'course' => 3.0,
                'marketplace' => 5.0,
                'payment_processing' => 2.9
            ],
            'professional' => [
                'escrow' => 3.0,
                'ecommerce' => 2.5,
                'booking' => 2.0,
                'course' => 1.5,
                'marketplace' => 3.0,
                'payment_processing' => 2.9
            ],
            'enterprise' => [
                'escrow' => 1.5,
                'ecommerce' => 1.0,
                'booking' => 0.5,
                'course' => 0.5,
                'marketplace' => 1.5,
                'payment_processing' => 2.9
            ]
        ];

        return $defaultFees[$planSlug][$transactionType] ?? 2.9;
    }

    /**
     * Calculate total monthly cost
     */
    public function calculateMonthlyCost(): float
    {
        $features = $this->features();
        $featureCount = $features->where('is_free', false)->count();
        
        if ($this->subscriptionPlan->slug === 'free') {
            return 0.00;
        }
        
        $featurePrice = $this->billing_cycle === 'yearly' ? 
            $this->subscriptionPlan->feature_price_yearly / 12 : 
            $this->subscriptionPlan->feature_price_monthly;
            
        return $this->subscriptionPlan->base_price + ($featureCount * $featurePrice);
    }

    /**
     * Get usage limits for this subscription
     */
    public function getUsageLimit(string $limitType): ?int
    {
        $limits = $this->limits ?? [];
        return $limits[$limitType] ?? $this->getDefaultUsageLimit($limitType);
    }

    /**
     * Get default usage limits based on subscription plan
     */
    private function getDefaultUsageLimit(string $limitType): ?int
    {
        $planSlug = $this->subscriptionPlan->slug ?? 'free';
        
        $defaultLimits = [
            'free' => [
                'features' => 10,
                'team_members' => 3,
                'storage_gb' => 1,
                'bandwidth_gb' => 10,
                'api_calls_per_month' => 1000,
                'email_sends_per_month' => 100
            ],
            'professional' => [
                'features' => null, // unlimited
                'team_members' => 25,
                'storage_gb' => 100,
                'bandwidth_gb' => 1000,
                'api_calls_per_month' => 50000,
                'email_sends_per_month' => 10000
            ],
            'enterprise' => [
                'features' => null, // unlimited
                'team_members' => null, // unlimited
                'storage_gb' => null, // unlimited
                'bandwidth_gb' => null, // unlimited
                'api_calls_per_month' => null, // unlimited
                'email_sends_per_month' => null // unlimited
            ]
        ];

        return $defaultLimits[$planSlug][$limitType] ?? null;
    }

    /**
     * Enable a feature for this subscription
     */
    public function enableFeature(int $featureId): void
    {
        $enabledFeatures = $this->enabled_features ?? [];
        
        if (!in_array($featureId, $enabledFeatures)) {
            $enabledFeatures[] = $featureId;
            $this->update([
                'enabled_features' => $enabledFeatures,
                'feature_count' => count($enabledFeatures)
            ]);
        }
    }

    /**
     * Disable a feature for this subscription
     */
    public function disableFeature(int $featureId): void
    {
        $enabledFeatures = $this->enabled_features ?? [];
        
        if (($key = array_search($featureId, $enabledFeatures)) !== false) {
            unset($enabledFeatures[$key]);
            $enabledFeatures = array_values($enabledFeatures);
            
            $this->update([
                'enabled_features' => $enabledFeatures,
                'feature_count' => count($enabledFeatures)
            ]);
        }
    }

    /**
     * Check if a feature is enabled
     */
    public function hasFeature(int $featureId): bool
    {
        return in_array($featureId, $this->enabled_features ?? []);
    }

    /**
     * Update transaction fees for this subscription
     */
    public function updateTransactionFees(array $fees): void
    {
        $this->update(['transaction_fees' => $fees]);
    }

    /**
     * Scope for active subscriptions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for trial subscriptions
     */
    public function scopeTrial($query)
    {
        return $query->whereNotNull('trial_end')
            ->where('trial_end', '>', now());
    }

    /**
     * Scope for expired subscriptions
     */
    public function scopeExpired($query)
    {
        return $query->where('current_period_end', '<', now());
    }
}