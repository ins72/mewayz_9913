<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WorkspaceSetupController extends Controller
{
    /**
     * Enhanced 6-Step Workspace Setup Wizard
     * Based on comprehensive platform documentation
     * 
     * Step 1: Main Goals Selection (6 primary business goals)
     * Step 2: Feature Selection (40+ features organized by goals)
     * Step 3: Team Setup (roles, invitations, permissions)
     * Step 4: Subscription Selection (Free, Professional, Enterprise)
     * Step 5: Branding Configuration (logo, colors, white-label)
     * Step 6: Final Review & Launch
     */
    
    const SETUP_STEPS = [
        1 => 'main_goals',
        2 => 'feature_selection',
        3 => 'team_setup',
        4 => 'subscription_selection',
        5 => 'branding_configuration',
        6 => 'final_review'
    ];
    
    const MAIN_GOALS = [
        'instagram_management' => [
            'name' => 'Instagram Management',
            'description' => 'Social media posting, scheduling, and analytics',
            'icon' => '📱',
            'features' => [
                'content_scheduling', 'content_calendar', 'hashtag_research',
                'story_management', 'analytics_dashboard', 'dm_management',
                'competitor_analysis'
            ]
        ],
        'link_in_bio' => [
            'name' => 'Link in Bio',
            'description' => 'Custom landing pages with link management',
            'icon' => '🔗',
            'features' => [
                'page_builder', 'template_library', 'custom_components',
                'analytics_tracking', 'ab_testing', 'mobile_optimization'
            ]
        ],
        'course_creation' => [
            'name' => 'Course Creation',
            'description' => 'Educational content and community building',
            'icon' => '🎓',
            'features' => [
                'course_builder', 'content_management', 'student_management',
                'community_features', 'certification_system', 'payment_integration',
                'live_sessions'
            ]
        ],
        'ecommerce' => [
            'name' => 'E-commerce',
            'description' => 'Online store management and sales',
            'icon' => '🛍️',
            'features' => [
                'product_catalog', 'inventory_tracking', 'order_processing',
                'payment_gateway', 'shipping_management', 'customer_portal',
                'marketing_tools'
            ]
        ],
        'crm' => [
            'name' => 'CRM',
            'description' => 'Customer relationship and lead management',
            'icon' => '👥',
            'features' => [
                'contact_management', 'lead_tracking', 'communication_history',
                'task_management', 'deal_management', 'custom_fields',
                'automation_rules'
            ]
        ],
        'marketing_hub' => [
            'name' => 'Marketing Hub',
            'description' => 'Email campaigns and automation',
            'icon' => '📧',
            'features' => [
                'email_campaigns', 'automation_workflows', 'list_management',
                'campaign_analytics', 'social_integration', 'content_calendar',
                'performance_tracking'
            ]
        ]
    ];
    
    const SUBSCRIPTION_PLANS = [
        'free' => [
            'name' => 'Free Plan',
            'price' => 0,
            'max_features' => 10,
            'branding' => 'Mewayz branding on external content',
            'support' => 'Community support',
            'features' => ['basic_functionality', 'limited_features', 'community_support']
        ],
        'professional' => [
            'name' => 'Professional Plan',
            'price_monthly' => 1, // per feature per month
            'price_yearly' => 10, // per feature per year
            'max_features' => 'unlimited',
            'branding' => 'Mewayz branding on external content',
            'support' => 'Priority support',
            'features' => ['advanced_features', 'priority_support', 'mewayz_branding']
        ],
        'enterprise' => [
            'name' => 'Enterprise Plan',
            'price_monthly' => 1.5, // per feature per month
            'price_yearly' => 15, // per feature per year
            'max_features' => 'unlimited',
            'branding' => 'White-label capabilities',
            'support' => 'Dedicated account management',
            'features' => ['white_label', 'custom_branding', 'dedicated_support', 'advanced_analytics']
        ]
    ];
    
    /**
     * Get current setup step for user's workspace
     */
    public function getCurrentStep(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Get user's primary workspace or create one
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                $workspace = Workspace::create([
                    'user_id' => $user->id,
                    'name' => $user->name . "'s Workspace",
                    'description' => 'Primary workspace',
                    'is_primary' => true,
                    'settings' => json_encode([
                        'setup_step' => 1,
                        'setup_completed' => false,
                        'setup_progress' => []
                    ])
                ]);
            }
            
            $settings = json_decode($workspace->settings, true) ?? [];
            $currentStep = $settings['setup_step'] ?? 1;
            $setupCompleted = $settings['setup_completed'] ?? false;
            $setupProgress = $settings['setup_progress'] ?? [];
            
            return response()->json([
                'success' => true,
                'current_step' => $currentStep,
                'setup_completed' => $setupCompleted,
                'setup_progress' => $setupProgress,
                'total_steps' => count(self::SETUP_STEPS),
                'step_name' => self::SETUP_STEPS[$currentStep] ?? 'unknown'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting current setup step', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to get current setup step'
            ], 500);
        }
    }
    
    /**
     * Step 1: Business Information
     */
    public function saveBusinessInfo(Request $request)
    {
        try {
            $request->validate([
                'business_name' => 'required|string|max:255',
                'business_type' => 'required|string|max:100',
                'industry' => 'required|string|max:100',
                'business_size' => 'required|string|max:50',
                'website' => 'nullable|url',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'description' => 'nullable|string|max:1000'
            ]);
            
            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }
            
            $settings = json_decode($workspace->settings, true) ?? [];
            $settings['business_info'] = $request->only([
                'business_name', 'business_type', 'industry', 'business_size',
                'website', 'phone', 'address', 'description'
            ]);
            
            // Update progress
            $settings['setup_progress']['business_info'] = true;
            $settings['setup_step'] = 2;
            
            $workspace->update([
                'name' => $request->business_name,
                'description' => $request->description,
                'settings' => json_encode($settings)
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Business information saved successfully',
                'next_step' => 2
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error saving business info', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to save business information'
            ], 500);
        }
    }
    
    /**
     * Step 2: Social Media Accounts
     */
    public function saveSocialMedia(Request $request)
    {
        try {
            $request->validate([
                'platforms' => 'required|array',
                'platforms.*.platform' => 'required|string|in:instagram,facebook,twitter,linkedin,youtube,tiktok',
                'platforms.*.username' => 'required|string|max:100',
                'platforms.*.url' => 'required|url',
                'platforms.*.primary' => 'boolean',
                'content_types' => 'array',
                'content_types.*' => 'string|max:50',
                'posting_frequency' => 'required|string|in:daily,weekly,bi-weekly,monthly',
                'target_audience' => 'required|string|max:500'
            ]);
            
            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }
            
            $settings = json_decode($workspace->settings, true) ?? [];
            $settings['social_media'] = $request->only([
                'platforms', 'content_types', 'posting_frequency', 'target_audience'
            ]);
            
            // Update progress
            $settings['setup_progress']['social_media'] = true;
            $settings['setup_step'] = 3;
            
            $workspace->update(['settings' => json_encode($settings)]);
            
            return response()->json([
                'success' => true,
                'message' => 'Social media information saved successfully',
                'next_step' => 3
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error saving social media info', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to save social media information'
            ], 500);
        }
    }
    
    /**
     * Step 3: Brand Colors & Logo
     */
    public function saveBranding(Request $request)
    {
        try {
            $request->validate([
                'primary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'secondary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'accent_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'logo' => 'nullable|string', // Base64 encoded image
                'brand_voice' => 'required|string|in:professional,casual,friendly,authoritative,playful',
                'brand_values' => 'array',
                'brand_values.*' => 'string|max:100'
            ]);
            
            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }
            
            $settings = json_decode($workspace->settings, true) ?? [];
            $settings['branding'] = $request->only([
                'primary_color', 'secondary_color', 'accent_color', 
                'logo', 'brand_voice', 'brand_values'
            ]);
            
            // Update progress
            $settings['setup_progress']['branding'] = true;
            $settings['setup_step'] = 4;
            
            $workspace->update(['settings' => json_encode($settings)]);
            
            return response()->json([
                'success' => true,
                'message' => 'Branding information saved successfully',
                'next_step' => 4
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error saving branding info', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to save branding information'
            ], 500);
        }
    }
    
    /**
     * Step 4: Content Categories
     */
    public function saveContentCategories(Request $request)
    {
        try {
            $request->validate([
                'categories' => 'required|array|min:1',
                'categories.*' => 'string|max:100',
                'content_pillars' => 'required|array|min:3|max:5',
                'content_pillars.*' => 'string|max:100',
                'content_style' => 'required|string|in:educational,entertaining,promotional,inspirational,mixed',
                'hashtag_strategy' => 'required|string|in:trending,branded,mixed,niche'
            ]);
            
            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }
            
            $settings = json_decode($workspace->settings, true) ?? [];
            $settings['content_categories'] = $request->only([
                'categories', 'content_pillars', 'content_style', 'hashtag_strategy'
            ]);
            
            // Update progress
            $settings['setup_progress']['content_categories'] = true;
            $settings['setup_step'] = 5;
            
            $workspace->update(['settings' => json_encode($settings)]);
            
            return response()->json([
                'success' => true,
                'message' => 'Content categories saved successfully',
                'next_step' => 5
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error saving content categories', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to save content categories'
            ], 500);
        }
    }
    
    /**
     * Step 5: Goals & Objectives
     */
    public function saveGoalsObjectives(Request $request)
    {
        try {
            $request->validate([
                'primary_goal' => 'required|string|in:brand_awareness,lead_generation,sales,engagement,traffic,community_building',
                'target_metrics' => 'required|array',
                'target_metrics.followers' => 'integer|min:0',
                'target_metrics.engagement_rate' => 'numeric|min:0|max:100',
                'target_metrics.monthly_reach' => 'integer|min:0',
                'target_metrics.conversions' => 'integer|min:0',
                'timeline' => 'required|string|in:1_month,3_months,6_months,1_year',
                'budget' => 'required|string|in:0-100,100-500,500-1000,1000-5000,5000+',
                'success_metrics' => 'array',
                'success_metrics.*' => 'string|max:100'
            ]);
            
            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }
            
            $settings = json_decode($workspace->settings, true) ?? [];
            $settings['goals_objectives'] = $request->only([
                'primary_goal', 'target_metrics', 'timeline', 'budget', 'success_metrics'
            ]);
            
            // Update progress
            $settings['setup_progress']['goals_objectives'] = true;
            $settings['setup_step'] = 6;
            
            $workspace->update(['settings' => json_encode($settings)]);
            
            return response()->json([
                'success' => true,
                'message' => 'Goals and objectives saved successfully',
                'next_step' => 6
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error saving goals and objectives', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to save goals and objectives'
            ], 500);
        }
    }
    
    /**
     * Step 6: Review & Complete Setup
     */
    public function completeSetup(Request $request)
    {
        try {
            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }
            
            $settings = json_decode($workspace->settings, true) ?? [];
            
            // Verify all steps are completed
            $requiredSteps = ['business_info', 'social_media', 'branding', 'content_categories', 'goals_objectives'];
            $missingSteps = [];
            
            foreach ($requiredSteps as $step) {
                if (!isset($settings['setup_progress'][$step]) || !$settings['setup_progress'][$step]) {
                    $missingSteps[] = $step;
                }
            }
            
            if (!empty($missingSteps)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Please complete all setup steps',
                    'missing_steps' => $missingSteps
                ], 400);
            }
            
            // Mark setup as completed
            $settings['setup_completed'] = true;
            $settings['setup_completed_at'] = now()->toISOString();
            $settings['setup_step'] = 6;
            
            $workspace->update(['settings' => json_encode($settings)]);
            
            Log::info('Workspace setup completed', [
                'user_id' => $user->id,
                'workspace_id' => $workspace->id
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Workspace setup completed successfully!',
                'workspace' => [
                    'id' => $workspace->id,
                    'name' => $workspace->name,
                    'description' => $workspace->description,
                    'setup_completed' => true
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error completing setup', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to complete setup'
            ], 500);
        }
    }
    
    /**
     * Get setup summary for review
     */
    public function getSetupSummary(Request $request)
    {
        try {
            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }
            
            $settings = json_decode($workspace->settings, true) ?? [];
            
            $summary = [
                'business_info' => $settings['business_info'] ?? [],
                'social_media' => $settings['social_media'] ?? [],
                'branding' => $settings['branding'] ?? [],
                'content_categories' => $settings['content_categories'] ?? [],
                'goals_objectives' => $settings['goals_objectives'] ?? [],
                'setup_progress' => $settings['setup_progress'] ?? [],
                'setup_completed' => $settings['setup_completed'] ?? false
            ];
            
            return response()->json([
                'success' => true,
                'summary' => $summary
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting setup summary', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to get setup summary'
            ], 500);
        }
    }
    
    /**
     * Reset setup (for testing or re-onboarding)
     */
    public function resetSetup(Request $request)
    {
        try {
            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }
            
            $settings = json_decode($workspace->settings, true) ?? [];
            
            // Reset setup progress
            $settings['setup_step'] = 1;
            $settings['setup_completed'] = false;
            $settings['setup_progress'] = [];
            
            // Keep business info but reset other steps
            unset($settings['social_media']);
            unset($settings['branding']);
            unset($settings['content_categories']);
            unset($settings['goals_objectives']);
            
            $workspace->update(['settings' => json_encode($settings)]);
            
            return response()->json([
                'success' => true,
                'message' => 'Setup reset successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error resetting setup', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to reset setup'
            ], 500);
        }
    }
}
