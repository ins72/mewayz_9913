<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\Feature;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class PlanManagementController extends Controller
{
    /**
     * Display the plan management dashboard.
     */
    public function index(): View
    {
        $plans = SubscriptionPlan::with('features')->ordered()->get();
        $features = Feature::with('goal')->active()->get();

        return view('admin.plan-management.index', compact('plans', 'features'));
    }

    /**
     * Show the form for creating a new plan.
     */
    public function create(): View
    {
        $features = Feature::with('goal')->active()->get();
        
        return view('admin.plan-management.create', compact('features'));
    }

    /**
     * Store a newly created plan.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'pricing_type' => 'required|in:feature_based,flat_monthly',
            'base_price' => 'required|numeric|min:0',
            'feature_price_monthly' => 'nullable|numeric|min:0',
            'feature_price_yearly' => 'nullable|numeric|min:0',
            'max_features' => 'nullable|integer|min:1',
            'includes_whitelabel' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'features' => 'array',
            'features.*.key' => 'required_with:features|exists:features,key',
            'features.*.is_included' => 'required_with:features|boolean',
            'features.*.quota_limit' => 'nullable|integer|min:1',
            'features.*.overage_price' => 'nullable|numeric|min:0',
        ]);

        $plan = SubscriptionPlan::create($request->except('features'));

        // Attach features if provided
        if ($request->has('features')) {
            $featureAssignments = [];
            foreach ($request->features as $featureData) {
                $featureAssignments[$featureData['key']] = [
                    'is_included' => $featureData['is_included'],
                    'quota_limit' => $featureData['quota_limit'] ?? null,
                    'overage_price' => $featureData['overage_price'] ?? 0,
                ];
            }
            $plan->features()->attach($featureAssignments);
        }

        return response()->json([
            'success' => true,
            'message' => 'Plan created successfully',
            'data' => [
                'plan' => $plan->load('features'),
            ],
        ]);
    }

    /**
     * Display the specified plan.
     */
    public function show(SubscriptionPlan $plan): View
    {
        $plan->load(['features.goal', 'subscriptions']);
        
        return view('admin.plan-management.show', compact('plan'));
    }

    /**
     * Show the form for editing the specified plan.
     */
    public function edit(SubscriptionPlan $plan): View
    {
        $plan->load('features');
        $features = Feature::with('goal')->active()->get();
        
        return view('admin.plan-management.edit', compact('plan', 'features'));
    }

    /**
     * Update the specified plan.
     */
    public function update(Request $request, SubscriptionPlan $plan): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'pricing_type' => 'required|in:feature_based,flat_monthly',
            'base_price' => 'required|numeric|min:0',
            'feature_price_monthly' => 'nullable|numeric|min:0',
            'feature_price_yearly' => 'nullable|numeric|min:0',
            'max_features' => 'nullable|integer|min:1',
            'includes_whitelabel' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'features' => 'array',
            'features.*.key' => 'required_with:features|exists:features,key',
            'features.*.is_included' => 'required_with:features|boolean',
            'features.*.quota_limit' => 'nullable|integer|min:1',
            'features.*.overage_price' => 'nullable|numeric|min:0',
        ]);

        $plan->update($request->except('features'));

        // Update features if provided
        if ($request->has('features')) {
            $featureAssignments = [];
            foreach ($request->features as $featureData) {
                $featureAssignments[$featureData['key']] = [
                    'is_included' => $featureData['is_included'],
                    'quota_limit' => $featureData['quota_limit'] ?? null,
                    'overage_price' => $featureData['overage_price'] ?? 0,
                ];
            }
            $plan->features()->sync($featureAssignments);
        }

        return response()->json([
            'success' => true,
            'message' => 'Plan updated successfully',
            'data' => [
                'plan' => $plan->load('features'),
            ],
        ]);
    }

    /**
     * Toggle plan active status.
     */
    public function toggleStatus(SubscriptionPlan $plan): JsonResponse
    {
        $plan->update(['is_active' => !$plan->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Plan ' . ($plan->is_active ? 'activated' : 'deactivated') . ' successfully',
            'data' => [
                'plan' => $plan,
            ],
        ]);
    }

    /**
     * Update plan sort order.
     */
    public function updateOrder(Request $request): JsonResponse
    {
        $request->validate([
            'plans' => 'required|array',
            'plans.*.id' => 'required|exists:subscription_plans,id',
            'plans.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($request->plans as $planData) {
            SubscriptionPlan::where('id', $planData['id'])
                ->update(['sort_order' => $planData['sort_order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Plan order updated successfully',
        ]);
    }

    /**
     * Delete the specified plan.
     */
    public function destroy(SubscriptionPlan $plan): JsonResponse
    {
        // Check if plan has active subscriptions
        $subscriptionCount = $plan->subscriptions()->count();
        
        if ($subscriptionCount > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete plan with active subscriptions',
                'data' => [
                    'subscription_count' => $subscriptionCount,
                ],
            ], 422);
        }

        $plan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Plan deleted successfully',
        ]);
    }

    /**
     * Get plan analytics.
     */
    public function getAnalytics(SubscriptionPlan $plan): JsonResponse
    {
        // This would implement actual analytics
        // For now, returning mock data
        $analytics = [
            'subscribers' => [
                'total' => 150,
                'active' => 142,
                'trial' => 8,
                'churned_this_month' => 5,
            ],
            'revenue' => [
                'monthly' => 4250.00,
                'yearly' => 51000.00,
                'average_per_user' => 28.33,
            ],
            'features' => [
                'most_used' => [
                    ['key' => 'instagram_post_scheduling', 'usage' => 89],
                    ['key' => 'link_bio_pages', 'usage' => 76],
                    ['key' => 'course_creation', 'usage' => 45],
                ],
                'least_used' => [
                    ['key' => 'ai_image_generation', 'usage' => 12],
                    ['key' => 'website_seo_tools', 'usage' => 18],
                ],
            ],
            'growth' => [
                'new_subscribers_this_month' => 23,
                'growth_rate' => 15.3,
                'retention_rate' => 94.2,
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $analytics,
        ]);
    }

    /**
     * Clone a plan.
     */
    public function clone(SubscriptionPlan $plan): JsonResponse
    {
        $newPlan = $plan->replicate();
        $newPlan->name = $plan->name . ' (Copy)';
        $newPlan->is_active = false;
        $newPlan->sort_order = $plan->sort_order + 1;
        $newPlan->save();

        // Clone the features
        $features = $plan->features;
        $featureAssignments = [];
        foreach ($features as $feature) {
            $featureAssignments[$feature->key] = [
                'is_included' => $feature->pivot->is_included,
                'quota_limit' => $feature->pivot->quota_limit,
                'overage_price' => $feature->pivot->overage_price,
            ];
        }
        $newPlan->features()->attach($featureAssignments);

        return response()->json([
            'success' => true,
            'message' => 'Plan cloned successfully',
            'data' => [
                'plan' => $newPlan->load('features'),
            ],
        ]);
    }

    /**
     * Get plan pricing calculator data.
     */
    public function getPricingData(SubscriptionPlan $plan): JsonResponse
    {
        $features = Feature::active()->get();
        
        $pricingData = [
            'plan' => [
                'id' => $plan->id,
                'name' => $plan->name,
                'pricing_type' => $plan->pricing_type,
                'base_price' => $plan->base_price,
                'feature_price_monthly' => $plan->feature_price_monthly,
                'feature_price_yearly' => $plan->feature_price_yearly,
            ],
            'features' => $features->map(function ($feature) use ($plan) {
                $planFeature = $plan->features->where('key', $feature->key)->first();
                return [
                    'key' => $feature->key,
                    'name' => $feature->name,
                    'goal' => $feature->goal?->name,
                    'type' => $feature->type,
                    'is_included' => $planFeature?->pivot->is_included ?? false,
                    'quota_limit' => $planFeature?->pivot->quota_limit,
                ];
            }),
            'examples' => [
                '5_features' => [
                    'monthly' => $plan->calculateMonthlyPrice(['feat1', 'feat2', 'feat3', 'feat4', 'feat5']),
                    'yearly' => $plan->calculateYearlyPrice(['feat1', 'feat2', 'feat3', 'feat4', 'feat5']),
                ],
                '10_features' => [
                    'monthly' => $plan->calculateMonthlyPrice(range(1, 10)),
                    'yearly' => $plan->calculateYearlyPrice(range(1, 10)),
                ],
                '20_features' => [
                    'monthly' => $plan->calculateMonthlyPrice(range(1, 20)),
                    'yearly' => $plan->calculateYearlyPrice(range(1, 20)),
                ],
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $pricingData,
        ]);
    }
}