<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use App\Models\WorkspaceGoal;
use App\Models\Feature;
use App\Models\SubscriptionPlan;
use App\Models\WorkspaceFeature;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class WorkspaceSetupController extends Controller
{
    /**
     * Show the workspace setup wizard
     */
    public function index(Request $request)
    {
        $step = $request->get('step', 1);
        $workspaceId = $request->get('workspace_id');
        
        // Get current workspace if editing
        $workspace = null;
        if ($workspaceId) {
            $workspace = Workspace::where('id', $workspaceId)
                ->where('user_id', Auth::id())
                ->first();
        }

        // Get goals and features
        $goals = WorkspaceGoal::orderBy('sort_order')->get();
        $features = Feature::orderBy('sort_order')->get();
        $subscriptionPlans = SubscriptionPlan::where('is_active', true)->orderBy('id')->get();

        return view('workspace-setup.index', compact('step', 'workspace', 'goals', 'features', 'subscriptionPlans'));
    }

    /**
     * Process step 1: Workspace information
     */
    public function processStep1(Request $request)
    {
        $request->validate([
            'workspace_name' => 'required|string|max:255',
            'workspace_description' => 'nullable|string|max:1000',
            'workspace_industry' => 'nullable|string|max:100'
        ]);

        // Create or update workspace
        $workspace = Workspace::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'is_setup_complete' => false
            ],
            [
                'name' => $request->workspace_name,
                'description' => $request->workspace_description,
                'industry' => $request->workspace_industry,
                'slug' => Str::slug($request->workspace_name),
                'settings' => json_encode([
                    'setup_step' => 1,
                    'created_at' => now(),
                    'industry' => $request->workspace_industry
                ])
            ]
        );

        return response()->json([
            'success' => true,
            'workspace_id' => $workspace->id,
            'next_step' => 2
        ]);
    }

    /**
     * Process step 2: Goal selection
     */
    public function processStep2(Request $request)
    {
        $request->validate([
            'workspace_id' => 'required|exists:workspaces,id',
            'selected_goals' => 'required|array|min:1',
            'selected_goals.*' => 'exists:workspace_goals,id'
        ]);

        $workspace = Workspace::where('id', $request->workspace_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Store selected goals
        $selectedGoals = WorkspaceGoal::whereIn('id', $request->selected_goals)->get();
        
        $workspace->update([
            'selected_goals' => json_encode($request->selected_goals),
            'settings' => json_encode(array_merge(
                json_decode($workspace->settings, true) ?? [],
                [
                    'setup_step' => 2,
                    'selected_goals' => $selectedGoals->pluck('slug')->toArray()
                ]
            ))
        ]);

        // Get features for selected goals
        $availableFeatures = Feature::whereJsonContains('goals', $request->selected_goals)
            ->orWhereJsonContains('goals', array_map('intval', $request->selected_goals))
            ->get();

        return response()->json([
            'success' => true,
            'next_step' => 3,
            'available_features' => $availableFeatures
        ]);
    }

    /**
     * Process step 3: Feature selection
     */
    public function processStep3(Request $request)
    {
        $request->validate([
            'workspace_id' => 'required|exists:workspaces,id',
            'selected_features' => 'required|array|min:1',
            'selected_features.*' => 'exists:features,id'
        ]);

        $workspace = Workspace::where('id', $request->workspace_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Get selected features with pricing
        $selectedFeatures = Feature::whereIn('id', $request->selected_features)->get();
        
        // Calculate total cost
        $monthlyTotal = $selectedFeatures->sum('monthly_price');
        $yearlyTotal = $selectedFeatures->sum('yearly_price');
        $freeFeatures = $selectedFeatures->where('is_free', true)->count();

        // Store selected features
        $workspace->update([
            'selected_features' => json_encode($request->selected_features),
            'settings' => json_encode(array_merge(
                json_decode($workspace->settings, true) ?? [],
                [
                    'setup_step' => 3,
                    'selected_features' => $selectedFeatures->pluck('slug')->toArray(),
                    'pricing' => [
                        'monthly_total' => $monthlyTotal,
                        'yearly_total' => $yearlyTotal,
                        'feature_count' => $selectedFeatures->count(),
                        'free_features' => $freeFeatures
                    ]
                ]
            ))
        ]);

        return response()->json([
            'success' => true,
            'next_step' => 4,
            'pricing' => [
                'monthly_total' => $monthlyTotal,
                'yearly_total' => $yearlyTotal,
                'feature_count' => $selectedFeatures->count(),
                'free_features' => $freeFeatures
            ]
        ]);
    }

    /**
     * Process step 4: Subscription plan selection
     */
    public function processStep4(Request $request)
    {
        $request->validate([
            'workspace_id' => 'required|exists:workspaces,id',
            'selected_plan' => 'required|exists:subscription_plans,id',
            'billing_cycle' => 'required|in:monthly,yearly'
        ]);

        $workspace = Workspace::where('id', $request->workspace_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $plan = SubscriptionPlan::findOrFail($request->selected_plan);
        $selectedFeatures = Feature::whereIn('id', json_decode($workspace->selected_features, true) ?? [])->get();

        // Calculate final pricing
        $finalPricing = $this->calculateFinalPricing($plan, $selectedFeatures, $request->billing_cycle);

        // Store subscription plan
        $workspace->update([
            'subscription_plan_id' => $plan->id,
            'billing_cycle' => $request->billing_cycle,
            'settings' => json_encode(array_merge(
                json_decode($workspace->settings, true) ?? [],
                [
                    'setup_step' => 4,
                    'subscription_plan' => $plan->slug,
                    'billing_cycle' => $request->billing_cycle,
                    'final_pricing' => $finalPricing
                ]
            ))
        ]);

        return response()->json([
            'success' => true,
            'next_step' => 5,
            'final_pricing' => $finalPricing
        ]);
    }

    /**
     * Process step 5: Team invitations
     */
    public function processStep5(Request $request)
    {
        $request->validate([
            'workspace_id' => 'required|exists:workspaces,id',
            'team_members' => 'nullable|array',
            'team_members.*.email' => 'required_with:team_members|email',
            'team_members.*.role' => 'required_with:team_members|in:admin,editor,viewer'
        ]);

        $workspace = Workspace::where('id', $request->workspace_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        DB::transaction(function() use ($request, $workspace) {
            // Send team invitations
            if ($request->team_members) {
                foreach ($request->team_members as $member) {
                    if (!empty($member['email'])) {
                        $this->sendTeamInvitation($workspace, $member['email'], $member['role']);
                    }
                }
            }

            // Enable selected features
            $this->enableWorkspaceFeatures($workspace);

            // Mark setup as complete
            $workspace->update([
                'is_setup_complete' => true,
                'settings' => json_encode(array_merge(
                    json_decode($workspace->settings, true) ?? [],
                    [
                        'setup_step' => 5,
                        'setup_completed_at' => now(),
                        'team_invitations_sent' => count($request->team_members ?? [])
                    ]
                ))
            ]);
        });

        return response()->json([
            'success' => true,
            'workspace_id' => $workspace->id,
            'redirect_url' => route('dashboard-index', ['workspace' => $workspace->slug])
        ]);
    }

    /**
     * Calculate final pricing based on plan and features
     */
    private function calculateFinalPricing($plan, $selectedFeatures, $billingCycle)
    {
        $featureCount = $selectedFeatures->count();
        $freeFeatures = $selectedFeatures->where('is_free', true)->count();
        $paidFeatures = $featureCount - $freeFeatures;

        if ($plan->slug === 'free') {
            return [
                'base_price' => 0,
                'feature_price' => 0,
                'total_price' => 0,
                'billing_cycle' => $billingCycle,
                'features_included' => min($featureCount, $plan->max_features ?? 10)
            ];
        }

        $featurePrice = $billingCycle === 'yearly' ? $plan->feature_price_yearly : $plan->feature_price_monthly;
        $totalPrice = $paidFeatures * $featurePrice;

        return [
            'base_price' => $plan->base_price,
            'feature_price' => $featurePrice,
            'total_price' => $totalPrice,
            'billing_cycle' => $billingCycle,
            'features_included' => $featureCount,
            'paid_features' => $paidFeatures,
            'free_features' => $freeFeatures
        ];
    }

    /**
     * Send team invitation
     */
    private function sendTeamInvitation($workspace, $email, $role)
    {
        // Check if user already exists
        $existingUser = User::where('email', $email)->first();
        
        $invitation = TeamInvitation::create([
            'workspace_id' => $workspace->id,
            'email' => $email,
            'role' => $role,
            'invited_by' => Auth::id(),
            'token' => Str::random(32),
            'expires_at' => now()->addDays(7)
        ]);

        // Send invitation email
        // Mail::to($email)->send(new TeamInvitationMail($invitation, $workspace));

        return $invitation;
    }

    /**
     * Enable workspace features
     */
    private function enableWorkspaceFeatures($workspace)
    {
        $selectedFeatures = json_decode($workspace->selected_features, true) ?? [];
        
        foreach ($selectedFeatures as $featureId) {
            WorkspaceFeature::create([
                'workspace_id' => $workspace->id,
                'feature_id' => $featureId,
                'is_active' => true,
                'enabled_at' => now()
            ]);
        }
    }

    /**
     * Get workspace setup progress
     */
    public function getProgress($workspaceId)
    {
        $workspace = Workspace::where('id', $workspaceId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $settings = json_decode($workspace->settings, true) ?? [];
        $currentStep = $settings['setup_step'] ?? 1;

        return response()->json([
            'current_step' => $currentStep,
            'is_complete' => $workspace->is_setup_complete,
            'settings' => $settings
        ]);
    }

    /**
     * Get available features for selected goals
     */
    public function getAvailableFeatures(Request $request)
    {
        $request->validate([
            'goal_ids' => 'required|array',
            'goal_ids.*' => 'exists:workspace_goals,id'
        ]);

        $features = Feature::where(function($query) use ($request) {
            foreach ($request->goal_ids as $goalId) {
                $query->orWhereJsonContains('goals', intval($goalId));
            }
        })
        ->orderBy('category')
        ->orderBy('sort_order')
        ->get()
        ->groupBy('category');

        return response()->json([
            'features' => $features
        ]);
    }

    /**
     * Calculate pricing for selected features
     */
    public function calculatePricing(Request $request)
    {
        $request->validate([
            'feature_ids' => 'required|array',
            'feature_ids.*' => 'exists:features,id',
            'billing_cycle' => 'required|in:monthly,yearly'
        ]);

        $features = Feature::whereIn('id', $request->feature_ids)->get();
        
        $monthlyTotal = $features->sum('monthly_price');
        $yearlyTotal = $features->sum('yearly_price');
        $freeFeatures = $features->where('is_free', true)->count();

        $total = $request->billing_cycle === 'yearly' ? $yearlyTotal : $monthlyTotal;

        return response()->json([
            'monthly_total' => $monthlyTotal,
            'yearly_total' => $yearlyTotal,
            'current_total' => $total,
            'billing_cycle' => $request->billing_cycle,
            'feature_count' => $features->count(),
            'free_features' => $freeFeatures,
            'paid_features' => $features->count() - $freeFeatures
        ]);
    }
}