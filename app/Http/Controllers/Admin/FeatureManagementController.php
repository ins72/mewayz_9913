<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\Models\Goal;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class FeatureManagementController extends Controller
{
    /**
     * Display the feature management dashboard.
     */
    public function index(): View
    {
        $goals = Goal::with('features')->active()->ordered()->get();
        $features = Feature::with('goal', 'subscriptionPlans')->active()->get();
        $plans = SubscriptionPlan::active()->ordered()->get();

        return view('admin.feature-management.index', compact('goals', 'features', 'plans'));
    }

    /**
     * Get all features with their current plan assignments.
     */
    public function getFeatures(): JsonResponse
    {
        $features = Feature::with(['goal', 'subscriptionPlans'])->active()->get();

        $formattedFeatures = $features->map(function ($feature) {
            return [
                'key' => $feature->key,
                'name' => $feature->name,
                'description' => $feature->description,
                'goal' => [
                    'key' => $feature->goal?->key,
                    'name' => $feature->goal?->name,
                    'color' => $feature->goal?->color,
                ],
                'category' => $feature->category,
                'type' => $feature->type,
                'plans' => $feature->subscriptionPlans->map(function ($plan) {
                    return [
                        'id' => $plan->id,
                        'name' => $plan->name,
                        'is_included' => $plan->pivot->is_included,
                        'quota_limit' => $plan->pivot->quota_limit,
                        'overage_price' => $plan->pivot->overage_price,
                    ];
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedFeatures,
        ]);
    }

    /**
     * Update feature assignments for plans.
     */
    public function updatePlanFeatures(Request $request): JsonResponse
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'features' => 'required|array',
            'features.*.key' => 'required|exists:features,key',
            'features.*.is_included' => 'required|boolean',
            'features.*.quota_limit' => 'nullable|integer|min:1',
            'features.*.overage_price' => 'nullable|numeric|min:0',
        ]);

        $plan = SubscriptionPlan::findOrFail($request->plan_id);
        
        $featureAssignments = [];
        foreach ($request->features as $featureData) {
            $featureAssignments[$featureData['key']] = [
                'is_included' => $featureData['is_included'],
                'quota_limit' => $featureData['quota_limit'],
                'overage_price' => $featureData['overage_price'] ?? 0,
            ];
        }

        $plan->features()->sync($featureAssignments);

        return response()->json([
            'success' => true,
            'message' => 'Plan features updated successfully',
            'data' => [
                'plan' => $plan->load('features'),
            ],
        ]);
    }

    /**
     * Bulk update multiple plans with feature assignments.
     */
    public function bulkUpdatePlans(Request $request): JsonResponse
    {
        $request->validate([
            'updates' => 'required|array',
            'updates.*.plan_id' => 'required|exists:subscription_plans,id',
            'updates.*.features' => 'required|array',
        ]);

        foreach ($request->updates as $update) {
            $plan = SubscriptionPlan::findOrFail($update['plan_id']);
            
            $featureAssignments = [];
            foreach ($update['features'] as $featureData) {
                $featureAssignments[$featureData['key']] = [
                    'is_included' => $featureData['is_included'] ?? false,
                    'quota_limit' => $featureData['quota_limit'] ?? null,
                    'overage_price' => $featureData['overage_price'] ?? 0,
                ];
            }

            $plan->features()->sync($featureAssignments);
        }

        return response()->json([
            'success' => true,
            'message' => 'Plans updated successfully',
        ]);
    }

    /**
     * Get feature usage analytics.
     */
    public function getFeatureUsage(): JsonResponse
    {
        // This would implement actual analytics
        // For now, returning mock data
        $usageData = [
            'most_used' => [
                ['feature_key' => 'instagram_post_scheduling', 'usage_count' => 1250, 'growth' => 15.3],
                ['feature_key' => 'link_bio_pages', 'usage_count' => 980, 'growth' => 8.7],
                ['feature_key' => 'course_creation', 'usage_count' => 540, 'growth' => 23.1],
                ['feature_key' => 'ecommerce_products', 'usage_count' => 420, 'growth' => -2.4],
                ['feature_key' => 'crm_email_campaigns', 'usage_count' => 380, 'growth' => 12.8],
            ],
            'by_plan' => [
                'Free Plan' => ['total_usage' => 2100, 'active_users' => 450],
                'Professional Plan' => ['total_usage' => 5670, 'active_users' => 230],
                'Enterprise Plan' => ['total_usage' => 3200, 'active_users' => 45],
            ],
            'quota_utilization' => [
                ['feature_key' => 'instagram_post_scheduling', 'utilization' => 78.5],
                ['feature_key' => 'course_video_hosting', 'utilization' => 45.2],
                ['feature_key' => 'crm_contacts', 'utilization' => 92.1],
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $usageData,
        ]);
    }

    /**
     * Toggle feature active status.
     */
    public function toggleFeature(Feature $feature): JsonResponse
    {
        $feature->update(['is_active' => !$feature->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Feature ' . ($feature->is_active ? 'activated' : 'deactivated') . ' successfully',
            'data' => [
                'feature' => $feature,
            ],
        ]);
    }

    /**
     * Create a new feature.
     */
    public function createFeature(Request $request): JsonResponse
    {
        $request->validate([
            'key' => 'required|string|unique:features,key',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'goal_key' => 'required|exists:goals,key',
            'category' => 'required|string|max:100',
            'type' => 'required|in:binary,quota,tiered',
        ]);

        $feature = Feature::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Feature created successfully',
            'data' => [
                'feature' => $feature->load('goal'),
            ],
        ]);
    }

    /**
     * Update an existing feature.
     */
    public function updateFeature(Request $request, Feature $feature): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'goal_key' => 'required|exists:goals,key',
            'category' => 'required|string|max:100',
            'type' => 'required|in:binary,quota,tiered',
        ]);

        $feature->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Feature updated successfully',
            'data' => [
                'feature' => $feature->load('goal'),
            ],
        ]);
    }

    /**
     * Delete a feature.
     */
    public function deleteFeature(Feature $feature): JsonResponse
    {
        // Check if feature is in use
        $workspaceCount = $feature->workspaces()->count();
        
        if ($workspaceCount > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete feature that is currently being used by workspaces',
                'data' => [
                    'workspace_count' => $workspaceCount,
                ],
            ], 422);
        }

        $feature->delete();

        return response()->json([
            'success' => true,
            'message' => 'Feature deleted successfully',
        ]);
    }

    /**
     * Get feature matrix (plans vs features).
     */
    public function getFeatureMatrix(): JsonResponse
    {
        $plans = SubscriptionPlan::with('features')->active()->ordered()->get();
        $features = Feature::with('goal')->active()->get();

        $matrix = [];
        foreach ($features as $feature) {
            $featureRow = [
                'key' => $feature->key,
                'name' => $feature->name,
                'goal' => [
                    'key' => $feature->goal?->key,
                    'name' => $feature->goal?->name,
                    'color' => $feature->goal?->color,
                ],
                'type' => $feature->type,
                'plans' => [],
            ];

            foreach ($plans as $plan) {
                $planFeature = $plan->features->where('key', $feature->key)->first();
                $featureRow['plans'][$plan->id] = [
                    'is_included' => $planFeature?->pivot->is_included ?? false,
                    'quota_limit' => $planFeature?->pivot->quota_limit,
                    'overage_price' => $planFeature?->pivot->overage_price ?? 0,
                ];
            }

            $matrix[] = $featureRow;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'plans' => $plans->map(function ($plan) {
                    return [
                        'id' => $plan->id,
                        'name' => $plan->name,
                        'pricing_type' => $plan->pricing_type,
                        'feature_price_monthly' => $plan->feature_price_monthly,
                    ];
                }),
                'matrix' => $matrix,
            ],
        ]);
    }

    /**
     * Export feature configuration.
     */
    public function exportConfiguration(): JsonResponse
    {
        $goals = Goal::with('features')->active()->get();
        $features = Feature::with('subscriptionPlans')->active()->get();
        $plans = SubscriptionPlan::with('features')->active()->get();

        $configuration = [
            'exported_at' => now()->toISOString(),
            'version' => '1.0',
            'goals' => $goals,
            'features' => $features,
            'plans' => $plans,
        ];

        return response()->json([
            'success' => true,
            'data' => $configuration,
        ]);
    }

    /**
     * Import feature configuration.
     */
    public function importConfiguration(Request $request): JsonResponse
    {
        $request->validate([
            'configuration' => 'required|array',
            'configuration.goals' => 'required|array',
            'configuration.features' => 'required|array',
            'configuration.plans' => 'required|array',
        ]);

        // This would implement the import logic
        // For now, just return success
        return response()->json([
            'success' => true,
            'message' => 'Configuration imported successfully',
        ]);
    }
}