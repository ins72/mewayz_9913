<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WorkspaceGoal;
use App\Models\Feature;
use App\Models\SubscriptionPlan;
use App\Models\Workspace;
use App\Models\TeamInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WorkspaceSetupWizardController extends Controller
{
    /**
     * Get initial setup data for the wizard
     */
    public function getInitialData()
    {
        try {
            $user = auth()->user();
            $workspace = $user->workspaces()->first();
            
            // If no workspace exists, create a default one
            if (!$workspace) {
                $workspace = Workspace::create([
                    'user_id' => $user->id,
                    'name' => $user->name . "'s Workspace",
                    'is_primary' => true,
                    'setup_step' => 'goals',
                    'setup_completed' => false,
                ]);
            }
            
            $goals = WorkspaceGoal::active()->ordered()->get();
            $subscriptionPlans = SubscriptionPlan::active()->get();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'workspace' => $workspace,
                    'goals' => $goals,
                    'subscription_plans' => $subscriptionPlans,
                    'current_step' => $workspace->setup_step,
                    'setup_completed' => $workspace->setup_completed,
                    'progress' => $workspace->getSetupProgress(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load setup data: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Step 1: Save selected goals
     */
    public function saveGoals(Request $request)
    {
        try {
            $request->validate([
                'goals' => 'required|array|min:1|max:6',
                'goals.*' => 'required|string|exists:workspace_goals,slug',
            ]);
            
            $user = auth()->user();
            $workspace = $user->workspaces()->first();
            
            if (!$workspace) {
                return response()->json([
                    'success' => false,
                    'message' => 'No workspace found'
                ], 404);
            }
            
            $workspace->update([
                'selected_goals' => $request->goals,
                'setup_step' => 'features',
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Goals saved successfully',
                'data' => [
                    'workspace' => $workspace->fresh(),
                    'next_step' => 'features',
                    'progress' => $workspace->getSetupProgress(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save goals: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get features for selected goals
     */
    public function getFeatures(Request $request)
    {
        try {
            $user = auth()->user();
            $workspace = $user->workspaces()->first();
            
            if (!$workspace || !$workspace->selected_goals) {
                return response()->json([
                    'success' => false,
                    'message' => 'No goals selected'
                ], 400);
            }
            
            $features = Feature::active()
                ->where(function($query) use ($workspace) {
                    foreach ($workspace->selected_goals as $goal) {
                        $query->orWhereJsonContains('goals', $goal);
                    }
                })
                ->orderBy('sort_order')
                ->get();
            
            // Group features by category
            $featuresGrouped = $features->groupBy('category');
            
            return response()->json([
                'success' => true,
                'data' => [
                    'features' => $features,
                    'features_grouped' => $featuresGrouped,
                    'selected_goals' => $workspace->selected_goals,
                    'workspace' => $workspace,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load features: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Step 2: Save selected features
     */
    public function saveFeatures(Request $request)
    {
        try {
            $request->validate([
                'features' => 'required|array|min:1',
                'features.*' => 'required|integer|exists:features,id',
            ]);
            
            $user = auth()->user();
            $workspace = $user->workspaces()->first();
            
            if (!$workspace) {
                return response()->json([
                    'success' => false,
                    'message' => 'No workspace found'
                ], 404);
            }
            
            $workspace->update([
                'selected_features' => $request->features,
                'setup_step' => 'team',
            ]);
            
            // Enable selected features in workspace
            foreach ($request->features as $featureId) {
                $workspace->enableFeature($featureId);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Features saved successfully',
                'data' => [
                    'workspace' => $workspace->fresh(),
                    'next_step' => 'team',
                    'progress' => $workspace->getSetupProgress(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save features: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Step 3: Save team setup
     */
    public function saveTeamSetup(Request $request)
    {
        try {
            $request->validate([
                'team_members' => 'sometimes|array',
                'team_members.*.email' => 'required|email',
                'team_members.*.role' => 'required|string|in:member,editor,manager,admin',
                'team_members.*.permissions' => 'sometimes|array',
                'skip_team_setup' => 'sometimes|boolean',
            ]);
            
            $user = auth()->user();
            $workspace = $user->workspaces()->first();
            
            if (!$workspace) {
                return response()->json([
                    'success' => false,
                    'message' => 'No workspace found'
                ], 404);
            }
            
            $teamSetup = [
                'invitations_sent' => 0,
                'team_size' => 1, // Owner
                'roles_configured' => true,
            ];
            
            if (!($request->skip_team_setup ?? false) && !empty($request->team_members)) {
                $invitations = [];
                
                foreach ($request->team_members as $member) {
                    $invitation = TeamInvitation::create([
                        'workspace_id' => $workspace->id,
                        'invited_by' => $user->id,
                        'email' => $member['email'],
                        'role' => $member['role'],
                        'permissions' => $member['permissions'] ?? [],
                        'expires_at' => now()->addDays(7),
                    ]);
                    
                    $invitations[] = $invitation;
                }
                
                $teamSetup['invitations_sent'] = count($invitations);
                $teamSetup['team_size'] = 1 + count($invitations);
                $teamSetup['invitations'] = $invitations;
            }
            
            $workspace->update([
                'team_setup' => $teamSetup,
                'setup_step' => 'subscription',
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Team setup saved successfully',
                'data' => [
                    'workspace' => $workspace->fresh(),
                    'next_step' => 'subscription',
                    'progress' => $workspace->getSetupProgress(),
                    'team_setup' => $teamSetup,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save team setup: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Calculate pricing for selected features and plan
     */
    public function calculatePricing(Request $request)
    {
        try {
            $request->validate([
                'plan_id' => 'required|integer|exists:subscription_plans,id',
                'billing_interval' => 'required|string|in:monthly,yearly',
                'features' => 'sometimes|array', // Allow features to be passed directly
                'features.*' => 'integer|exists:features,id',
            ]);
            
            $user = auth()->user();
            $workspace = $user->workspaces()->first();
            $plan = SubscriptionPlan::find($request->plan_id);
            
            if (!$workspace) {
                return response()->json([
                    'success' => false,
                    'message' => 'No workspace found'
                ], 404);
            }
            
            // Use features from request if provided, otherwise use workspace selected features
            $selectedFeatures = $request->features ?? $workspace->selected_features ?? [];
            
            if (empty($selectedFeatures)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No features selected'
                ], 400);
            }
            
            $features = Feature::whereIn('id', $selectedFeatures)->get();
            $totalPrice = $plan->calculatePrice($selectedFeatures, $request->billing_interval);
            
            // Calculate savings for yearly billing
            $monthlyPrice = $plan->calculatePrice($selectedFeatures, 'monthly');
            $yearlyPrice = $plan->calculatePrice($selectedFeatures, 'yearly');
            $yearlyMonthlySavings = ($monthlyPrice * 12) - $yearlyPrice;
            
            return response()->json([
                'success' => true,
                'data' => [
                    'plan' => $plan,
                    'features' => $features,
                    'feature_count' => count($selectedFeatures),
                    'billing_interval' => $request->billing_interval,
                    'pricing' => [
                        'base_price' => $plan->base_price,
                        'feature_price' => $request->billing_interval === 'yearly' ? $plan->feature_price_yearly : $plan->feature_price_monthly,
                        'total_price' => $totalPrice,
                        'monthly_equivalent' => $request->billing_interval === 'yearly' ? $totalPrice / 12 : $totalPrice,
                        'yearly_savings' => $yearlyMonthlySavings,
                    ],
                    'plan_features' => [
                        'max_features' => $plan->max_features,
                        'has_branding' => $plan->has_branding,
                        'has_priority_support' => $plan->has_priority_support,
                        'has_custom_domain' => $plan->has_custom_domain,
                        'has_api_access' => $plan->has_api_access,
                    ],
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate pricing: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Step 4: Save subscription plan
     */
    public function saveSubscription(Request $request)
    {
        try {
            $request->validate([
                'plan_id' => 'required|integer|exists:subscription_plans,id',
                'billing_interval' => 'required|string|in:monthly,yearly',
            ]);
            
            $user = auth()->user();
            $workspace = $user->workspaces()->first();
            $plan = SubscriptionPlan::find($request->plan_id);
            
            if (!$workspace) {
                return response()->json([
                    'success' => false,
                    'message' => 'No workspace found'
                ], 404);
            }
            
            // Check if plan allows the selected feature count
            if (!$plan->allowsFeatureCount(count($workspace->selected_features ?? []))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected plan does not allow ' . count($workspace->selected_features) . ' features'
                ], 400);
            }
            
            $workspace->update([
                'subscription_plan_id' => $plan->id,
                'setup_step' => 'branding',
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Subscription plan saved successfully',
                'data' => [
                    'workspace' => $workspace->fresh()->load('subscriptionPlan'),
                    'next_step' => 'branding',
                    'progress' => $workspace->getSetupProgress(),
                    'billing_interval' => $request->billing_interval,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save subscription: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Step 5: Save branding configuration
     */
    public function saveBranding(Request $request)
    {
        try {
            $request->validate([
                'logo' => 'sometimes|string',
                'primary_color' => 'sometimes|string',
                'secondary_color' => 'sometimes|string',
                'workspace_name' => 'sometimes|string|max:255',
                'custom_domain' => 'sometimes|string|max:255',
                'skip_branding' => 'sometimes|boolean',
            ]);
            
            $user = auth()->user();
            $workspace = $user->workspaces()->first();
            
            if (!$workspace) {
                return response()->json([
                    'success' => false,
                    'message' => 'No workspace found'
                ], 404);
            }
            
            $brandingConfig = [];
            
            if (!($request->skip_branding ?? false)) {
                $brandingConfig = [
                    'logo' => $request->logo,
                    'primary_color' => $request->primary_color ?? '#3B82F6',
                    'secondary_color' => $request->secondary_color ?? '#10B981',
                    'custom_domain' => $request->custom_domain,
                    'branding_enabled' => true,
                ];
            }
            
            $updateData = [
                'branding_config' => $brandingConfig,
                'setup_step' => 'complete',
                'setup_completed' => true,
                'setup_completed_at' => now(),
            ];
            
            if ($request->workspace_name) {
                $updateData['name'] = $request->workspace_name;
            }
            
            $workspace->update($updateData);
            
            return response()->json([
                'success' => true,
                'message' => 'Workspace setup completed successfully!',
                'data' => [
                    'workspace' => $workspace->fresh()->load('subscriptionPlan'),
                    'next_step' => 'complete',
                    'progress' => 100,
                    'setup_completed' => true,
                    'branding_config' => $brandingConfig,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save branding: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get current workspace setup status
     */
    public function getSetupStatus()
    {
        try {
            $user = auth()->user();
            $workspace = $user->workspaces()->first();
            
            if (!$workspace) {
                return response()->json([
                    'success' => false,
                    'message' => 'No workspace found'
                ], 404);
            }
            
            $data = [
                'workspace' => $workspace->load('subscriptionPlan'),
                'current_step' => $workspace->setup_step,
                'setup_completed' => $workspace->setup_completed,
                'progress' => $workspace->getSetupProgress(),
            ];
            
            // Add step-specific data
            if ($workspace->selected_goals) {
                $data['selected_goals'] = WorkspaceGoal::whereIn('slug', $workspace->selected_goals)->get();
            }
            
            if ($workspace->selected_features) {
                $data['selected_features'] = Feature::whereIn('id', $workspace->selected_features)->get();
            }
            
            if ($workspace->team_setup) {
                $data['team_setup'] = $workspace->team_setup;
                $data['pending_invitations'] = $workspace->pendingInvitations()->count();
            }
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get setup status: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Reset workspace setup (for testing)
     */
    public function resetSetup()
    {
        try {
            $user = auth()->user();
            $workspace = $user->workspaces()->first();
            
            if (!$workspace) {
                return response()->json([
                    'success' => false,
                    'message' => 'No workspace found'
                ], 404);
            }
            
            $workspace->update([
                'selected_goals' => null,
                'selected_features' => null,
                'team_setup' => null,
                'subscription_plan_id' => null,
                'branding_config' => null,
                'setup_step' => 'goals',
                'setup_completed' => false,
                'setup_completed_at' => null,
            ]);
            
            // Disable all features
            $workspace->workspaceFeatures()->delete();
            
            // Delete pending invitations
            $workspace->teamInvitations()->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Workspace setup reset successfully',
                'data' => [
                    'workspace' => $workspace->fresh(),
                    'current_step' => 'goals',
                    'progress' => 0,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset setup: ' . $e->getMessage()
            ], 500);
        }
    }
}
