<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\PaymentTransaction;
use App\Models\PricingPlan;

class RealTimeSubscriptionController extends Controller
{
    /**
     * Get real-time subscription plans with live pricing
     */
    public function getPlans(Request $request)
    {
        try {
            // Cache key for plans
            $cacheKey = 'subscription_plans_' . $request->input('currency', 'USD');
            
            $plans = Cache::remember($cacheKey, 300, function () use ($request) {
                return SubscriptionPlan::where('is_active', true)
                    ->orderBy('base_price')
                    ->get()
                    ->map(function ($plan) {
                        return [
                            'id' => $plan->id,
                            'name' => $plan->name,
                            'slug' => $plan->slug,
                            'description' => $plan->description,
                            'base_price' => $plan->base_price,
                            'feature_price_monthly' => $plan->feature_price_monthly,
                            'feature_price_yearly' => $plan->feature_price_yearly,
                            'max_features' => $plan->max_features,
                            'has_branding' => $plan->has_branding,
                            'has_priority_support' => $plan->has_priority_support,
                            'has_custom_domain' => $plan->has_custom_domain,
                            'has_api_access' => $plan->has_api_access,
                            'included_features' => $plan->included_features,
                            'type' => $plan->type,
                            'is_popular' => $plan->type === 'professional',
                            'yearly_discount' => 0.20, // 20% discount for yearly
                            'pricing' => [
                                'monthly' => $plan->base_price,
                                'yearly' => round($plan->base_price * 12 * 0.8, 2),
                                'currency' => 'USD'
                            ]
                        ];
                    });
            });

            return response()->json([
                'success' => true,
                'plans' => $plans,
                'timestamp' => now()->toISOString(),
                'cache_key' => $cacheKey
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get subscription plans: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to retrieve subscription plans'
            ], 500);
        }
    }

    /**
     * Update subscription plan (Admin only)
     */
    public function updatePlan(Request $request, $planId)
    {
        try {
            $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
                'base_price' => 'sometimes|numeric|min:0',
                'feature_price_monthly' => 'sometimes|numeric|min:0',
                'feature_price_yearly' => 'sometimes|numeric|min:0',
                'max_features' => 'sometimes|integer|min:0',
                'has_branding' => 'sometimes|boolean',
                'has_priority_support' => 'sometimes|boolean',
                'has_custom_domain' => 'sometimes|boolean',
                'has_api_access' => 'sometimes|boolean',
                'included_features' => 'sometimes|array',
                'is_active' => 'sometimes|boolean'
            ]);

            $plan = SubscriptionPlan::findOrFail($planId);
            $oldPlan = $plan->toArray();

            $plan->update($request->only([
                'name', 'description', 'base_price', 'feature_price_monthly', 
                'feature_price_yearly', 'max_features', 'has_branding', 
                'has_priority_support', 'has_custom_domain', 'has_api_access', 
                'included_features', 'is_active'
            ]));

            // Clear cache for all currencies
            Cache::forget('subscription_plans_USD');
            Cache::forget('subscription_plans_EUR');
            Cache::forget('subscription_plans_GBP');

            // Broadcast real-time update to all connected users
            $this->broadcastPlanUpdate($plan, $oldPlan);

            // Update existing user subscriptions if pricing changed
            if ($request->has('base_price') || $request->has('feature_price_monthly') || $request->has('feature_price_yearly')) {
                $this->updateExistingSubscriptions($plan, $oldPlan);
            }

