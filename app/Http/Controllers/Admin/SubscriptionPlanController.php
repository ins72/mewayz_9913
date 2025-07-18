<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\SubscriptionPlan;
use App\Models\Admin\PlanFeature;
use App\Models\Admin\PlanFeatureAssignment;
use Illuminate\Support\Str;

class SubscriptionPlanController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:active,inactive,deprecated',
            'sort' => 'nullable|string|in:name,price,created_at,sort_order',
            'order' => 'nullable|string|in:asc,desc'
        ]);

        try {
            $perPage = $request->per_page ?? 25;
            $query = SubscriptionPlan::withCount('assignments');

            // Search
            if ($request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%");
                });
            }

            // Filter by status
            if ($request->status) {
                $query->where('status', $request->status);
            }

            // Sort
            $sortField = $request->sort ?? 'sort_order';
            $sortOrder = $request->order ?? 'asc';
            $query->orderBy($sortField, $sortOrder);

            $plans = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => [
                    'plans' => $plans->items(),
                    'pagination' => [
                        'current_page' => $plans->currentPage(),
                        'per_page' => $plans->perPage(),
                        'total' => $plans->total(),
                        'last_page' => $plans->lastPage(),
                        'from' => $plans->firstItem(),
                        'to' => $plans->lastItem()
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Subscription plans index failed', [
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load subscription plans'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'billing_cycle' => 'required|string|in:monthly,yearly,quarterly,weekly',
            'trial_days' => 'nullable|integer|min:0',
            'is_popular' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'status' => 'required|string|in:active,inactive,deprecated',
            'features' => 'nullable|array',
            'limits' => 'nullable|array',
            'restrictions' => 'nullable|array',
            'pricing_tiers' => 'nullable|array',
            'geographic_pricing' => 'nullable|array',
            'sort_order' => 'nullable|integer'
        ]);

        try {
            $planData = $request->validated();
            $planData['slug'] = Str::slug($planData['name']);
            
            // Ensure unique slug
            $originalSlug = $planData['slug'];
            $counter = 1;
            while (SubscriptionPlan::where('slug', $planData['slug'])->exists()) {
                $planData['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }

            $plan = SubscriptionPlan::create($planData);

            // Log the activity
            $request->user()->logActivity('create_subscription_plan', 'SubscriptionPlan', $plan->id, null, $planData);

            return response()->json([
                'success' => true,
                'message' => 'Subscription plan created successfully',
                'data' => $plan
            ]);

        } catch (\Exception $e) {
            Log::error('Create subscription plan failed', [
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create subscription plan'
            ], 500);
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $plan = SubscriptionPlan::with(['planFeatures', 'assignments.feature'])
                                   ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $plan
            ]);

        } catch (\Exception $e) {
            Log::error('Show subscription plan failed', [
                'error' => $e->getMessage(),
                'plan_id' => $id,
                'admin_user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load subscription plan'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'billing_cycle' => 'required|string|in:monthly,yearly,quarterly,weekly',
            'trial_days' => 'nullable|integer|min:0',
            'is_popular' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'status' => 'required|string|in:active,inactive,deprecated',
            'features' => 'nullable|array',
            'limits' => 'nullable|array',
            'restrictions' => 'nullable|array',
            'pricing_tiers' => 'nullable|array',
            'geographic_pricing' => 'nullable|array',
            'sort_order' => 'nullable|integer'
        ]);

        try {
            $plan = SubscriptionPlan::findOrFail($id);
            $oldValues = $plan->toArray();
            
            $planData = $request->validated();
            
            // Update slug if name changed
            if ($planData['name'] !== $plan->name) {
                $planData['slug'] = Str::slug($planData['name']);
                
                // Ensure unique slug
                $originalSlug = $planData['slug'];
                $counter = 1;
                while (SubscriptionPlan::where('slug', $planData['slug'])->where('id', '!=', $id)->exists()) {
                    $planData['slug'] = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }

            $plan->update($planData);

            // Log the activity
            $request->user()->logActivity('update_subscription_plan', 'SubscriptionPlan', $plan->id, $oldValues, $planData);

            return response()->json([
                'success' => true,
                'message' => 'Subscription plan updated successfully',
                'data' => $plan
            ]);

        } catch (\Exception $e) {
            Log::error('Update subscription plan failed', [
                'error' => $e->getMessage(),
                'plan_id' => $id,
                'admin_user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update subscription plan'
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $plan = SubscriptionPlan::findOrFail($id);
            $planData = $plan->toArray();

            // Check if plan has active subscriptions
            // This would need to be implemented based on your subscription model
            
            $plan->delete();

            // Log the activity
            $request->user()->logActivity('delete_subscription_plan', 'SubscriptionPlan', $id, $planData, null);

            return response()->json([
                'success' => true,
                'message' => 'Subscription plan deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Delete subscription plan failed', [
                'error' => $e->getMessage(),
                'plan_id' => $id,
                'admin_user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete subscription plan'
            ], 500);
        }
    }

    public function updateFeatures(Request $request, $id)
    {
        $request->validate([
            'features' => 'required|array',
            'features.*.feature_id' => 'required|exists:plan_features,id',
            'features.*.is_enabled' => 'required|boolean',
            'features.*.limits' => 'nullable|array',
            'features.*.config' => 'nullable|array'
        ]);

        try {
            $plan = SubscriptionPlan::findOrFail($id);
            
            DB::transaction(function () use ($plan, $request) {
                // Remove existing feature assignments
                $plan->assignments()->delete();
                
                // Add new feature assignments
                foreach ($request->features as $featureData) {
                    PlanFeatureAssignment::create([
                        'plan_id' => $plan->id,
                        'feature_id' => $featureData['feature_id'],
                        'is_enabled' => $featureData['is_enabled'],
                        'limits' => $featureData['limits'] ?? [],
                        'config' => $featureData['config'] ?? []
                    ]);
                }
            });

            // Log the activity
            $request->user()->logActivity('update_plan_features', 'SubscriptionPlan', $plan->id, null, $request->features);

            return response()->json([
                'success' => true,
                'message' => 'Plan features updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Update plan features failed', [
                'error' => $e->getMessage(),
                'plan_id' => $id,
                'admin_user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update plan features'
            ], 500);
        }
    }

    public function updatePricing(Request $request, $id)
    {
        $request->validate([
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'geographic_pricing' => 'nullable|array',
            'pricing_tiers' => 'nullable|array',
            'effective_date' => 'nullable|date|after:now'
        ]);

        try {
            $plan = SubscriptionPlan::findOrFail($id);
            $oldValues = $plan->only(['price', 'currency', 'geographic_pricing', 'pricing_tiers']);
            
            $pricingData = $request->only(['price', 'currency', 'geographic_pricing', 'pricing_tiers']);
            
            // If effective date is provided, schedule the pricing change
            if ($request->effective_date) {
                // In a real implementation, this would schedule a job
                // For now, we'll just update immediately
                $plan->update($pricingData);
            } else {
                $plan->update($pricingData);
            }

            // Log the activity
            $request->user()->logActivity('update_plan_pricing', 'SubscriptionPlan', $plan->id, $oldValues, $pricingData);

            return response()->json([
                'success' => true,
                'message' => 'Plan pricing updated successfully',
                'data' => $plan
            ]);

        } catch (\Exception $e) {
            Log::error('Update plan pricing failed', [
                'error' => $e->getMessage(),
                'plan_id' => $id,
                'admin_user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update plan pricing'
            ], 500);
        }
    }

    public function getComparison(Request $request)
    {
        $request->validate([
            'plan_ids' => 'required|array',
            'plan_ids.*' => 'exists:subscription_plans,id'
        ]);

        try {
            $plans = SubscriptionPlan::with(['assignments.feature'])
                                   ->whereIn('id', $request->plan_ids)
                                   ->get();

            $comparison = $plans->map(function ($plan) {
                return $plan->getComparisonData();
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'comparison' => $comparison,
                    'features' => $this->getAllFeatures()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Plan comparison failed', [
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate plan comparison'
            ], 500);
        }
    }

    public function getAnalytics(Request $request)
    {
        $request->validate([
            'period' => 'nullable|string|in:7d,30d,90d,1y,all_time'
        ]);

        try {
            $period = $request->period ?? '30d';
            $dateRange = $this->getDateRange($period);

            $analytics = [
                'overview' => [
                    'total_plans' => SubscriptionPlan::count(),
                    'active_plans' => SubscriptionPlan::where('status', 'active')->count(),
                    'deprecated_plans' => SubscriptionPlan::where('status', 'deprecated')->count(),
                    'featured_plans' => SubscriptionPlan::where('is_featured', true)->count()
                ],
                'pricing_analysis' => [
                    'average_price' => SubscriptionPlan::where('status', 'active')->avg('price'),
                    'price_range' => [
                        'min' => SubscriptionPlan::where('status', 'active')->min('price'),
                        'max' => SubscriptionPlan::where('status', 'active')->max('price')
                    ],
                    'pricing_distribution' => $this->getPricingDistribution()
                ],
                'feature_usage' => [
                    'most_used_features' => $this->getMostUsedFeatures(),
                    'feature_adoption' => $this->getFeatureAdoption()
                ],
                'billing_cycles' => SubscriptionPlan::select('billing_cycle', DB::raw('COUNT(*) as count'))
                                                  ->groupBy('billing_cycle')
                                                  ->get()
                                                  ->pluck('count', 'billing_cycle')
            ];

            return response()->json([
                'success' => true,
                'data' => $analytics
            ]);

        } catch (\Exception $e) {
            Log::error('Plan analytics failed', [
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate plan analytics'
            ], 500);
        }
    }

    private function getAllFeatures()
    {
        return PlanFeature::active()->orderBy('category')->orderBy('sort_order')->get();
    }

    private function getPricingDistribution()
    {
        $ranges = [
            '0-10' => ['min' => 0, 'max' => 10],
            '11-50' => ['min' => 11, 'max' => 50],
            '51-100' => ['min' => 51, 'max' => 100],
            '101-500' => ['min' => 101, 'max' => 500],
            '500+' => ['min' => 501, 'max' => 999999]
        ];

        $distribution = [];
        foreach ($ranges as $label => $range) {
            $count = SubscriptionPlan::where('status', 'active')
                                   ->whereBetween('price', [$range['min'], $range['max']])
                                   ->count();
            $distribution[$label] = $count;
        }

        return $distribution;
    }

    private function getMostUsedFeatures()
    {
        return PlanFeature::withCount(['assignments' => function ($query) {
                              $query->where('is_enabled', true);
                          }])
                          ->orderBy('assignments_count', 'desc')
                          ->limit(10)
                          ->get();
    }

    private function getFeatureAdoption()
    {
        $totalPlans = SubscriptionPlan::where('status', 'active')->count();
        
        return PlanFeature::withCount(['assignments' => function ($query) {
                              $query->where('is_enabled', true);
                          }])
                          ->get()
                          ->map(function ($feature) use ($totalPlans) {
                              return [
                                  'feature' => $feature->name,
                                  'adoption_rate' => $totalPlans > 0 ? 
                                      ($feature->assignments_count / $totalPlans) * 100 : 0
                              ];
                          });
    }

    private function getDateRange($period): array
    {
        switch ($period) {
            case '7d':
                return [now()->subDays(7), now()];
            case '30d':
                return [now()->subDays(30), now()];
            case '90d':
                return [now()->subDays(90), now()];
            case '1y':
                return [now()->subYear(), now()];
            case 'all_time':
                return [now()->subYears(10), now()];
            default:
                return [now()->subDays(30), now()];
        }
    }
}