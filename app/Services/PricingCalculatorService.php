<?php

namespace App\Services;

use App\Models\SubscriptionPlan;
use App\Models\Feature;

class PricingCalculatorService
{
    /**
     * Calculate pricing for a plan with selected features.
     */
    public function calculatePricing(SubscriptionPlan $plan, array $selectedFeatures, string $billingCycle): array
    {
        $features = Feature::whereIn('key', $selectedFeatures)->get();
        
        $pricing = [
            'plan' => [
                'id' => $plan->id,
                'name' => $plan->name,
                'pricing_type' => $plan->pricing_type,
                'billing_cycle' => $billingCycle,
            ],
            'features' => [
                'selected' => $selectedFeatures,
                'count' => count($selectedFeatures),
                'details' => $features->map(function ($feature) use ($plan) {
                    $planFeature = $plan->features()->where('key', $feature->key)->first();
                    return [
                        'key' => $feature->key,
                        'name' => $feature->name,
                        'type' => $feature->type,
                        'quota_limit' => $planFeature?->pivot->quota_limit,
                        'is_included' => $planFeature?->pivot->is_included ?? false,
                    ];
                }),
            ],
            'costs' => $this->calculateCosts($plan, $selectedFeatures, $billingCycle),
            'savings' => $this->calculateSavings($plan, $selectedFeatures, $billingCycle),
            'breakdown' => $this->getBreakdown($plan, $selectedFeatures, $billingCycle),
        ];

        return $pricing;
    }

    /**
     * Calculate costs for different billing cycles.
     */
    private function calculateCosts(SubscriptionPlan $plan, array $selectedFeatures, string $billingCycle): array
    {
        $featureCount = count($selectedFeatures);
        $basePrice = (float) $plan->base_price;
        
        if ($plan->pricing_type === 'flat_monthly') {
            $monthlyTotal = $basePrice;
            $yearlyTotal = $basePrice * 12;
        } else {
            $monthlyFeaturePrice = (float) $plan->feature_price_monthly;
            $yearlyFeaturePrice = (float) $plan->feature_price_yearly;
            
            $monthlyTotal = $basePrice + ($monthlyFeaturePrice * $featureCount);
            $yearlyTotal = ($basePrice * 12) + ($yearlyFeaturePrice * $featureCount);
        }

        return [
            'monthly' => [
                'base' => $basePrice,
                'features' => $plan->pricing_type === 'flat_monthly' ? 0 : (float) $plan->feature_price_monthly * $featureCount,
                'total' => $monthlyTotal,
            ],
            'yearly' => [
                'base' => $basePrice * 12,
                'features' => $plan->pricing_type === 'flat_monthly' ? 0 : (float) $plan->feature_price_yearly * $featureCount,
                'total' => $yearlyTotal,
            ],
            'selected' => [
                'cycle' => $billingCycle,
                'amount' => $billingCycle === 'yearly' ? $yearlyTotal : $monthlyTotal,
            ],
        ];
    }

    /**
     * Calculate savings for yearly billing.
     */
    private function calculateSavings(SubscriptionPlan $plan, array $selectedFeatures, string $billingCycle): array
    {
        $costs = $this->calculateCosts($plan, $selectedFeatures, $billingCycle);
        $monthlyTotal = $costs['monthly']['total'];
        $yearlyTotal = $costs['yearly']['total'];
        $yearlyEquivalent = $monthlyTotal * 12;
        
        $savings = [
            'yearly_vs_monthly' => [
                'amount' => $yearlyEquivalent - $yearlyTotal,
                'percentage' => $yearlyEquivalent > 0 ? (($yearlyEquivalent - $yearlyTotal) / $yearlyEquivalent) * 100 : 0,
            ],
            'recommendations' => [],
        ];

        // Add recommendations based on savings
        if ($savings['yearly_vs_monthly']['amount'] > 0) {
            $savings['recommendations'][] = [
                'type' => 'billing_cycle',
                'title' => 'Switch to Yearly Billing',
                'description' => 'Save $' . number_format($savings['yearly_vs_monthly']['amount'], 2) . ' per year',
                'savings' => $savings['yearly_vs_monthly']['amount'],
            ];
        }

        return $savings;
    }

    /**
     * Get detailed cost breakdown.
     */
    private function getBreakdown(SubscriptionPlan $plan, array $selectedFeatures, string $billingCycle): array
    {
        $features = Feature::whereIn('key', $selectedFeatures)->get();
        $breakdown = [];

        // Base cost
        $baseCost = (float) $plan->base_price;
        if ($billingCycle === 'yearly') {
            $baseCost *= 12;
        }

        if ($baseCost > 0) {
            $breakdown[] = [
                'type' => 'base',
                'name' => 'Base Plan',
                'description' => 'Platform access and core features',
                'cost' => $baseCost,
                'billing_cycle' => $billingCycle,
            ];
        }

        // Feature costs
        if ($plan->pricing_type === 'feature_based') {
            $featurePrice = $billingCycle === 'yearly' ? 
                (float) $plan->feature_price_yearly : 
                (float) $plan->feature_price_monthly;

            foreach ($features as $feature) {
                $breakdown[] = [
                    'type' => 'feature',
                    'name' => $feature->name,
                    'description' => $feature->description,
                    'cost' => $featurePrice,
                    'billing_cycle' => $billingCycle,
                    'feature_key' => $feature->key,
                    'feature_type' => $feature->type,
                ];
            }
        }

        return $breakdown;
    }