            Log::info('Subscription plan updated', [
                'plan_id' => $planId,
                'changes' => $request->only([
                    'name', 'description', 'base_price', 'feature_price_monthly', 
                    'feature_price_yearly', 'max_features', 'has_branding', 
                    'has_priority_support', 'has_custom_domain', 'has_api_access', 
                    'included_features', 'is_active'
                ])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Subscription plan updated successfully',
                'plan' => $plan,
                'changes_applied' => true
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update subscription plan: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to update subscription plan'
            ], 500);
        }
    }

    /**
     * Create new subscription plan (Admin only)
     */
    public function createPlan(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:subscription_plans,slug',
                'description' => 'required|string',
                'base_price' => 'required|numeric|min:0',
                'feature_price_monthly' => 'required|numeric|min:0',
                'feature_price_yearly' => 'required|numeric|min:0',
                'max_features' => 'nullable|integer|min:0',
                'type' => 'required|in:free,professional,enterprise',
                'has_branding' => 'boolean',
                'has_priority_support' => 'boolean',
                'has_custom_domain' => 'boolean',
                'has_api_access' => 'boolean',
                'included_features' => 'array'
            ]);

            $plan = SubscriptionPlan::create($request->all());

            // Clear cache
            Cache::forget('subscription_plans_USD');
            Cache::forget('subscription_plans_EUR');
            Cache::forget('subscription_plans_GBP');

            // Broadcast new plan to all users
            $this->broadcastNewPlan($plan);

            Log::info('New subscription plan created', [
                'plan_id' => $plan->id,
                'name' => $plan->name,
                'price' => $plan->base_price
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Subscription plan created successfully',
                'plan' => $plan
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to create subscription plan: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to create subscription plan'
            ], 500);
        }
    }

    /**
     * Delete subscription plan (Admin only)
     */
    public function deletePlan(Request $request, $planId)
    {
        try {
            $plan = SubscriptionPlan::findOrFail($planId);

            // Check if plan has active subscriptions
            $activeSubscriptions = DB::table('user_subscriptions')
                ->where('plan_id', $planId)
                ->where('status', 'active')
                ->count();

            if ($activeSubscriptions > 0) {
                return response()->json([
                    'error' => 'Cannot delete plan with active subscriptions',
                    'active_subscriptions' => $activeSubscriptions
                ], 400);
            }

            $plan->delete();

            // Clear cache
            Cache::forget('subscription_plans_USD');
            Cache::forget('subscription_plans_EUR');
            Cache::forget('subscription_plans_GBP');

            // Broadcast plan deletion
            $this->broadcastPlanDeletion($plan);

            Log::info('Subscription plan deleted', [
                'plan_id' => $planId,
                'name' => $plan->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Subscription plan deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to delete subscription plan: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to delete subscription plan'
            ], 500);
        }
    }

    /**
     * Get user's current subscription with real-time data
     */
    public function getCurrentSubscription(Request $request)
    {
        try {
            $user = $request->user();
            
            $subscription = DB::table('user_subscriptions')
                ->join('subscription_plans', 'user_subscriptions.plan_id', '=', 'subscription_plans.id')
                ->where('user_subscriptions.user_id', $user->id)
                ->where('user_subscriptions.status', 'active')
                ->select(
                    'user_subscriptions.*',
                    'subscription_plans.name as plan_name',
                    'subscription_plans.slug as plan_slug',
                    'subscription_plans.base_price',
                    'subscription_plans.feature_price_monthly',
                    'subscription_plans.feature_price_yearly',
                    'subscription_plans.type',
                    'subscription_plans.included_features'
                )
                ->first();

            if (!$subscription) {
                return response()->json([
                    'success' => true,
                    'subscription' => null,
                    'has_active_subscription' => false
                ]);
            }

            // Calculate usage and billing info
            $usage = $this->calculateUsage($user->id);
            $nextBilling = $this->calculateNextBilling($subscription);

            return response()->json([
                'success' => true,
                'subscription' => [
                    'id' => $subscription->id,
                    'plan' => [
                        'name' => $subscription->plan_name,
                        'slug' => $subscription->plan_slug,
                        'type' => $subscription->type,
                        'base_price' => $subscription->base_price,
                        'feature_price_monthly' => $subscription->feature_price_monthly,
                        'feature_price_yearly' => $subscription->feature_price_yearly,
                        'included_features' => json_decode($subscription->included_features, true)
                    ],
                    'status' => $subscription->status,
                    'amount' => $subscription->amount,
                    'billing_cycle' => $subscription->billing_cycle,
                    'current_period_start' => $subscription->current_period_start,
                    'current_period_end' => $subscription->current_period_end,
                    'next_billing_date' => $subscription->next_billing_date,
                    'cancel_at_period_end' => $subscription->cancel_at_period_end,
                    'usage' => $usage,
                    'next_billing' => $nextBilling
                ],
                'has_active_subscription' => true
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get current subscription: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to retrieve subscription information'
            ], 500);
        }
    }

    /**
     * Calculate user usage
     */
    private function calculateUsage($userId)
    {
        return [
            'bio_sites' => DB::table('bio_sites')->where('user_id', $userId)->count(),
            'workspaces' => DB::table('workspaces')->where('user_id', $userId)->count(),
            'email_campaigns' => DB::table('email_campaigns')->where('user_id', $userId)->count(),
            'booking_services' => DB::table('booking_services')->where('user_id', $userId)->count(),
            'storage_used' => 0, // TODO: Implement storage calculation
            'api_calls_this_month' => 0, // TODO: Implement API call tracking
        ];
    }

    /**
     * Calculate next billing information
     */
    private function calculateNextBilling($subscription)
    {
        if ($subscription->cancel_at_period_end) {
            return [
                'next_amount' => 0,
                'next_date' => null,
                'billing_status' => 'cancelled'
            ];
        }

        return [
            'next_amount' => $subscription->amount,
            'next_date' => $subscription->next_billing_date,
            'billing_status' => 'active'
        ];
    }

    /**
     * Broadcast plan update to all users
     */
    private function broadcastPlanUpdate($plan, $oldPlan)
    {
        // This would typically use Laravel Broadcasting
        // For now, we'll implement a simple cache-based approach
        $updateData = [
            'type' => 'plan_updated',
            'plan_id' => $plan->id,
            'changes' => array_diff_assoc($plan->toArray(), $oldPlan),
            'timestamp' => now()->toISOString()
        ];

        Cache::put('plan_update_' . $plan->id, $updateData, 3600);
    }

    /**
     * Broadcast new plan to all users
     */
    private function broadcastNewPlan($plan)
    {
        $updateData = [
            'type' => 'plan_created',
            'plan' => $plan->toArray(),
            'timestamp' => now()->toISOString()
        ];

        Cache::put('plan_created_' . $plan->id, $updateData, 3600);
    }

    /**
     * Broadcast plan deletion to all users
     */
    private function broadcastPlanDeletion($plan)
    {
        $updateData = [
            'type' => 'plan_deleted',
            'plan_id' => $plan->id,
            'timestamp' => now()->toISOString()
        ];

        Cache::put('plan_deleted_' . $plan->id, $updateData, 3600);
    }

    /**
     * Update existing subscriptions when plan changes
     */
    private function updateExistingSubscriptions($plan, $oldPlan)
    {
        try {
            $subscriptions = DB::table('user_subscriptions')
                ->where('plan_id', $plan->id)
                ->where('status', 'active')
                ->get();

            foreach ($subscriptions as $subscription) {
                // If price increased, notify user but don't change immediately
                if ($plan->base_price > $oldPlan['base_price']) {
                    // Schedule price change for next billing cycle
                    DB::table('user_subscriptions')
                        ->where('id', $subscription->id)
                        ->update([
                            'metadata' => json_encode([
                                'price_change_scheduled' => true,
                                'old_price' => $oldPlan['base_price'],
                                'new_price' => $plan->base_price,
                                'effective_date' => $subscription->next_billing_date
                            ])
                        ]);
                } else {
                    // If price decreased, apply immediately
                    DB::table('user_subscriptions')
                        ->where('id', $subscription->id)
                        ->update([
                            'amount' => $plan->base_price,
                            'updated_at' => now()
                        ]);
                }
            }

        } catch (\Exception $e) {
            Log::error('Failed to update existing subscriptions: ' . $e->getMessage());
        }
    }

    /**
     * Get real-time plan updates
     */
    public function getPlanUpdates(Request $request)
    {
        try {
            $since = $request->input('since', now()->subHours(1)->toISOString());
            $updates = [];

            // Get all cached updates since the specified time
            $cacheKeys = Cache::get('plan_update_keys', []);
            
            foreach ($cacheKeys as $key) {
                $update = Cache::get($key);
                if ($update && $update['timestamp'] > $since) {
                    $updates[] = $update;
                }
            }

            return response()->json([
                'success' => true,
                'updates' => $updates,
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get plan updates: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to retrieve plan updates'
            ], 500);
        }
    }
}