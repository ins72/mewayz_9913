<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Workspace;
use App\Models\OnboardingProgress;
use App\Models\UserPreference;
use App\Services\OnboardingService;
use App\Services\AnalyticsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OnboardingController extends Controller
{
    protected $onboardingService;
    protected $analyticsService;

    public function __construct(OnboardingService $onboardingService, AnalyticsService $analyticsService)
    {
        $this->onboardingService = $onboardingService;
        $this->analyticsService = $analyticsService;
    }

    /**
     * Get onboarding progress for the authenticated user
     */
    public function getProgress(Request $request)
    {
        try {
            $user = $request->user();
            $progress = OnboardingProgress::where('user_id', $user->id)->first();

            if (!$progress) {
                $progress = OnboardingProgress::create([
                    'user_id' => $user->id,
                    'current_step' => 1,
                    'completed_steps' => [],
                    'total_steps' => 6,
                    'progress_percentage' => 0,
                    'started_at' => now(),
                    'metadata' => [
                        'user_type' => 'new',
                        'referral_source' => $request->get('ref'),
                        'device_type' => $request->header('User-Agent')
                    ]
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'progress' => $progress,
                    'next_step' => $this->getNextStep($progress),
                    'recommendations' => $this->getPersonalizedRecommendations($user)
                ],
                'message' => 'Onboarding progress retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve onboarding progress',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update onboarding progress
     */
    public function updateProgress(Request $request)
    {
        $request->validate([
            'step' => 'required|integer|min:1|max:6',
            'completed' => 'required|boolean',
            'data' => 'nullable|array'
        ]);

        try {
            $user = $request->user();
            $progress = OnboardingProgress::where('user_id', $user->id)->first();

            if (!$progress) {
                return response()->json([
                    'success' => false,
                    'error' => 'Onboarding progress not found'
                ], 404);
            }

            $completedSteps = $progress->completed_steps ?? [];
            
            if ($request->completed && !in_array($request->step, $completedSteps)) {
                $completedSteps[] = $request->step;
            }

            $progressPercentage = (count($completedSteps) / $progress->total_steps) * 100;

            $progress->update([
                'current_step' => $request->step,
                'completed_steps' => $completedSteps,
                'progress_percentage' => $progressPercentage,
                'updated_at' => now(),
                'metadata' => array_merge($progress->metadata ?? [], $request->data ?? [])
            ]);

            // Track completion
            if ($progressPercentage >= 100 && !$progress->completed_at) {
                $progress->update([
                    'completed_at' => now(),
                    'completion_time' => now()->diffInMinutes($progress->started_at)
                ]);

                // Trigger completion events
                $this->onboardingService->handleOnboardingCompletion($user, $progress);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'progress' => $progress,
                    'next_step' => $this->getNextStep($progress),
                    'achievements' => $this->getAchievements($user, $progress)
                ],
                'message' => 'Onboarding progress updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to update onboarding progress',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get interactive demo data
     */
    public function getInteractiveDemo(Request $request)
    {
        try {
            $user = $request->user();
            $demoData = $this->onboardingService->generateInteractiveDemo($user);

            return response()->json([
                'success' => true,
                'data' => $demoData,
                'message' => 'Interactive demo data retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve demo data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get personalized recommendations
     */
    public function getRecommendations(Request $request)
    {
        try {
            $user = $request->user();
            $recommendations = $this->getPersonalizedRecommendations($user);

            return response()->json([
                'success' => true,
                'data' => $recommendations,
                'message' => 'Recommendations retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve recommendations',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Complete onboarding step
     */
    public function completeStep(Request $request)
    {
        $request->validate([
            'step' => 'required|integer|min:1|max:6',
            'step_data' => 'required|array'
        ]);

        try {
            $user = $request->user();
            $step = $request->step;
            $stepData = $request->step_data;

            DB::beginTransaction();

            // Process step-specific data
            switch ($step) {
                case 1: // Welcome & Goal Selection
                    $this->processGoalSelection($user, $stepData);
                    break;
                case 2: // Interactive Demo
                    $this->processInteractiveDemo($user, $stepData);
                    break;
                case 3: // AI-Powered Recommendations
                    $this->processAIRecommendations($user, $stepData);
                    break;
                case 4: // Quick Wins Setup
                    $this->processQuickWins($user, $stepData);
                    break;
                case 5: // Team & Collaboration
                    $this->processTeamSetup($user, $stepData);
                    break;
                case 6: // Final Configuration
                    $this->processFinalConfiguration($user, $stepData);
                    break;
            }

            // Update progress
            $progress = OnboardingProgress::where('user_id', $user->id)->first();
            $completedSteps = $progress->completed_steps ?? [];
            
            if (!in_array($step, $completedSteps)) {
                $completedSteps[] = $step;
            }

            $progressPercentage = (count($completedSteps) / $progress->total_steps) * 100;

            $progress->update([
                'current_step' => $step + 1,
                'completed_steps' => $completedSteps,
                'progress_percentage' => $progressPercentage,
                'updated_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => [
                    'progress' => $progress,
                    'next_step' => $this->getNextStep($progress),
                    'achievements' => $this->getAchievements($user, $progress)
                ],
                'message' => 'Onboarding step completed successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Failed to complete onboarding step',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get next step information
     */
    private function getNextStep($progress)
    {
        $currentStep = $progress->current_step;
        
        if ($currentStep > $progress->total_steps) {
            return null;
        }

        $steps = [
            1 => [
                'title' => 'Welcome & Goal Selection',
                'description' => 'Choose your primary business goals and objectives',
                'estimated_time' => 3,
                'type' => 'goal_selection'
            ],
            2 => [
                'title' => 'Interactive Demo',
                'description' => 'Explore key features with guided walkthrough',
                'estimated_time' => 5,
                'type' => 'interactive_demo'
            ],
            3 => [
                'title' => 'AI-Powered Recommendations',
                'description' => 'Get personalized feature recommendations',
                'estimated_time' => 2,
                'type' => 'ai_recommendations'
            ],
            4 => [
                'title' => 'Quick Wins Setup',
                'description' => 'Set up features for immediate value',
                'estimated_time' => 4,
                'type' => 'quick_wins'
            ],
            5 => [
                'title' => 'Team & Collaboration',
                'description' => 'Invite team members and set up collaboration',
                'estimated_time' => 3,
                'type' => 'team_setup'
            ],
            6 => [
                'title' => 'Final Configuration',
                'description' => 'Complete setup and launch your workspace',
                'estimated_time' => 2,
                'type' => 'final_config'
            ]
        ];

        return $steps[$currentStep] ?? null;
    }

    /**
     * Get personalized recommendations
     */
    private function getPersonalizedRecommendations($user)
    {
        return $this->onboardingService->getPersonalizedRecommendations($user);
    }

    /**
     * Get user achievements
     */
    private function getAchievements($user, $progress)
    {
        return $this->onboardingService->getAchievements($user, $progress);
    }

    /**
     * Process goal selection step
     */
    private function processGoalSelection($user, $stepData)
    {
        $preferences = UserPreference::firstOrCreate(['user_id' => $user->id]);
        
        $preferences->update([
            'primary_goals' => $stepData['goals'] ?? [],
            'business_type' => $stepData['business_type'] ?? null,
            'experience_level' => $stepData['experience_level'] ?? null,
            'team_size' => $stepData['team_size'] ?? null
        ]);

        // Track analytics
        $this->analyticsService->track('onboarding_goal_selection', [
            'user_id' => $user->id,
            'goals' => $stepData['goals'] ?? [],
            'business_type' => $stepData['business_type'] ?? null
        ]);
    }

    /**
     * Process interactive demo step
     */
    private function processInteractiveDemo($user, $stepData)
    {
        // Track demo interaction
        $this->analyticsService->track('onboarding_demo_interaction', [
            'user_id' => $user->id,
            'demo_completion' => $stepData['completion_percentage'] ?? 0,
            'features_explored' => $stepData['features_explored'] ?? [],
            'time_spent' => $stepData['time_spent'] ?? 0
        ]);
    }

    /**
     * Process AI recommendations step
     */
    private function processAIRecommendations($user, $stepData)
    {
        $preferences = UserPreference::firstOrCreate(['user_id' => $user->id]);
        
        $preferences->update([
            'recommended_features' => $stepData['selected_features'] ?? [],
            'feature_priorities' => $stepData['feature_priorities'] ?? []
        ]);

        // Track analytics
        $this->analyticsService->track('onboarding_ai_recommendations', [
            'user_id' => $user->id,
            'recommendations_accepted' => $stepData['recommendations_accepted'] ?? 0,
            'recommendations_rejected' => $stepData['recommendations_rejected'] ?? 0
        ]);
    }

    /**
     * Process quick wins step
     */
    private function processQuickWins($user, $stepData)
    {
        // Create quick win tasks
        foreach ($stepData['quick_wins'] ?? [] as $quickWin) {
            $this->onboardingService->createQuickWinTask($user, $quickWin);
        }

        // Track analytics
        $this->analyticsService->track('onboarding_quick_wins', [
            'user_id' => $user->id,
            'quick_wins_selected' => count($stepData['quick_wins'] ?? []),
            'quick_wins_completed' => $stepData['completed_count'] ?? 0
        ]);
    }

    /**
     * Process team setup step
     */
    private function processTeamSetup($user, $stepData)
    {
        // Send team invitations
        if (!empty($stepData['team_invitations'])) {
            $this->onboardingService->sendTeamInvitations($user, $stepData['team_invitations']);
        }

        // Track analytics
        $this->analyticsService->track('onboarding_team_setup', [
            'user_id' => $user->id,
            'invitations_sent' => count($stepData['team_invitations'] ?? []),
            'team_size_target' => $stepData['team_size_target'] ?? 1
        ]);
    }

    /**
     * Process final configuration step
     */
    private function processFinalConfiguration($user, $stepData)
    {
        // Update workspace settings
        if (!empty($stepData['workspace_settings'])) {
            $workspace = $user->workspaces()->first();
            if ($workspace) {
                $workspace->update([
                    'settings' => array_merge($workspace->settings ?? [], $stepData['workspace_settings'])
                ]);
            }
        }

        // Track completion
        $this->analyticsService->track('onboarding_completion', [
            'user_id' => $user->id,
            'completion_time' => $stepData['completion_time'] ?? 0,
            'features_enabled' => $stepData['features_enabled'] ?? []
        ]);
    }
}