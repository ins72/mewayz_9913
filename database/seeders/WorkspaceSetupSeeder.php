<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\WorkspaceGoal;
use App\Models\Feature;
use App\Models\SubscriptionPlan;

class WorkspaceSetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the 6 main goals
        $this->createGoals();
        
        // Create the 40 features
        $this->createFeatures();
        
        // Create subscription plans
        $this->createSubscriptionPlans();
    }

    private function createGoals()
    {
        $goals = [
            [
                'name' => 'Instagram Management',
                'slug' => 'instagram-management',
                'description' => 'Social media posting, scheduling, and analytics',
                'icon' => '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>',
                'color' => '#E1306C',
                'sort_order' => 1,
            ],
            [
                'name' => 'Link in Bio',
                'slug' => 'link-in-bio',
                'description' => 'Custom landing pages with link management',
                'icon' => '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clip-rule="evenodd"/></svg>',
                'color' => '#10B981',
                'sort_order' => 2,
            ],
            [
                'name' => 'Course Creation',
                'slug' => 'course-creation',
                'description' => 'Educational content and community building',
                'icon' => '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                'color' => '#3B82F6',
                'sort_order' => 3,
            ],
            [
                'name' => 'E-commerce',
                'slug' => 'e-commerce',
                'description' => 'Online store management and sales',
                'icon' => '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2L3 7v11a2 2 0 002 2h10a2 2 0 002-2V7l-7-5zM8 16a2 2 0 100-4 2 2 0 000 4zm4-6a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>',
                'color' => '#F59E0B',
                'sort_order' => 4,
            ],
            [
                'name' => 'CRM',
                'slug' => 'crm',
                'description' => 'Customer relationship and lead management',
                'icon' => '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>',
                'color' => '#8B5CF6',
                'sort_order' => 5,
            ],
            [
                'name' => 'Marketing Hub',
                'slug' => 'marketing-hub',
                'description' => 'Email campaigns and automation',
                'icon' => '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
                'color' => '#EF4444',
                'sort_order' => 6,
            ],
        ];

        foreach ($goals as $goal) {
            WorkspaceGoal::create($goal);
        }
    }

    private function createFeatures()
    {
        $features = [
            // Instagram Management Features
            [
                'name' => 'Instagram Post Scheduling',
                'slug' => 'instagram-post-scheduling',
                'description' => 'Schedule Instagram posts in advance',
                'category' => 'Social Media',
                'goals' => ['instagram-management'],
                'icon' => '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/></svg>',
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'sort_order' => 1,
            ],
            [
                'name' => 'Instagram Stories Management',
                'slug' => 'instagram-stories-management',
                'description' => 'Create and schedule Instagram Stories',
                'category' => 'Social Media',
                'goals' => ['instagram-management'],
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'sort_order' => 2,
            ],
            [
                'name' => 'Instagram Analytics',
                'slug' => 'instagram-analytics',
                'description' => 'Track engagement metrics and audience insights',
                'category' => 'Analytics',
                'goals' => ['instagram-management'],
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'sort_order' => 3,
            ],
            [
                'name' => 'Hashtag Research',
                'slug' => 'hashtag-research',
                'description' => 'Find trending hashtags for your content',
                'category' => 'Social Media',
                'goals' => ['instagram-management'],
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'sort_order' => 4,
            ],
            [
                'name' => 'Instagram Content Calendar',
                'slug' => 'instagram-content-calendar',
                'description' => 'Visual content planning and organization',
                'category' => 'Social Media',
                'goals' => ['instagram-management'],
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'sort_order' => 5,
            ],
            [
                'name' => 'Instagram Direct Messages',
                'slug' => 'instagram-direct-messages',
                'description' => 'Manage DMs and customer conversations',
                'category' => 'Communication',
                'goals' => ['instagram-management'],
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'sort_order' => 6,
            ],

            // Link in Bio Features
            [
                'name' => 'Bio Page Builder',
                'slug' => 'bio-page-builder',
                'description' => 'Drag-and-drop bio page creation',
                'category' => 'Website Builder',
                'goals' => ['link-in-bio'],
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'sort_order' => 7,
            ],
            [
                'name' => 'Custom Bio Templates',
                'slug' => 'custom-bio-templates',
                'description' => 'Pre-designed templates for various industries',
                'category' => 'Website Builder',
                'goals' => ['link-in-bio'],
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'sort_order' => 8,
            ],
            [
                'name' => 'Bio Link Analytics',
                'slug' => 'bio-link-analytics',
                'description' => 'Track clicks and visitor behavior',
                'category' => 'Analytics',
                'goals' => ['link-in-bio'],
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'sort_order' => 9,
            ],
            [
                'name' => 'A/B Testing Bio Pages',
                'slug' => 'ab-testing-bio-pages',
                'description' => 'Test different versions of your bio page',
                'category' => 'Optimization',
                'goals' => ['link-in-bio'],
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'sort_order' => 10,
            ],
            [
                'name' => 'Custom Domain for Bio',
                'slug' => 'custom-domain-bio',
                'description' => 'Use your own domain for bio pages',
                'category' => 'Website Builder',
                'goals' => ['link-in-bio'],
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'sort_order' => 11,
            ],
            [
                'name' => 'Bio Monetization',
                'slug' => 'bio-monetization',
                'description' => 'Add payment buttons and tip jars',
                'category' => 'E-commerce',
                'goals' => ['link-in-bio'],
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'sort_order' => 12,
            ],

            // Course Creation Features
            [
                'name' => 'Course Builder',
                'slug' => 'course-builder',
                'description' => 'Create comprehensive online courses',
                'category' => 'Education',
                'goals' => ['course-creation'],
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'sort_order' => 13,
            ],
            [
                'name' => 'Video Hosting',
                'slug' => 'video-hosting',
                'description' => 'Host and stream course videos',
                'category' => 'Education',
                'goals' => ['course-creation'],
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'sort_order' => 14,
            ],
            [
                'name' => 'Student Management',
                'slug' => 'student-management',
                'description' => 'Track student progress and enrollment',
                'category' => 'Education',
                'goals' => ['course-creation'],
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'sort_order' => 15,
            ],
            [
                'name' => 'Course Certificates',
                'slug' => 'course-certificates',
                'description' => 'Issue certificates upon completion',
                'category' => 'Education',
                'goals' => ['course-creation'],
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'sort_order' => 16,
            ],
            [
                'name' => 'Live Webinars',
                'slug' => 'live-webinars',
                'description' => 'Host live classes and Q&A sessions',
                'category' => 'Education',
                'goals' => ['course-creation'],
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'sort_order' => 17,
            ],
            [
                'name' => 'Community Forums',
                'slug' => 'community-forums',
                'description' => 'Build learning communities around courses',
                'category' => 'Community',
                'goals' => ['course-creation'],
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'sort_order' => 18,
            ],
            [
                'name' => 'Quiz Builder',
                'slug' => 'quiz-builder',
                'description' => 'Create interactive quizzes and assessments',
                'category' => 'Education',
                'goals' => ['course-creation'],
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'sort_order' => 19,
            ],

            // E-commerce Features
            [
                'name' => 'Product Catalog',
                'slug' => 'product-catalog',
                'description' => 'Manage digital and physical products',
                'category' => 'E-commerce',
                'goals' => ['e-commerce'],
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'sort_order' => 20,
            ],
            [
                'name' => 'Inventory Management',
                'slug' => 'inventory-management',
                'description' => 'Track stock levels and variants',
                'category' => 'E-commerce',
                'goals' => ['e-commerce'],
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'sort_order' => 21,
            ],
            [
                'name' => 'Order Processing',
                'slug' => 'order-processing',
                'description' => 'Automated order fulfillment workflows',
                'category' => 'E-commerce',
                'goals' => ['e-commerce'],
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'sort_order' => 22,
            ],
            [
                'name' => 'Digital Downloads',
                'slug' => 'digital-downloads',
                'description' => 'Sell digital products and downloads',
                'category' => 'E-commerce',
                'goals' => ['e-commerce'],
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'sort_order' => 23,
            ],
            [
                'name' => 'Discount Codes',
                'slug' => 'discount-codes',
                'description' => 'Create promotional codes and campaigns',
                'category' => 'E-commerce',
                'goals' => ['e-commerce'],
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'sort_order' => 24,
            ],
            [
                'name' => 'Shipping Calculator',
                'slug' => 'shipping-calculator',
                'description' => 'Calculate shipping costs and tracking',
                'category' => 'E-commerce',
                'goals' => ['e-commerce'],
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'sort_order' => 25,
            ],
            [
                'name' => 'Payment Gateway',
                'slug' => 'payment-gateway',
                'description' => 'Accept payments through multiple methods',
                'category' => 'E-commerce',
                'goals' => ['e-commerce'],
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'sort_order' => 26,
            ],

            // CRM Features
            [
                'name' => 'Contact Management',
                'slug' => 'contact-management',
                'description' => 'Organize customer and lead information',
                'category' => 'CRM',
                'goals' => ['crm'],
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'sort_order' => 27,
            ],
            [
                'name' => 'Lead Scoring',
                'slug' => 'lead-scoring',
                'description' => 'Automatically score and prioritize leads',
                'category' => 'CRM',
                'goals' => ['crm'],
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'sort_order' => 28,
            ],
            [
                'name' => 'Sales Pipeline',
                'slug' => 'sales-pipeline',
                'description' => 'Track deals through sales stages',
                'category' => 'CRM',
                'goals' => ['crm'],
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'sort_order' => 29,
            ],
            [
                'name' => 'Task Management',
                'slug' => 'task-management',
                'description' => 'Assign and track follow-up tasks',
                'category' => 'CRM',
                'goals' => ['crm'],
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'sort_order' => 30,
            ],
            [
                'name' => 'Communication History',
                'slug' => 'communication-history',
                'description' => 'Track all customer interactions',
                'category' => 'CRM',
                'goals' => ['crm'],
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'sort_order' => 31,
            ],
            [
                'name' => 'Custom Fields',
                'slug' => 'custom-fields',
                'description' => 'Add custom data fields to contacts',
                'category' => 'CRM',
                'goals' => ['crm'],
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'sort_order' => 32,
            ],

            // Marketing Hub Features
            [
                'name' => 'Email Campaigns',
                'slug' => 'email-campaigns',
                'description' => 'Create and send email marketing campaigns',
                'category' => 'Marketing',
                'goals' => ['marketing-hub'],
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'sort_order' => 33,
            ],
            [
                'name' => 'Email Automation',
                'slug' => 'email-automation',
                'description' => 'Set up automated email sequences',
                'category' => 'Marketing',
                'goals' => ['marketing-hub'],
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'sort_order' => 34,
            ],
            [
                'name' => 'List Management',
                'slug' => 'list-management',
                'description' => 'Segment and manage subscriber lists',
                'category' => 'Marketing',
                'goals' => ['marketing-hub'],
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'sort_order' => 35,
            ],
            [
                'name' => 'Landing Pages',
                'slug' => 'landing-pages',
                'description' => 'Create high-converting landing pages',
                'category' => 'Marketing',
                'goals' => ['marketing-hub'],
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'sort_order' => 36,
            ],
            [
                'name' => 'Pop-up Forms',
                'slug' => 'popup-forms',
                'description' => 'Create lead capture forms and pop-ups',
                'category' => 'Marketing',
                'goals' => ['marketing-hub'],
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'sort_order' => 37,
            ],
            [
                'name' => 'Email Templates',
                'slug' => 'email-templates',
                'description' => 'Professional email template library',
                'category' => 'Marketing',
                'goals' => ['marketing-hub'],
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'sort_order' => 38,
            ],
            [
                'name' => 'Campaign Analytics',
                'slug' => 'campaign-analytics',
                'description' => 'Track email campaign performance',
                'category' => 'Analytics',
                'goals' => ['marketing-hub'],
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'sort_order' => 39,
            ],
            [
                'name' => 'Social Media Integration',
                'slug' => 'social-media-integration',
                'description' => 'Connect and manage multiple social platforms',
                'category' => 'Social Media',
                'goals' => ['marketing-hub', 'instagram-management'],
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'sort_order' => 40,
            ],
        ];

        foreach ($features as $feature) {
            Feature::create($feature);
        }
    }

    private function createSubscriptionPlans()
    {
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'description' => 'Perfect for getting started with basic features',
                'type' => 'free',
                'base_price' => 0.00,
                'feature_price_monthly' => 0.00,
                'feature_price_yearly' => 0.00,
                'max_features' => 3,
                'has_branding' => true,
                'has_priority_support' => false,
                'has_custom_domain' => false,
                'has_api_access' => false,
                'included_features' => [1, 7, 13], // Basic features from each category
                'metadata' => [
                    'badge_color' => 'gray',
                    'popular' => false,
                    'features_list' => [
                        'Up to 3 features',
                        'Mewayz branding',
                        'Community support',
                        'Basic analytics',
                    ],
                ],
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'description' => 'For growing businesses and content creators',
                'type' => 'professional',
                'base_price' => 0.00,
                'feature_price_monthly' => 1.00,
                'feature_price_yearly' => 10.00,
                'max_features' => null, // unlimited
                'has_branding' => true,
                'has_priority_support' => true,
                'has_custom_domain' => false,
                'has_api_access' => false,
                'included_features' => [],
                'metadata' => [
                    'badge_color' => 'blue',
                    'popular' => true,
                    'features_list' => [
                        '$1 per feature per month',
                        '$10 per feature per year',
                        'Priority support',
                        'Advanced analytics',
                        'Mewayz branding',
                    ],
                ],
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'For large organizations with custom needs',
                'type' => 'enterprise',
                'base_price' => 0.00,
                'feature_price_monthly' => 1.50,
                'feature_price_yearly' => 15.00,
                'max_features' => null, // unlimited
                'has_branding' => false, // white-label
                'has_priority_support' => true,
                'has_custom_domain' => true,
                'has_api_access' => true,
                'included_features' => [],
                'metadata' => [
                    'badge_color' => 'purple',
                    'popular' => false,
                    'features_list' => [
                        '$1.50 per feature per month',
                        '$15 per feature per year',
                        'White-label capabilities',
                        'Custom branding',
                        'Dedicated support',
                        'API access',
                        'Custom domain',
                    ],
                ],
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::create($plan);
        }
    }
}
