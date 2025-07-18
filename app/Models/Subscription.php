<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspace_id',
        'plan_id',
        'stripe_subscription_id',
        'status',
        'current_period_start',
        'current_period_end',
        'trial_start',
        'trial_end',
        'cancelled_at',
        'grace_period_ends_at',
        'retry_count',
        'last_payment_failed_at',
        'metadata',
    ];

    protected $casts = [
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'trial_start' => 'datetime',
        'trial_end' => 'datetime',
        'cancelled_at' => 'datetime',
        'grace_period_ends_at' => 'datetime',
        'last_payment_failed_at' => 'datetime',
        'retry_count' => 'integer',
        'metadata' => 'array',
    ];

    /**
     * Get the workspace that owns this subscription.
     */
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    /**
     * Get the subscription plan.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    /**
     * Get the payment failures for this subscription.
     */
    public function paymentFailures(): HasMany
    {
        return $this->hasMany(PaymentFailure::class);
    }

    /**
     * Get the subscription items (for feature-based pricing).
     */
    public function items(): HasMany
    {
        return $this->hasMany(SubscriptionItem::class);
    }

    /**
     * Scope for active subscriptions.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for past due subscriptions.
     */
    public function scopePastDue($query)
    {
        return $query->where('status', 'past_due');
    }

    /**
     * Scope for cancelled subscriptions.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope for trialing subscriptions.
     */
    public function scopeTrialing($query)
    {
        return $query->where('status', 'trialing');
    }

    /**
     * Check if the subscription is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the subscription is past due.
     */
    public function isPastDue(): bool
    {
        return $this->status === 'past_due';
    }

    /**
     * Check if the subscription is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Check if the subscription is on trial.
     */
    public function isTrialing(): bool
    {
        return $this->status === 'trialing' && $this->trial_end && $this->trial_end->isFuture();
    }

    /**
     * Check if the trial has ended.
     */
    public function trialEnded(): bool
    {
        return $this->trial_end && $this->trial_end->isPast();
    }

    /**
     * Check if the subscription is in grace period.
     */
    public function isInGracePeriod(): bool
    {
        return $this->grace_period_ends_at && $this->grace_period_ends_at->isFuture();
    }

    /**
     * Check if the grace period has ended.
     */
    public function gracePeriodEnded(): bool
    {
        return $this->grace_period_ends_at && $this->grace_period_ends_at->isPast();
    }

    /**
     * Get the days remaining in the trial.
     */
    public function daysLeftInTrial(): int
    {
        if (!$this->trial_end) {
            return 0;
        }

        return max(0, $this->trial_end->diffInDays(now()));
    }

    /**
     * Get the days remaining in the grace period.
     */
    public function daysLeftInGracePeriod(): int
    {
        if (!$this->grace_period_ends_at) {
            return 0;
        }

        return max(0, $this->grace_period_ends_at->diffInDays(now()));
    }

    /**
     * Calculate the monthly cost of this subscription.
     */
    public function getMonthlyCost(): float
    {
        if (!$this->plan) {
            return 0.0;
        }

        if ($this->plan->pricing_type === 'flat_monthly') {
            return (float) $this->plan->base_price;
        }

        // For feature-based pricing, sum up all items
        $totalCost = (float) $this->plan->base_price;
        foreach ($this->items as $item) {
            $totalCost += (float) $item->unit_price * $item->quantity;
        }

        return $totalCost;
    }

    /**
     * Calculate the yearly cost of this subscription.
     */
    public function getYearlyCost(): float
    {
        if (!$this->plan) {
            return 0.0;
        }

        if ($this->plan->pricing_type === 'flat_monthly') {
            return (float) $this->plan->base_price * 12;
        }

        // For feature-based pricing with yearly discount
        $monthlyFeatureCost = $this->items->sum(function ($item) {
            return (float) $item->unit_price * $item->quantity;
        });

        $yearlyFeatureCost = $this->items->sum(function ($item) {
            return (float) $this->plan->feature_price_yearly * $item->quantity;
        });

        return ((float) $this->plan->base_price * 12) + $yearlyFeatureCost;
    }

    /**
     * Get the next billing date.
     */
    public function getNextBillingDate(): ?Carbon
    {
        return $this->current_period_end;
    }

    /**
     * Check if subscription can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['active', 'trialing', 'past_due']);
    }

    /**
     * Check if subscription can be resumed.
     */
    public function canBeResumed(): bool
    {
        return $this->status === 'cancelled' && $this->current_period_end && $this->current_period_end->isFuture();
    }

    /**
     * Get the effective features for this subscription.
     */
    public function getEffectiveFeatures(): array
    {
        if (!$this->plan) {
            return [];
        }

        $features = [];
        
        if ($this->plan->pricing_type === 'flat_monthly') {
            // For flat pricing, return all included features
            foreach ($this->plan->features as $feature) {
                if ($feature->pivot->is_included) {
                    $features[$feature->key] = [
                        'is_enabled' => true,
                        'quota_limit' => $feature->pivot->quota_limit,
                        'usage_count' => 0, // This would be tracked separately
                    ];
                }
            }
        } else {
            // For feature-based pricing, return features based on subscription items
            foreach ($this->items as $item) {
                $features[$item->feature_key] = [
                    'is_enabled' => true,
                    'quota_limit' => $item->quota_limit,
                    'usage_count' => 0, // This would be tracked separately
                ];
            }
        }

        return $features;
    }

    /**
     * Get subscription status display name.
     */
    public function getStatusDisplayName(): string
    {
        return match ($this->status) {
            'active' => 'Active',
            'trialing' => 'Trial',
            'past_due' => 'Past Due',
            'cancelled' => 'Cancelled',
            'unpaid' => 'Unpaid',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get subscription status badge color.
     */
    public function getStatusBadgeColor(): string
    {
        return match ($this->status) {
            'active' => 'bg-green-100 text-green-800',
            'trialing' => 'bg-blue-100 text-blue-800',
            'past_due' => 'bg-yellow-100 text-yellow-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'unpaid' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get subscription health status.
     */
    public function getHealthStatus(): string
    {
        if ($this->isActive()) {
            return 'healthy';
        }

        if ($this->isTrialing()) {
            return 'trial';
        }

        if ($this->isPastDue()) {
            if ($this->isInGracePeriod()) {
                return 'grace_period';
            }
            return 'at_risk';
        }

        if ($this->isCancelled()) {
            return 'cancelled';
        }

        return 'unknown';
    }
}