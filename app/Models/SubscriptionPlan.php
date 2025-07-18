<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'billing_cycle',
        'features',
        'feature_limits',
        'is_active',
        'is_popular',
        'trial_days',
        'setup_fee',
        'order',
        'stripe_price_id',
        'metadata'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'array',
        'feature_limits' => 'array',
        'is_active' => 'boolean',
        'is_popular' => 'boolean',
        'trial_days' => 'integer',
        'setup_fee' => 'decimal:2',
        'order' => 'integer',
        'metadata' => 'array'
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class, 'plan_id');
    }

    public function addons(): BelongsToMany
    {
        return $this->belongsToMany(SubscriptionAddon::class, 'plan_addon_assignments');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    // Accessors
    public function getMonthlyPriceAttribute()
    {
        return $this->billing_cycle === 'yearly' ? $this->price / 12 : $this->price;
    }

    public function getYearlyPriceAttribute()
    {
        return $this->billing_cycle === 'monthly' ? $this->price * 12 : $this->price;
    }

    public function getYearlySavingsAttribute()
    {
        if ($this->billing_cycle !== 'yearly') {
            return 0;
        }

        $monthlyEquivalent = $this->price / 12;
        $monthlyPlan = static::where('name', $this->name)
            ->where('billing_cycle', 'monthly')
            ->first();

        if (!$monthlyPlan) {
            return 0;
        }

        return ($monthlyPlan->price * 12) - $this->price;
    }

    public function getYearlySavingsPercentageAttribute()
    {
        $savings = $this->yearly_savings;
        $yearlyPrice = $this->yearly_price;

        if ($yearlyPrice == 0) {
            return 0;
        }

        return round(($savings / $yearlyPrice) * 100, 1);
    }

    // Methods
    public function hasFeature($feature)
    {
        return in_array($feature, $this->features ?? []);
    }

    public function getFeatureLimit($feature)
    {
        $limits = $this->feature_limits ?? [];
        return $limits[$feature] ?? null;
    }

    public function isFeatureLimited($feature)
    {
        $limits = $this->feature_limits ?? [];
        return isset($limits[$feature]) && $limits[$feature] > 0;
    }

    public function isFeatureUnlimited($feature)
    {
        $limits = $this->feature_limits ?? [];
        return isset($limits[$feature]) && $limits[$feature] === -1;
    }

    public function compareWith(SubscriptionPlan $other)
    {
        $comparison = [
            'price_difference' => $this->price - $other->price,
            'features_added' => array_diff($this->features ?? [], $other->features ?? []),
            'features_removed' => array_diff($other->features ?? [], $this->features ?? []),
            'limits_increased' => [],
            'limits_decreased' => []
        ];

        $thisLimits = $this->feature_limits ?? [];
        $otherLimits = $other->feature_limits ?? [];

        foreach (array_keys(array_merge($thisLimits, $otherLimits)) as $feature) {
            $thisLimit = $thisLimits[$feature] ?? 0;
            $otherLimit = $otherLimits[$feature] ?? 0;

            if ($thisLimit > $otherLimit) {
                $comparison['limits_increased'][$feature] = [
                    'from' => $otherLimit,
                    'to' => $thisLimit
                ];
            } elseif ($thisLimit < $otherLimit) {
                $comparison['limits_decreased'][$feature] = [
                    'from' => $otherLimit,
                    'to' => $thisLimit
                ];
            }
        }

        return $comparison;
    }

    public function getActiveSubscriptionsCount()
    {
        return $this->subscriptions()->where('status', 'active')->count();
    }

    public function getMonthlyRecurringRevenue()
    {
        $activeSubscriptions = $this->getActiveSubscriptionsCount();
        
        if ($this->billing_cycle === 'monthly') {
            return $activeSubscriptions * $this->price;
        } elseif ($this->billing_cycle === 'yearly') {
            return $activeSubscriptions * ($this->price / 12);
        }

        return 0;
    }

    public function getAnnualRecurringRevenue()
    {
        $activeSubscriptions = $this->getActiveSubscriptionsCount();
        
        if ($this->billing_cycle === 'monthly') {
            return $activeSubscriptions * ($this->price * 12);
        } elseif ($this->billing_cycle === 'yearly') {
            return $activeSubscriptions * $this->price;
        }

        return 0;
    }

    public function getChurnRate($days = 30)
    {
        $startDate = now()->subDays($days);
        $totalAtStart = $this->subscriptions()
            ->where('created_at', '<', $startDate)
            ->count();

        $churned = $this->subscriptions()
            ->where('canceled_at', '>=', $startDate)
            ->count();

        return $totalAtStart > 0 ? ($churned / $totalAtStart) * 100 : 0;
    }

    public function getAverageLifetimeValue()
    {
        $subscriptions = $this->subscriptions()->where('status', 'canceled')->get();
        
        if ($subscriptions->isEmpty()) {
            return 0;
        }

        $totalValue = $subscriptions->sum(function ($subscription) {
            $months = $subscription->created_at->diffInMonths($subscription->canceled_at ?: now());
            return $months * $this->monthly_price;
        });

        return $totalValue / $subscriptions->count();
    }

    public function getConversionRate()
    {
        $trials = $this->subscriptions()
            ->where('status', 'trialing')
            ->orWhere('trial_ends_at', '!=', null)
            ->count();

        $conversions = $this->subscriptions()
            ->where('status', 'active')
            ->whereNotNull('trial_ends_at')
            ->count();

        return $trials > 0 ? ($conversions / $trials) * 100 : 0;
    }

    public function getRecommendedUpgrade()
    {
        $nextPlan = static::where('price', '>', $this->price)
            ->where('billing_cycle', $this->billing_cycle)
            ->where('is_active', true)
            ->orderBy('price')
            ->first();

        return $nextPlan;
    }

    public function getRecommendedDowngrade()
    {
        $prevPlan = static::where('price', '<', $this->price)
            ->where('billing_cycle', $this->billing_cycle)
            ->where('is_active', true)
            ->orderBy('price', 'desc')
            ->first();

        return $prevPlan;
    }

    public function canUpgradeTo(SubscriptionPlan $plan)
    {
        return $plan->price > $this->price && $plan->billing_cycle === $this->billing_cycle;
    }

    public function canDowngradeTo(SubscriptionPlan $plan)
    {
        return $plan->price < $this->price && $plan->billing_cycle === $this->billing_cycle;
    }

    public function getFormattedPrice()
    {
        $price = number_format($this->price, 2);
        $currency = config('app.currency_symbol', '$');
        
        return $currency . $price;
    }

    public function getFormattedPriceWithCycle()
    {
        $price = $this->getFormattedPrice();
        $cycle = $this->billing_cycle === 'monthly' ? 'month' : 'year';
        
        return $price . ' per ' . $cycle;
    }

    public function toArray()
    {
        $array = parent::toArray();
        
        // Add computed attributes
        $array['monthly_price'] = $this->monthly_price;
        $array['yearly_price'] = $this->yearly_price;
        $array['yearly_savings'] = $this->yearly_savings;
        $array['yearly_savings_percentage'] = $this->yearly_savings_percentage;
        $array['formatted_price'] = $this->getFormattedPrice();
        $array['formatted_price_with_cycle'] = $this->getFormattedPriceWithCycle();
        
        return $array;
    }
}