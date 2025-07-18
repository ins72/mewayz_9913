<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class UserSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'stripe_subscription_id',
        'status',
        'amount',
        'billing_cycle',
        'current_period_start',
        'current_period_end',
        'trial_ends_at',
        'next_billing_date',
        'cancel_at_period_end',
        'canceled_at',
        'default_payment_method_id',
        'metadata'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'trial_ends_at' => 'datetime',
        'next_billing_date' => 'datetime',
        'canceled_at' => 'datetime',
        'cancel_at_period_end' => 'boolean',
        'metadata' => 'array'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function defaultPaymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'default_payment_method_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'subscription_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'subscription_id');
    }

    public function addons(): BelongsToMany
    {
        return $this->belongsToMany(SubscriptionAddon::class, 'user_subscription_addons', 'subscription_id', 'addon_id')
            ->withPivot('quantity', 'price')
            ->withTimestamps();
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(AffiliateCommission::class, 'subscription_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCanceled($query)
    {
        return $query->where('status', 'canceled');
    }

    public function scopeTrialing($query)
    {
        return $query->where('status', 'trialing');
    }

    public function scopePastDue($query)
    {
        return $query->where('status', 'past_due');
    }

    // Accessors & Mutators
    public function getIsActiveAttribute()
    {
        return $this->status === 'active';
    }

    public function getIsTrialingAttribute()
    {
        return $this->status === 'trialing' || ($this->trial_ends_at && $this->trial_ends_at->isFuture());
    }

    public function getIsCanceledAttribute()
    {
        return $this->status === 'canceled';
    }

    public function getIsExpiredAttribute()
    {
        return $this->status === 'expired' || ($this->current_period_end && $this->current_period_end->isPast());
    }

    public function getDaysUntilRenewalAttribute()
    {
        if (!$this->next_billing_date) {
            return null;
        }

        return $this->next_billing_date->diffInDays(now());
    }

    public function getTrialDaysRemainingAttribute()
    {
        if (!$this->trial_ends_at || $this->trial_ends_at->isPast()) {
            return 0;
        }

        return $this->trial_ends_at->diffInDays(now());
    }

    public function getTotalAddonsAmountAttribute()
    {
        return $this->addons->sum(function ($addon) {
            return $addon->pivot->price * $addon->pivot->quantity;
        });
    }

    public function getMonthlyAmountAttribute()
    {
        $baseAmount = $this->amount;
        $addonsAmount = $this->total_addons_amount;

        if ($this->billing_cycle === 'yearly') {
            $baseAmount = $baseAmount / 12;
        }

        return $baseAmount + $addonsAmount;
    }

    // Methods
    public function canUpgrade()
    {
        return $this->is_active && !$this->cancel_at_period_end;
    }

    public function canDowngrade()
    {
        return $this->is_active && !$this->cancel_at_period_end;
    }

    public function canReactivate()
    {
        return $this->cancel_at_period_end && !$this->current_period_end->isPast();
    }

    public function getRemainingUsage($feature)
    {
        $limits = $this->plan->feature_limits ?? [];
        $used = $this->user->getUsageForFeature($feature);

        return max(0, ($limits[$feature] ?? 0) - $used);
    }

    public function hasFeature($feature)
    {
        return in_array($feature, $this->plan->features ?? []);
    }

    public function isFeatureLimited($feature)
    {
        $limits = $this->plan->feature_limits ?? [];
        return isset($limits[$feature]);
    }

    public function getFeatureLimit($feature)
    {
        $limits = $this->plan->feature_limits ?? [];
        return $limits[$feature] ?? null;
    }

    public function hasExceededLimit($feature)
    {
        if (!$this->isFeatureLimited($feature)) {
            return false;
        }

        $limit = $this->getFeatureLimit($feature);
        $used = $this->user->getUsageForFeature($feature);

        return $used >= $limit;
    }

    public function getUsagePercentage($feature)
    {
        if (!$this->isFeatureLimited($feature)) {
            return 0;
        }

        $limit = $this->getFeatureLimit($feature);
        $used = $this->user->getUsageForFeature($feature);

        if ($limit == 0) {
            return 0;
        }

        return min(100, ($used / $limit) * 100);
    }

    public function calculateProrationCredit($newPlan)
    {
        $daysRemaining = $this->current_period_end->diffInDays(now());
        $totalDays = $this->current_period_start->diffInDays($this->current_period_end);

        if ($totalDays == 0) {
            return 0;
        }

        $currentPlanDailyRate = $this->amount / $totalDays;
        $newPlanDailyRate = $newPlan->price / $totalDays;

        return ($currentPlanDailyRate - $newPlanDailyRate) * $daysRemaining;
    }

    public function generateInvoicePreview($changes = [])
    {
        $amount = $changes['amount'] ?? $this->amount;
        $addons = $changes['addons'] ?? $this->addons;

        $subtotal = $amount;
        foreach ($addons as $addon) {
            $subtotal += $addon->price * ($addon->pivot->quantity ?? 1);
        }

        $taxRate = $this->user->getTaxRate();
        $tax = $subtotal * $taxRate;

        return [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $subtotal + $tax,
            'next_billing_date' => $this->next_billing_date,
            'proration_credit' => $changes['proration_credit'] ?? 0
        ];
    }

    public function getSubscriptionHealth()
    {
        $score = 100;
        $issues = [];

        // Check payment failures
        $recentFailures = $this->transactions()
            ->where('status', 'failed')
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        if ($recentFailures > 0) {
            $score -= $recentFailures * 10;
            $issues[] = "Recent payment failures: {$recentFailures}";
        }

        // Check usage patterns
        $user = $this->user;
        $lastActivity = $user->last_login_at;

        if (!$lastActivity || $lastActivity->diffInDays(now()) > 30) {
            $score -= 20;
            $issues[] = 'Low user engagement';
        }

        // Check cancellation risk
        if ($this->cancel_at_period_end) {
            $score -= 30;
            $issues[] = 'Scheduled for cancellation';
        }

        return [
            'score' => max(0, $score),
            'status' => $score >= 80 ? 'healthy' : ($score >= 60 ? 'at_risk' : 'unhealthy'),
            'issues' => $issues
        ];
    }
}