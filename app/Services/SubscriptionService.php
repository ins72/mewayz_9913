<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionItem;
use App\Models\Workspace;
use App\Models\Feature;
use App\Models\PaymentFailure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class SubscriptionService
{
    public function __construct(
        private StripeService $stripeService,
        private PricingCalculatorService $pricingCalculator
    ) {}

    /**
     * Create a new subscription.
     */
    public function createSubscription(
        Workspace $workspace,
        int $planId,
        array $features,
        string $billingCycle,
        string $paymentMethodId
    ): Subscription {
        $plan = SubscriptionPlan::findOrFail($planId);
        
        return DB::transaction(function () use ($workspace, $plan, $features, $billingCycle, $paymentMethodId) {
            // Create local subscription record
            $subscription = Subscription::create([
                'workspace_id' => $workspace->id,
                'plan_id' => $plan->id,
                'status' => 'trialing',
                'trial_start' => now(),
                'trial_end' => now()->addDays(14),
                'current_period_start' => now(),
                'current_period_end' => now()->addDays(14),
            ]);

            // Create Stripe subscription
            $stripeSubscription = $this->stripeService->createSubscription(
                $workspace,
                $plan,
                $features,
                $billingCycle,
                $paymentMethodId
            );

            // Update subscription with Stripe data
            $subscription->update([
                'stripe_subscription_id' => $stripeSubscription->id,
                'status' => $stripeSubscription->status,
                'current_period_start' => $stripeSubscription->current_period_start,
                'current_period_end' => $stripeSubscription->current_period_end,
                'trial_start' => $stripeSubscription->trial_start,
                'trial_end' => $stripeSubscription->trial_end,
            ]);

            // Create subscription items for feature-based pricing
            if ($plan->pricing_type === 'feature_based') {
                $this->createSubscriptionItems($subscription, $features, $billingCycle);
            }

            // Apply features to workspace
            $this->applyFeaturesToWorkspace($workspace, $subscription);

            // Log subscription creation
            Log::info('Subscription created', [
                'subscription_id' => $subscription->id,
                'workspace_id' => $workspace->id,
                'plan_id' => $plan->id,
                'features' => $features,
            ]);

            return $subscription->fresh(['plan', 'items']);
        });
    }

    /**
     * Change subscription plan.
     */
    public function changePlan(
        Subscription $subscription,
        int $newPlanId,
        array $features,
        string $billingCycle
    ): Subscription {
        $newPlan = SubscriptionPlan::findOrFail($newPlanId);
        
        return DB::transaction(function () use ($subscription, $newPlan, $features, $billingCycle) {
            // Update Stripe subscription
            $stripeSubscription = $this->stripeService->updateSubscription(
                $subscription->stripe_subscription_id,
                $newPlan,
                $features,
                $billingCycle
            );

            // Update local subscription
            $subscription->update([
                'plan_id' => $newPlan->id,
                'status' => $stripeSubscription->status,
                'current_period_start' => $stripeSubscription->current_period_start,
                'current_period_end' => $stripeSubscription->current_period_end,
            ]);

            // Update subscription items
            $subscription->items()->delete();
            if ($newPlan->pricing_type === 'feature_based') {
                $this->createSubscriptionItems($subscription, $features, $billingCycle);
            }

            // Update workspace features
            $this->applyFeaturesToWorkspace($subscription->workspace, $subscription);

            // Log plan change
            Log::info('Subscription plan changed', [
                'subscription_id' => $subscription->id,
                'old_plan_id' => $subscription->getOriginal('plan_id'),
                'new_plan_id' => $newPlan->id,
                'features' => $features,
            ]);

            return $subscription->fresh(['plan', 'items']);
        });
    }

    /**
     * Cancel subscription.
     */
    public function cancelSubscription(
        Subscription $subscription,
        ?string $reason = null,
        ?string $feedback = null,
        bool $cancelImmediately = false
    ): Subscription {
        return DB::transaction(function () use ($subscription, $reason, $feedback, $cancelImmediately) {
            // Cancel Stripe subscription
            $stripeSubscription = $this->stripeService->cancelSubscription(
                $subscription->stripe_subscription_id,
                $cancelImmediately
            );

            // Update local subscription
            $subscription->update([
                'status' => $stripeSubscription->status,
                'cancelled_at' => now(),
                'current_period_end' => $stripeSubscription->current_period_end,
                'metadata' => array_merge($subscription->metadata ?? [], [
                    'cancellation_reason' => $reason,
                    'cancellation_feedback' => $feedback,
                    'cancelled_immediately' => $cancelImmediately,
                ]),
            ]);

            // If cancelled immediately, revoke features
            if ($cancelImmediately) {
                $this->revokeWorkspaceFeatures($subscription->workspace);
            }

            // Log cancellation
            Log::info('Subscription cancelled', [
                'subscription_id' => $subscription->id,
                'reason' => $reason,
                'immediate' => $cancelImmediately,
            ]);

            return $subscription->fresh(['plan', 'items']);
        });
    }

    /**
     * Resume cancelled subscription.
     */
    public function resumeSubscription(Subscription $subscription): Subscription
    {
        return DB::transaction(function () use ($subscription) {
            // Resume Stripe subscription
            $stripeSubscription = $this->stripeService->resumeSubscription(
                $subscription->stripe_subscription_id
            );

            // Update local subscription
            $subscription->update([
                'status' => $stripeSubscription->status,
                'cancelled_at' => null,
                'current_period_end' => $stripeSubscription->current_period_end,
            ]);

            // Reapply features to workspace
            $this->applyFeaturesToWorkspace($subscription->workspace, $subscription);

            // Log resumption
            Log::info('Subscription resumed', [
                'subscription_id' => $subscription->id,
            ]);

            return $subscription->fresh(['plan', 'items']);
        });
    }

    /**
     * Update payment method.
     */
    public function updatePaymentMethod(Subscription $subscription, string $paymentMethodId): void
    {
        $this->stripeService->updateSubscriptionPaymentMethod(
            $subscription->stripe_subscription_id,
            $paymentMethodId
        );

        Log::info('Payment method updated', [
            'subscription_id' => $subscription->id,
        ]);
    }

    /**
     * Get subscription usage data.
     */
    public function getUsageData(Subscription $subscription): array
    {
        $items = $subscription->items()->with('feature')->get();
        $workspace = $subscription->workspace;

        $usageData = [
            'current_period' => [
                'start' => $subscription->current_period_start,
                'end' => $subscription->current_period_end,
                'days_remaining' => $subscription->current_period_end ? 
                    max(0, $subscription->current_period_end->diffInDays(now())) : 0,
            ],
            'features' => [],
            'summary' => [
                'total_features' => $items->count(),
                'features_at_limit' => 0,
                'features_approaching_limit' => 0,
                'total_usage' => 0,
            ],
        ];

        foreach ($items as $item) {
            $workspaceFeature = $workspace->features()->where('key', $item->feature_key)->first();
            $currentUsage = $workspaceFeature?->pivot->usage_count ?? 0;
            
            $featureData = [
                'key' => $item->feature_key,
                'name' => $item->feature?->name ?? $item->feature_key,
                'quota_limit' => $item->quota_limit,
                'usage_count' => $currentUsage,
                'remaining' => $item->quota_limit ? max(0, $item->quota_limit - $currentUsage) : null,
                'percentage' => $item->quota_limit ? min(100, ($currentUsage / $item->quota_limit) * 100) : 0,
                'status' => $this->getFeatureUsageStatus($currentUsage, $item->quota_limit),
            ];

            $usageData['features'][] = $featureData;
            $usageData['summary']['total_usage'] += $currentUsage;

            if ($item->quota_limit && $currentUsage >= $item->quota_limit) {
                $usageData['summary']['features_at_limit']++;
            } elseif ($item->quota_limit && $currentUsage >= ($item->quota_limit * 0.8)) {
                $usageData['summary']['features_approaching_limit']++;
            }
        }

        return $usageData;
    }

    /**
     * Get billing history.
     */
    public function getBillingHistory(Subscription $subscription): array
    {
        return $this->stripeService->getInvoices($subscription->stripe_subscription_id);
    }

    /**
     * Get upcoming invoice.
     */
    public function getUpcomingInvoice(Subscription $subscription): ?array
    {
        return $this->stripeService->getUpcomingInvoice($subscription->stripe_subscription_id);
    }

    /**
     * Get proration amount for subscription changes.
     */
    public function getProrationAmount(Subscription $subscription): float
    {
        $upcomingInvoice = $this->getUpcomingInvoice($subscription);
        return $upcomingInvoice ? $upcomingInvoice['amount_due'] / 100 : 0.0;
    }

    /**
     * Get invoice download URL.
     */
    public function getInvoiceDownloadUrl(Subscription $subscription, string $invoiceId): string
    {
        return $this->stripeService->getInvoiceDownloadUrl($invoiceId);
    }

    /**
     * Get retention offers for cancellation flow.
     */
    public function getRetentionOffers(Subscription $subscription): array
    {
        $plan = $subscription->plan;
        $currentPrice = $subscription->getMonthlyCost();

        $offers = [];

        // Discount offer
        if ($currentPrice > 0) {
            $offers[] = [
                'type' => 'discount',
                'title' => '50% Off for 3 Months',
                'description' => 'Continue with your current plan at 50% off for the next 3 months',
                'discount_percentage' => 50,
                'discount_duration' => 3,
                'new_price' => $currentPrice * 0.5,
                'savings' => $currentPrice * 0.5 * 3,
            ];
        }

        // Downgrade offer
        $lowerPlans = SubscriptionPlan::where('base_price', '<', $plan->base_price)
            ->where('feature_price_monthly', '<', $plan->feature_price_monthly)
            ->active()
            ->get();

        if ($lowerPlans->count() > 0) {
            $suggestedPlan = $lowerPlans->first();
            $offers[] = [
                'type' => 'downgrade',
                'title' => 'Switch to ' . $suggestedPlan->name,
                'description' => 'Reduce your costs while keeping essential features',
                'plan_id' => $suggestedPlan->id,
                'plan_name' => $suggestedPlan->name,
                'new_price' => $suggestedPlan->base_price,
                'savings' => $currentPrice - $suggestedPlan->base_price,
            ];
        }

        // Pause offer
        $offers[] = [
            'type' => 'pause',
            'title' => 'Pause Your Subscription',
            'description' => 'Take a break for up to 3 months, then resume automatically',
            'pause_duration' => 90,
            'resume_date' => now()->addDays(90)->format('F j, Y'),
        ];

        // Annual discount offer
        if ($plan->feature_price_yearly > 0) {
            $yearlyDiscount = (($plan->feature_price_monthly * 12) - $plan->feature_price_yearly) / ($plan->feature_price_monthly * 12) * 100;
            $offers[] = [
                'type' => 'annual',
                'title' => 'Switch to Annual Billing',
                'description' => 'Save ' . round($yearlyDiscount) . '% with annual billing',
                'discount_percentage' => round($yearlyDiscount),
                'new_price' => $plan->feature_price_yearly,
                'savings' => ($plan->feature_price_monthly * 12) - $plan->feature_price_yearly,
            ];
        }

        return $offers;
    }

    /**
     * Create subscription items for feature-based pricing.
     */
    private function createSubscriptionItems(Subscription $subscription, array $features, string $billingCycle): void
    {
        $plan = $subscription->plan;
        $unitPrice = $billingCycle === 'yearly' ? $plan->feature_price_yearly : $plan->feature_price_monthly;

        foreach ($features as $featureKey) {
            $feature = Feature::where('key', $featureKey)->first();
            $planFeature = $plan->features()->where('key', $featureKey)->first();

            if ($feature && $planFeature) {
                SubscriptionItem::create([
                    'subscription_id' => $subscription->id,
                    'feature_key' => $featureKey,
                    'quantity' => 1,
                    'unit_price' => $unitPrice,
                    'quota_limit' => $planFeature->pivot->quota_limit,
                    'usage_count' => 0,
                ]);
            }
        }
    }

    /**
     * Apply subscription features to workspace.
     */
    private function applyFeaturesToWorkspace(Workspace $workspace, Subscription $subscription): void
    {
        $plan = $subscription->plan;
        $features = [];

        if ($plan->pricing_type === 'flat_monthly') {
            // For flat pricing, use all included features from the plan
            foreach ($plan->features as $feature) {
                if ($feature->pivot->is_included) {
                    $features[$feature->key] = [
                        'is_enabled' => true,
                        'quota_limit' => $feature->pivot->quota_limit,
                        'usage_count' => 0,
                    ];
                }
            }
        } else {
            // For feature-based pricing, use subscription items
            foreach ($subscription->items as $item) {
                $features[$item->feature_key] = [
                    'is_enabled' => true,
                    'quota_limit' => $item->quota_limit,
                    'usage_count' => 0,
                ];
            }
        }

        $workspace->features()->sync($features);
    }

    /**
     * Revoke all features from workspace.
     */
    private function revokeWorkspaceFeatures(Workspace $workspace): void
    {
        $workspace->features()->sync([]);
    }

    /**
     * Get feature usage status.
     */
    private function getFeatureUsageStatus(int $usage, ?int $limit): string
    {
        if (!$limit) {
            return 'unlimited';
        }

        $percentage = ($usage / $limit) * 100;

        if ($percentage >= 100) {
            return 'at_limit';
        } elseif ($percentage >= 80) {
            return 'approaching_limit';
        } elseif ($percentage >= 50) {
            return 'moderate';
        } else {
            return 'low';
        }
    }
}