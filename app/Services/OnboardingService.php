<?php

namespace App\Services;

use App\Models\User;
use App\Models\Workspace;
use App\Models\OnboardingProgress;
use App\Models\UserPreference;
use App\Models\WorkspaceUser;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class OnboardingService
{
    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Generate interactive demo data
     */
    public function generateInteractiveDemo(User $user)
    {
        $preferences = UserPreference::where('user_id', $user->id)->first();
        $primaryGoals = $preferences->primary_goals ?? [];

        return [
            'demo_sections' => $this->getDemoSections($primaryGoals),
            'estimated_time' => 5, // minutes
            'features_to_explore' => $this->getFeaturesToExplore($primaryGoals),
            'demo_data' => $this->generateDemoData($user),
            'interactive_elements' => $this->getInteractiveElements()
        ];
    }

    /**
     * Get personalized recommendations
     */
    public function getPersonalizedRecommendations(User $user)
    {
        $preferences = UserPreference::where('user_id', $user->id)->first();
        
        if (!$preferences) {
            return $this->getDefaultRecommendations();
        }

        $recommendations = [];
        $primaryGoals = $preferences->primary_goals ?? [];
        $businessType = $preferences->business_type;
        $experienceLevel = $preferences->experience_level;

        // Goal-based recommendations
        foreach ($primaryGoals as $goal) {
            $recommendations = array_merge($recommendations, $this->getGoalRecommendations($goal, $businessType, $experienceLevel));
        }

        // Business type specific recommendations
        if ($businessType) {
            $recommendations = array_merge($recommendations, $this->getBusinessTypeRecommendations($businessType));
        }

        // Experience level recommendations
        $recommendations = array_merge($recommendations, $this->getExperienceLevelRecommendations($experienceLevel));

        return [
            'recommendations' => $this->prioritizeRecommendations($recommendations),
            'quick_actions' => $this->getQuickActions($primaryGoals),
            'suggested_features' => $this->getSuggestedFeatures($primaryGoals, $businessType),
            'templates' => $this->getRecommendedTemplates($primaryGoals, $businessType)
        ];
    }

    /**
     * Get user achievements
     */
    public function getAchievements(User $user, OnboardingProgress $progress)
    {
        $achievements = [];

        // Onboarding progress achievements
        if ($progress->progress_percentage >= 25) {
            $achievements[] = [
                'id' => 'onboarding_25',
                'title' => 'Getting Started',
                'description' => 'Completed 25% of onboarding',
                'badge' => 'bronze',
                'unlocked_at' => now()
            ];
        }

        if ($progress->progress_percentage >= 50) {
            $achievements[] = [
                'id' => 'onboarding_50',
                'title' => 'Half Way There',
                'description' => 'Completed 50% of onboarding',
                'badge' => 'silver',
                'unlocked_at' => now()
            ];
        }

        if ($progress->progress_percentage >= 100) {
            $achievements[] = [
                'id' => 'onboarding_complete',
                'title' => 'Welcome Aboard!',
                'description' => 'Completed full onboarding process',
                'badge' => 'gold',
                'unlocked_at' => $progress->completed_at
            ];
        }

        // Speed achievements
        if ($progress->completion_time && $progress->completion_time <= 10) {
            $achievements[] = [
                'id' => 'speed_demon',
                'title' => 'Speed Demon',
                'description' => 'Completed onboarding in under 10 minutes',
                'badge' => 'platinum',
                'unlocked_at' => $progress->completed_at
            ];
        }

        return $achievements;
    }

    /**
     * Handle onboarding completion
     */
    public function handleOnboardingCompletion(User $user, OnboardingProgress $progress)
    {
        // Send completion email
        // Mail::to($user->email)->send(new OnboardingCompletedMail($user, $progress));

        // Create welcome tasks
        $this->createWelcomeTasks($user);

        // Set up default workspace if needed
        $this->ensureDefaultWorkspace($user);

        // Track completion analytics
        $this->analyticsService->track('onboarding_completed', [
            'user_id' => $user->id,
            'completion_time' => $progress->completion_time,
            'steps_completed' => count($progress->completed_steps),
            'total_steps' => $progress->total_steps
        ]);
    }

    /**
     * Create quick win task
     */
    public function createQuickWinTask(User $user, array $quickWin)
    {
        // Implementation depends on your task system
        // For now, we'll just track it
        $this->analyticsService->track('quick_win_created', [
            'user_id' => $user->id,
            'task_type' => $quickWin['type'] ?? 'unknown',
            'task_data' => $quickWin
        ]);
    }

    /**
     * Send team invitations
     */
    public function sendTeamInvitations(User $user, array $invitations)
    {
        $workspace = $user->workspaces()->first();
        
        if (!$workspace) {
            return;
        }

        foreach ($invitations as $invitation) {
            // Send invitation email
            // Mail::to($invitation['email'])->send(new TeamInvitationMail($user, $workspace, $invitation));
            
            // Track invitation
            $this->analyticsService->track('team_invitation_sent', [
                'user_id' => $user->id,
                'workspace_id' => $workspace->id,
                'invitation_email' => $invitation['email'],
                'role' => $invitation['role']
            ]);
        }
    }

    /**
     * Get demo sections based on goals
     */
    private function getDemoSections(array $goals)
    {
        $sections = [];

        if (in_array('instagram', $goals)) {
            $sections[] = [
                'id' => 'instagram_demo',
                'title' => 'Instagram Management',
                'description' => 'Explore Instagram database and posting features',
                'duration' => 2,
                'interactive' => true
            ];
        }

        if (in_array('link_in_bio', $goals)) {
            $sections[] = [
                'id' => 'bio_builder_demo',
                'title' => 'Link in Bio Builder',
                'description' => 'Create a professional bio link page',
                'duration' => 1,
                'interactive' => true
            ];
        }

        if (in_array('ecommerce', $goals)) {
            $sections[] = [
                'id' => 'ecommerce_demo',
                'title' => 'E-commerce Setup',
                'description' => 'Set up your online store',
                'duration' => 2,
                'interactive' => true
            ];
        }

        return $sections;
    }

    /**
     * Get features to explore
     */
    private function getFeaturesToExplore(array $goals)
    {
        $features = [];

        $goalFeatures = [
            'instagram' => ['instagram_search', 'post_scheduler', 'analytics'],
            'link_in_bio' => ['bio_builder', 'qr_generator', 'analytics'],
            'ecommerce' => ['product_catalog', 'order_management', 'payments'],
            'crm' => ['contact_management', 'lead_scoring', 'email_campaigns'],
            'courses' => ['course_builder', 'student_management', 'certificates'],
            'analytics' => ['dashboard', 'reports', 'insights']
        ];

        foreach ($goals as $goal) {
            if (isset($goalFeatures[$goal])) {
                $features = array_merge($features, $goalFeatures[$goal]);
            }
        }

        return array_unique($features);
    }

    /**
     * Generate demo data
     */
    private function generateDemoData(User $user)
    {
        return [
            'sample_posts' => $this->generateSamplePosts(),
            'sample_contacts' => $this->generateSampleContacts(),
            'sample_products' => $this->generateSampleProducts(),
            'sample_analytics' => $this->generateSampleAnalytics()
        ];
    }

    /**
     * Get interactive elements
     */
    private function getInteractiveElements()
    {
        return [
            'tooltips' => true,
            'highlights' => true,
            'guided_tour' => true,
            'interactive_forms' => true,
            'progress_tracking' => true
        ];
    }

    /**
     * Get default recommendations
     */
    private function getDefaultRecommendations()
    {
        return [
            'recommendations' => [
                [
                    'id' => 'setup_profile',
                    'title' => 'Complete Your Profile',
                    'description' => 'Add your profile information and preferences',
                    'priority' => 'high',
                    'estimated_time' => 5
                ],
                [
                    'id' => 'create_workspace',
                    'title' => 'Create Your First Workspace',
                    'description' => 'Set up your workspace to get started',
                    'priority' => 'high',
                    'estimated_time' => 3
                ]
            ]
        ];
    }

    /**
     * Get goal-based recommendations
     */
    private function getGoalRecommendations(string $goal, ?string $businessType, string $experienceLevel)
    {
        $recommendations = [];

        switch ($goal) {
            case 'instagram':
                $recommendations[] = [
                    'id' => 'instagram_setup',
                    'title' => 'Connect Instagram Account',
                    'description' => 'Connect your Instagram account to start managing your social media',
                    'priority' => 'high',
                    'estimated_time' => 2
                ];
                break;

            case 'link_in_bio':
                $recommendations[] = [
                    'id' => 'bio_link_setup',
                    'title' => 'Create Bio Link Page',
                    'description' => 'Design your professional bio link page',
                    'priority' => 'high',
                    'estimated_time' => 10
                ];
                break;

            case 'ecommerce':
                $recommendations[] = [
                    'id' => 'store_setup',
                    'title' => 'Set Up Your Store',
                    'description' => 'Create your online store and add products',
                    'priority' => 'high',
                    'estimated_time' => 15
                ];
                break;
        }

        return $recommendations;
    }

    /**
     * Get business type recommendations
     */
    private function getBusinessTypeRecommendations(?string $businessType)
    {
        if (!$businessType) {
            return [];
        }

        $recommendations = [];

        switch ($businessType) {
            case 'influencer':
                $recommendations[] = [
                    'id' => 'influencer_setup',
                    'title' => 'Influencer Tools Setup',
                    'description' => 'Set up tools specifically for influencers',
                    'priority' => 'medium',
                    'estimated_time' => 8
                ];
                break;

            case 'small_business':
                $recommendations[] = [
                    'id' => 'business_setup',
                    'title' => 'Business Tools Setup',
                    'description' => 'Configure business management tools',
                    'priority' => 'medium',
                    'estimated_time' => 12
                ];
                break;
        }

        return $recommendations;
    }

    /**
     * Get experience level recommendations
     */
    private function getExperienceLevelRecommendations(string $experienceLevel)
    {
        $recommendations = [];

        if ($experienceLevel === 'beginner') {
            $recommendations[] = [
                'id' => 'beginner_guide',
                'title' => 'Beginner\'s Guide',
                'description' => 'Learn the basics with our comprehensive guide',
                'priority' => 'medium',
                'estimated_time' => 20
            ];
        }

        return $recommendations;
    }

    /**
     * Prioritize recommendations
     */
    private function prioritizeRecommendations(array $recommendations)
    {
        usort($recommendations, function ($a, $b) {
            $priorityOrder = ['high' => 1, 'medium' => 2, 'low' => 3];
            
            return $priorityOrder[$a['priority']] <=> $priorityOrder[$b['priority']];
        });

        return $recommendations;
    }

    /**
     * Get quick actions
     */
    private function getQuickActions(array $goals)
    {
        $actions = [];

        foreach ($goals as $goal) {
            switch ($goal) {
                case 'instagram':
                    $actions[] = [
                        'id' => 'create_post',
                        'title' => 'Create Your First Post',
                        'icon' => 'plus',
                        'route' => '/social/posts/create'
                    ];
                    break;

                case 'link_in_bio':
                    $actions[] = [
                        'id' => 'design_bio',
                        'title' => 'Design Bio Link',
                        'icon' => 'link',
                        'route' => '/bio/builder'
                    ];
                    break;
            }
        }

        return $actions;
    }

    /**
     * Get suggested features
     */
    private function getSuggestedFeatures(array $goals, ?string $businessType)
    {
        $features = [];

        foreach ($goals as $goal) {
            switch ($goal) {
                case 'instagram':
                    $features[] = 'instagram_analytics';
                    $features[] = 'content_scheduler';
                    break;

                case 'ecommerce':
                    $features[] = 'product_catalog';
                    $features[] = 'payment_processing';
                    break;
            }
        }

        return array_unique($features);
    }

    /**
     * Get recommended templates
     */
    private function getRecommendedTemplates(array $goals, ?string $businessType)
    {
        $templates = [];
        
        // Goal-based template recommendations
        foreach ($goals as $goal) {
            switch ($goal) {
                case 'instagram':
                    $templates[] = [
                        'id' => 'instagram-pro',
                        'name' => 'Instagram Pro',
                        'description' => 'Perfect for social media influencers and content creators',
                        'preview' => '/templates/instagram-pro.jpg',
                        'features' => ['Social Media Integration', 'Content Scheduler', 'Analytics Dashboard'],
                        'price' => 29.99,
                        'category' => 'Social Media'
                    ];
                    $templates[] = [
                        'id' => 'content-creator',
                        'name' => 'Content Creator Hub',
                        'description' => 'All-in-one solution for content creators',
                        'preview' => '/templates/content-creator.jpg',
                        'features' => ['Multi-Platform Posting', 'Brand Collaborations', 'Revenue Tracking'],
                        'price' => 49.99,
                        'category' => 'Content Creation'
                    ];
                    break;
                    
                case 'link_in_bio':
                    $templates[] = [
                        'id' => 'linktree-pro',
                        'name' => 'LinkTree Pro',
                        'description' => 'Professional link-in-bio solution',
                        'preview' => '/templates/linktree-pro.jpg',
                        'features' => ['Custom Branding', 'Link Analytics', 'QR Code Generation'],
                        'price' => 19.99,
                        'category' => 'Link Management'
                    ];
                    $templates[] = [
                        'id' => 'bio-showcase',
                        'name' => 'Bio Showcase',
                        'description' => 'Showcase your work and links in style',
                        'preview' => '/templates/bio-showcase.jpg',
                        'features' => ['Portfolio Gallery', 'Contact Forms', 'Social Integration'],
                        'price' => 24.99,
                        'category' => 'Portfolio'
                    ];
                    break;
                    
                case 'ecommerce':
                    $templates[] = [
                        'id' => 'store-builder',
                        'name' => 'Store Builder Pro',
                        'description' => 'Complete e-commerce solution',
                        'preview' => '/templates/store-builder.jpg',
                        'features' => ['Product Catalog', 'Payment Processing', 'Order Management'],
                        'price' => 79.99,
                        'category' => 'E-commerce'
                    ];
                    $templates[] = [
                        'id' => 'digital-products',
                        'name' => 'Digital Products Store',
                        'description' => 'Perfect for selling digital products',
                        'preview' => '/templates/digital-products.jpg',
                        'features' => ['Digital Downloads', 'License Management', 'Customer Portal'],
                        'price' => 59.99,
                        'category' => 'Digital Commerce'
                    ];
                    break;
                    
                case 'crm':
                    $templates[] = [
                        'id' => 'crm-dashboard',
                        'name' => 'CRM Dashboard',
                        'description' => 'Customer relationship management made easy',
                        'preview' => '/templates/crm-dashboard.jpg',
                        'features' => ['Contact Management', 'Sales Pipeline', 'Email Automation'],
                        'price' => 39.99,
                        'category' => 'CRM'
                    ];
                    break;
                    
                case 'courses':
                    $templates[] = [
                        'id' => 'course-platform',
                        'name' => 'Course Platform',
                        'description' => 'Create and sell online courses',
                        'preview' => '/templates/course-platform.jpg',
                        'features' => ['Course Builder', 'Student Management', 'Certificates'],
                        'price' => 89.99,
                        'category' => 'Education'
                    ];
                    break;
                    
                case 'analytics':
                    $templates[] = [
                        'id' => 'analytics-pro',
                        'name' => 'Analytics Pro',
                        'description' => 'Advanced analytics and reporting',
                        'preview' => '/templates/analytics-pro.jpg',
                        'features' => ['Custom Reports', 'Real-time Metrics', 'Data Visualization'],
                        'price' => 49.99,
                        'category' => 'Analytics'
                    ];
                    break;
            }
        }
        
        // Business type specific recommendations
        if ($businessType) {
            switch ($businessType) {
                case 'influencer':
                    $templates[] = [
                        'id' => 'influencer-hub',
                        'name' => 'Influencer Hub',
                        'description' => 'Complete influencer management platform',
                        'preview' => '/templates/influencer-hub.jpg',
                        'features' => ['Brand Partnerships', 'Content Calendar', 'Performance Analytics'],
                        'price' => 69.99,
                        'category' => 'Influencer'
                    ];
                    break;
                    
                case 'small_business':
                    $templates[] = [
                        'id' => 'business-suite',
                        'name' => 'Business Suite',
                        'description' => 'All-in-one business management solution',
                        'preview' => '/templates/business-suite.jpg',
                        'features' => ['CRM', 'E-commerce', 'Email Marketing', 'Analytics'],
                        'price' => 99.99,
                        'category' => 'Business'
                    ];
                    break;
                    
                case 'freelancer':
                    $templates[] = [
                        'id' => 'freelancer-portfolio',
                        'name' => 'Freelancer Portfolio',
                        'description' => 'Professional portfolio for freelancers',
                        'preview' => '/templates/freelancer-portfolio.jpg',
                        'features' => ['Portfolio Gallery', 'Client Management', 'Invoice System'],
                        'price' => 39.99,
                        'category' => 'Portfolio'
                    ];
                    break;
                    
                case 'startup':
                    $templates[] = [
                        'id' => 'startup-launcher',
                        'name' => 'Startup Launcher',
                        'description' => 'Perfect for launching your startup',
                        'preview' => '/templates/startup-launcher.jpg',
                        'features' => ['Landing Pages', 'User Analytics', 'Growth Tools'],
                        'price' => 79.99,
                        'category' => 'Startup'
                    ];
                    break;
                    
                case 'agency':
                    $templates[] = [
                        'id' => 'agency-pro',
                        'name' => 'Agency Pro',
                        'description' => 'Multi-client agency management',
                        'preview' => '/templates/agency-pro.jpg',
                        'features' => ['Client Portals', 'Project Management', 'White Label'],
                        'price' => 129.99,
                        'category' => 'Agency'
                    ];
                    break;
            }
        }
        
        // Add some popular general templates
        $generalTemplates = [
            [
                'id' => 'professional-dashboard',
                'name' => 'Professional Dashboard',
                'description' => 'Clean and professional dashboard template',
                'preview' => '/templates/professional-dashboard.jpg',
                'features' => ['Customizable Widgets', 'Dark/Light Mode', 'Mobile Responsive'],
                'price' => 34.99,
                'category' => 'General'
            ],
            [
                'id' => 'minimalist-bio',
                'name' => 'Minimalist Bio',
                'description' => 'Simple and elegant bio page',
                'preview' => '/templates/minimalist-bio.jpg',
                'features' => ['Clean Design', 'Fast Loading', 'SEO Optimized'],
                'price' => 14.99,
                'category' => 'Bio'
            ],
            [
                'id' => 'creative-portfolio',
                'name' => 'Creative Portfolio',
                'description' => 'Showcase your creative work',
                'preview' => '/templates/creative-portfolio.jpg',
                'features' => ['Image Gallery', 'Animation Effects', 'Contact Forms'],
                'price' => 44.99,
                'category' => 'Portfolio'
            ]
        ];
        
        $templates = array_merge($templates, $generalTemplates);
        
        // Remove duplicates and sort by relevance
        $templates = array_unique($templates, SORT_REGULAR);
        
        // Sort by price (ascending) and then by category
        usort($templates, function($a, $b) {
            if ($a['price'] == $b['price']) {
                return strcmp($a['category'], $b['category']);
            }
            return $a['price'] <=> $b['price'];
        });
        
        return array_slice($templates, 0, 6); // Return top 6 recommendations
    }

    /**
     * Create welcome tasks
     */
    private function createWelcomeTasks(User $user)
    {
        // Create welcome tasks for new users
        $this->analyticsService->track('welcome_tasks_created', [
            'user_id' => $user->id,
            'task_count' => 3
        ]);
    }

    /**
     * Ensure default workspace
     */
    private function ensureDefaultWorkspace(User $user)
    {
        if ($user->workspaces()->count() === 0) {
            $workspace = Workspace::create([
                'name' => $user->name . "'s Workspace",
                'user_id' => $user->id,
                'slug' => \Illuminate\Support\Str::slug($user->name) . '-workspace',
                'description' => 'Default workspace for ' . $user->name,
                'settings' => [
                    'theme' => 'dark',
                    'features' => [],
                    'branding' => []
                ]
            ]);

            // Add user as owner
            WorkspaceUser::create([
                'workspace_id' => $workspace->id,
                'user_id' => $user->id,
                'role' => 'owner',
                'permissions' => ['*']
            ]);
        }
    }

    /**
     * Generate sample posts
     */
    private function generateSamplePosts()
    {
        return [
            [
                'id' => 'sample_1',
                'content' => 'Welcome to our platform! 🚀',
                'platform' => 'instagram',
                'scheduled_at' => now()->addHours(2),
                'status' => 'scheduled'
            ],
            [
                'id' => 'sample_2',
                'content' => 'Check out our latest features!',
                'platform' => 'facebook',
                'scheduled_at' => now()->addDays(1),
                'status' => 'draft'
            ]
        ];
    }

    /**
     * Generate sample contacts
     */
    private function generateSampleContacts()
    {
        return [
            [
                'id' => 'contact_1',
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'status' => 'lead',
                'score' => 85
            ],
            [
                'id' => 'contact_2',
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'status' => 'customer',
                'score' => 92
            ]
        ];
    }

    /**
     * Generate sample products
     */
    private function generateSampleProducts()
    {
        return [
            [
                'id' => 'product_1',
                'name' => 'Sample Product 1',
                'price' => 29.99,
                'stock' => 100,
                'status' => 'active'
            ],
            [
                'id' => 'product_2',
                'name' => 'Sample Product 2',
                'price' => 49.99,
                'stock' => 50,
                'status' => 'active'
            ]
        ];
    }

    /**
     * Generate sample analytics
     */
    private function generateSampleAnalytics()
    {
        return [
            'total_visitors' => 1250,
            'total_conversions' => 45,
            'conversion_rate' => 3.6,
            'revenue' => 1380.50
        ];
    }
}