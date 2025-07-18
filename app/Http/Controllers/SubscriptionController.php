<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Models\Feature;
use App\Models\Goal;
use App\Models\Subscription;
use App\Models\Workspace;
use App\Services\SubscriptionService;
use App\Services\PricingCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function __construct(
        private SubscriptionService $subscriptionService,
        private PricingCalculatorService $pricingCalculator
    ) {}

    /**
     * Display subscription plans.
     */
    public function index(): View
    {
        $plans = SubscriptionPlan::with(['features.goal'])
            ->active()
            ->ordered()
            ->get();

        $goals = Goal::with('features')->active()->ordered()->get();
        $features = Feature::with('goal')->active()->get();

        $currentWorkspace = auth()->user()->currentWorkspace;
        $currentSubscription = $currentWorkspace?->subscription;

        return view('subscription.plans', compact('plans', 'goals', 'features', 'currentSubscription'));
    }

    /**
     * Show subscription management dashboard.
     */
    public function dashboard(): View
    {
        $workspace = auth()->user()->currentWorkspace;
        $subscription = $workspace?->subscription;

        if (!$subscription) {
            return redirect()->route('subscription.plans');
        }

        $subscription->load(['plan.features', 'items.feature', 'paymentFailures']);

        $usageData = $this->subscriptionService->getUsageData($subscription);
        $billingHistory = $this->subscriptionService->getBillingHistory($subscription);
        $upcomingInvoice = $this->subscriptionService->getUpcomingInvoice($subscription);

        return view('subscription.dashboard', compact(
            'subscription',
            'usageData',
            'billingHistory',
            'upcomingInvoice'
        ));
    }

    /**
     * Calculate pricing for selected features.
     */
    public function calculatePricing(Request $request): JsonResponse
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'features' => 'array',
            'features.*' => 'exists:features,key',
            'billing_cycle' => 'required|in:monthly,yearly',
        ]);

        $plan = SubscriptionPlan::findOrFail($request->plan_id);
        $features = $request->features ?? [];
        $billingCycle = $request->billing_cycle;

        $pricing = $this->pricingCalculator->calculatePricing($plan, $features, $billingCycle);

        return response()->json([
            'success' => true,
            'data' => $pricing,
        ]);
    }

    /**
     * Subscribe to a plan.
     */
    public function subscribe(Request $request): JsonResponse
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'features' => 'array',
            'features.*' => 'exists:features,key',
            'billing_cycle' => 'required|in:monthly,yearly',
            'payment_method_id' => 'required|string',
        ]);

        $workspace = auth()->user()->currentWorkspace;
        
        if (!$workspace) {
            return response()->json([
                'success' => false,
                'message' => 'No workspace selected',
            ], 422);
        }

        try {
            $subscription = $this->subscriptionService->createSubscription(
                $workspace,
                $request->plan_id,
                $request->features ?? [],
                $request->billing_cycle,
                $request->payment_method_id
            );

            return response()->json([
                'success' => true,
                'message' => 'Subscription created successfully',
                'data' => [
                    'subscription' => $subscription,
                    'redirect_url' => route('subscription.dashboard'),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Upgrade or downgrade subscription.
     */
    public function changePlan(Request $request): JsonResponse
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'features' => 'array',
            'features.*' => 'exists:features,key',
            'billing_cycle' => 'required|in:monthly,yearly',
        ]);

        $workspace = auth()->user()->currentWorkspace;
        $subscription = $workspace?->subscription;

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'No active subscription found',
            ], 422);
        }

        try {
            $updatedSubscription = $this->subscriptionService->changePlan(
                $subscription,
                $request->plan_id,
                $request->features ?? [],
                $request->billing_cycle
            );

            return response()->json([
                'success' => true,
                'message' => 'Subscription updated successfully',
                'data' => [
                    'subscription' => $updatedSubscription,
                    'proration_amount' => $this->subscriptionService->getProrationAmount($updatedSubscription),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Cancel subscription.
     */
    public function cancel(Request $request): JsonResponse
    {
        $request->validate([
            'reason' => 'nullable|string|max:1000',
            'feedback' => 'nullable|string|max:1000',
            'cancel_immediately' => 'boolean',
        ]);

        $workspace = auth()->user()->currentWorkspace;
        $subscription = $workspace?->subscription;

        if (!$subscription || !$subscription->canBeCancelled()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel this subscription',
            ], 422);
        }

        try {
            $cancelledSubscription = $this->subscriptionService->cancelSubscription(
                $subscription,
                $request->reason,
                $request->feedback,
                $request->boolean('cancel_immediately')
            );

            return response()->json([
                'success' => true,
                'message' => 'Subscription cancelled successfully',
                'data' => [
                    'subscription' => $cancelledSubscription,
                    'access_until' => $cancelledSubscription->current_period_end,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Resume cancelled subscription.
     */
    public function resume(): JsonResponse
    {
        $workspace = auth()->user()->currentWorkspace;
        $subscription = $workspace?->subscription;

        if (!$subscription || !$subscription->canBeResumed()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot resume this subscription',
            ], 422);
        }

        try {
            $resumedSubscription = $this->subscriptionService->resumeSubscription($subscription);

            return response()->json([
                'success' => true,
                'message' => 'Subscription resumed successfully',
                'data' => [
                    'subscription' => $resumedSubscription,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Update payment method.
     */
    public function updatePaymentMethod(Request $request): JsonResponse
    {
        $request->validate([
            'payment_method_id' => 'required|string',
        ]);

        $workspace = auth()->user()->currentWorkspace;
        $subscription = $workspace?->subscription;

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'No active subscription found',
            ], 422);
        }

        try {
            $this->subscriptionService->updatePaymentMethod(
                $subscription,
                $request->payment_method_id
            );

            return response()->json([
                'success' => true,
                'message' => 'Payment method updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get subscription usage data.
     */
    public function getUsageData(): JsonResponse
    {
        $workspace = auth()->user()->currentWorkspace;
        $subscription = $workspace?->subscription;

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'No active subscription found',
            ], 422);
        }

        $usageData = $this->subscriptionService->getUsageData($subscription);

        return response()->json([
            'success' => true,
            'data' => $usageData,
        ]);
    }

    /**
     * Get billing history.
     */
    public function getBillingHistory(): JsonResponse
    {
        $workspace = auth()->user()->currentWorkspace;
        $subscription = $workspace?->subscription;

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'No active subscription found',
            ], 422);
        }

        $billingHistory = $this->subscriptionService->getBillingHistory($subscription);

        return response()->json([
            'success' => true,
            'data' => $billingHistory,
        ]);
    }

    /**
     * Download invoice.
     */
    public function downloadInvoice(string $invoiceId): JsonResponse
    {
        $workspace = auth()->user()->currentWorkspace;
        $subscription = $workspace?->subscription;

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'No active subscription found',
            ], 422);
        }

        try {
            $downloadUrl = $this->subscriptionService->getInvoiceDownloadUrl($subscription, $invoiceId);

            return response()->json([
                'success' => true,
                'data' => [
                    'download_url' => $downloadUrl,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get plan comparison data.
     */
    public function getPlanComparison(): JsonResponse
    {
        $plans = SubscriptionPlan::with(['features.goal'])
            ->active()
            ->ordered()
            ->get();

        $features = Feature::with('goal')->active()->get();

        $comparisonData = [
            'plans' => $plans->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'description' => $plan->description,
                    'pricing_type' => $plan->pricing_type,
                    'base_price' => $plan->base_price,
                    'feature_price_monthly' => $plan->feature_price_monthly,
                    'feature_price_yearly' => $plan->feature_price_yearly,
                    'includes_whitelabel' => $plan->includes_whitelabel,
                    'badge_color' => $plan->getBadgeColor(),
                    'features' => $plan->features->pluck('key'),
                    'feature_limits' => $plan->features->mapWithKeys(function ($feature) {
                        return [$feature->key => $feature->pivot->quota_limit];
                    }),
                ];
            }),
            'features' => $features->groupBy('goal.key')->map(function ($groupedFeatures, $goalKey) {
                $goal = $groupedFeatures->first()->goal;
                return [
                    'goal' => [
                        'key' => $goal->key,
                        'name' => $goal->name,
                        'color' => $goal->color,
                        'icon' => $goal->icon,
                    ],
                    'features' => $groupedFeatures->map(function ($feature) {
                        return [
                            'key' => $feature->key,
                            'name' => $feature->name,
                            'description' => $feature->description,
                            'type' => $feature->type,
                        ];
                    }),
                ];
            }),
        ];

        return response()->json([
            'success' => true,
            'data' => $comparisonData,
        ]);
    }

    /**
     * Get retention offers for cancellation flow.
     */
    public function getRetentionOffers(): JsonResponse
    {
        $workspace = auth()->user()->currentWorkspace;
        $subscription = $workspace?->subscription;

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'No active subscription found',
            ], 422);
        }

        $offers = $this->subscriptionService->getRetentionOffers($subscription);

        return response()->json([
            'success' => true,
            'data' => $offers,
        ]);
    }
}