    /**
     * Compare plans with selected features.
     */
    public function comparePlans(array $selectedFeatures, string $billingCycle): array
    {
        $plans = SubscriptionPlan::with('features')->active()->ordered()->get();
        $comparison = [];

        foreach ($plans as $plan) {
            $pricing = $this->calculatePricing($plan, $selectedFeatures, $billingCycle);
            
            $comparison[] = [
                'plan' => [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'description' => $plan->description,
                    'pricing_type' => $plan->pricing_type,
                    'includes_whitelabel' => $plan->includes_whitelabel,
                    'badge_color' => $plan->getBadgeColor(),
                ],
                'pricing' => $pricing,
                'supported_features' => $this->getSupportedFeatures($plan, $selectedFeatures),
                'value_score' => $this->calculateValueScore($plan, $selectedFeatures, $pricing),
            ];
        }

        // Sort by value score (highest first)
        usort($comparison, function ($a, $b) {
            return $b['value_score'] <=> $a['value_score'];
        });

        return $comparison;
    }

    /**
     * Get supported features for a plan.
     */
    private function getSupportedFeatures(SubscriptionPlan $plan, array $selectedFeatures): array
    {
        $planFeatures = $plan->features()->whereIn('key', $selectedFeatures)->get();
        
        return $planFeatures->map(function ($feature) {
            return [
                'key' => $feature->key,
                'name' => $feature->name,
                'is_included' => $feature->pivot->is_included,
                'quota_limit' => $feature->pivot->quota_limit,
                'overage_price' => $feature->pivot->overage_price,
            ];
        })->toArray();
    }

    /**
     * Calculate value score for a plan.
     */
    private function calculateValueScore(SubscriptionPlan $plan, array $selectedFeatures, array $pricing): float
    {
        $supportedFeatures = $this->getSupportedFeatures($plan, $selectedFeatures);
        $supportedCount = count(array_filter($supportedFeatures, fn($f) => $f['is_included']));
        $totalFeatures = count($selectedFeatures);
        
        if ($totalFeatures === 0) {
            return 0;
        }

        $featureScore = ($supportedCount / $totalFeatures) * 100;
        $cost = $pricing['costs']['selected']['amount'];
        
        // Higher score for more features supported at lower cost
        $valueScore = $featureScore / max(1, $cost / 100);
        
        // Bonus points for white-label
        if ($plan->includes_whitelabel) {
            $valueScore += 10;
        }

        return round($valueScore, 2);
    }

    /**
     * Get pricing examples for different feature counts.
     */
    public function getPricingExamples(SubscriptionPlan $plan): array
    {
        $examples = [];
        $featureCounts = [1, 3, 5, 10, 15, 20];

        foreach ($featureCounts as $count) {
            $mockFeatures = array_fill(0, $count, 'example_feature');
            
            $examples[$count] = [
                'feature_count' => $count,
                'monthly' => $this->calculateCosts($plan, $mockFeatures, 'monthly'),
                'yearly' => $this->calculateCosts($plan, $mockFeatures, 'yearly'),
            ];
        }

        return $examples;
    }

    /**
     * Calculate upgrade/downgrade costs.
     */
    public function calculatePlanChangeCost(
        SubscriptionPlan $currentPlan,
        SubscriptionPlan $newPlan,
        array $currentFeatures,
        array $newFeatures,
        string $billingCycle
    ): array {
        $currentCosts = $this->calculateCosts($currentPlan, $currentFeatures, $billingCycle);
        $newCosts = $this->calculateCosts($newPlan, $newFeatures, $billingCycle);
        
        $difference = $newCosts['selected']['amount'] - $currentCosts['selected']['amount'];
        
        return [
            'current' => $currentCosts,
            'new' => $newCosts,
            'difference' => [
                'amount' => $difference,
                'is_upgrade' => $difference > 0,
                'is_downgrade' => $difference < 0,
                'proration' => $this->calculateProration($difference, $billingCycle),
            ],
        ];
    }

    /**
     * Calculate proration for plan changes.
     */
    private function calculateProration(float $difference, string $billingCycle): array
    {
        $daysInCycle = $billingCycle === 'yearly' ? 365 : 30;
        $remainingDays = 15; // This would be calculated based on actual subscription
        
        $prorationAmount = ($difference / $daysInCycle) * $remainingDays;
        
        return [
            'amount' => $prorationAmount,
            'days_remaining' => $remainingDays,
            'total_days' => $daysInCycle,
            'description' => $prorationAmount > 0 ? 
                'You will be charged a prorated amount for the upgrade' : 
                'You will receive a prorated credit for the downgrade',
        ];
    }
}