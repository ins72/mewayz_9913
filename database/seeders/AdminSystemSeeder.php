<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\PlanFeature;
use App\Models\Admin\SubscriptionPlan;
use App\Models\Admin\PlanFeatureAssignment;

class AdminSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create plan features
        $features = [
            [
                'name' => 'Bio Sites',
                'slug' => 'bio-sites',
                'description' => 'Create and manage bio sites',
                'category' => 'content',
                'type' => 'numeric',
                'config' => ['min' => 0, 'max' => 1000],
                'is_active' => true,
                'sort_order' => 1
            ],
            [
                'name' => 'Courses',
                'slug' => 'courses',
                'description' => 'Create and sell online courses',
                'category' => 'learning',
                'type' => 'numeric',
                'config' => ['min' => 0, 'max' => 100],
                'is_active' => true,
                'sort_order' => 2
            ],
            [
                'name' => 'Email Campaigns',
                'slug' => 'email-campaigns',
                'description' => 'Send email marketing campaigns',
                'category' => 'marketing',
                'type' => 'numeric',
                'config' => ['min' => 0, 'max' => 500],
                'is_active' => true,
                'sort_order' => 3
            ],
            [
                'name' => 'Custom Domain',
                'slug' => 'custom-domain',
                'description' => 'Use custom domain for bio sites',
                'category' => 'branding',
                'type' => 'boolean',
                'config' => ['default' => false],
                'is_active' => true,
                'sort_order' => 4
            ],
            [
                'name' => 'Analytics',
                'slug' => 'analytics',
                'description' => 'Advanced analytics and reporting',
                'category' => 'analytics',
                'type' => 'boolean',
                'config' => ['default' => false],
                'is_active' => true,
                'sort_order' => 5
            ],
            [
                'name' => 'API Access',
                'slug' => 'api-access',
                'description' => 'Access to API endpoints',
                'category' => 'api',
                'type' => 'boolean',
                'config' => ['default' => false],
                'is_active' => true,
                'sort_order' => 6
            ],
            [
                'name' => 'White Label',
                'slug' => 'white-label',
                'description' => 'Remove branding and use custom branding',
                'category' => 'branding',
                'type' => 'boolean',
                'config' => ['default' => false],
                'is_active' => true,
                'sort_order' => 7
            ],
            [
                'name' => 'Priority Support',
                'slug' => 'priority-support',
                'description' => 'Priority customer support',
                'category' => 'support',
                'type' => 'boolean',
                'config' => ['default' => false],
                'is_active' => true,
                'sort_order' => 8
            ],
            [
                'name' => 'Storage',
                'slug' => 'storage',
                'description' => 'File storage limit in GB',
                'category' => 'storage',
                'type' => 'numeric',
                'config' => ['min' => 1, 'max' => 1000],
                'is_active' => true,
                'sort_order' => 9
            ],
            [
                'name' => 'Team Members',
                'slug' => 'team-members',
                'description' => 'Number of team members allowed',
                'category' => 'users',
                'type' => 'numeric',
                'config' => ['min' => 1, 'max' => 100],
                'is_active' => true,
                'sort_order' => 10
            ]
        ];

        foreach ($features as $feature) {
            PlanFeature::create($feature);
        }

        // Create subscription plans
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'description' => 'Basic plan with limited features',
                'price' => 0.00,
                'currency' => 'USD',
                'billing_cycle' => 'monthly',
                'trial_days' => 0,
                'is_popular' => false,
                'is_featured' => false,
                'status' => 'active',
                'sort_order' => 1
            ],
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'description' => 'Perfect for individuals getting started',
                'price' => 9.99,
                'currency' => 'USD',
                'billing_cycle' => 'monthly',
                'trial_days' => 14,
                'is_popular' => false,
                'is_featured' => false,
                'status' => 'active',
                'sort_order' => 2
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'description' => 'Best for professionals and small businesses',
                'price' => 29.99,
                'currency' => 'USD',
                'billing_cycle' => 'monthly',
                'trial_days' => 14,
                'is_popular' => true,
                'is_featured' => true,
                'status' => 'active',
                'sort_order' => 3
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
                'description' => 'Advanced features for growing businesses',
                'price' => 99.99,
                'currency' => 'USD',
                'billing_cycle' => 'monthly',
                'trial_days' => 30,
                'is_popular' => false,
                'is_featured' => false,
                'status' => 'active',
                'sort_order' => 4
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'Custom solutions for large organizations',
                'price' => 299.99,
                'currency' => 'USD',
                'billing_cycle' => 'monthly',
                'trial_days' => 30,
                'is_popular' => false,
                'is_featured' => false,
                'status' => 'active',
                'sort_order' => 5
            ]
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::create($plan);
        }

        // Create plan feature assignments
        $this->createFeatureAssignments();
    }

    private function createFeatureAssignments(): void
    {
        $plans = SubscriptionPlan::all();
        $features = PlanFeature::all();

        foreach ($plans as $plan) {
            foreach ($features as $feature) {
                $assignment = $this->getAssignmentForPlan($plan->slug, $feature->slug);
                
                if ($assignment) {
                    PlanFeatureAssignment::create([
                        'plan_id' => $plan->id,
                        'feature_id' => $feature->id,
                        'is_enabled' => $assignment['enabled'],
                        'limits' => $assignment['limits'] ?? [],
                        'config' => $assignment['config'] ?? []
                    ]);
                }
            }
        }
    }

    private function getAssignmentForPlan(string $planSlug, string $featureSlug): ?array
    {
        $assignments = [
            'free' => [
                'bio-sites' => ['enabled' => true, 'limits' => ['max' => 1]],
                'courses' => ['enabled' => false],
                'email-campaigns' => ['enabled' => false],
                'custom-domain' => ['enabled' => false],
                'analytics' => ['enabled' => false],
                'api-access' => ['enabled' => false],
                'white-label' => ['enabled' => false],
                'priority-support' => ['enabled' => false],
                'storage' => ['enabled' => true, 'limits' => ['max' => 1]],
                'team-members' => ['enabled' => true, 'limits' => ['max' => 1]]
            ],
            'starter' => [
                'bio-sites' => ['enabled' => true, 'limits' => ['max' => 3]],
                'courses' => ['enabled' => true, 'limits' => ['max' => 1]],
                'email-campaigns' => ['enabled' => true, 'limits' => ['max' => 10]],
                'custom-domain' => ['enabled' => false],
                'analytics' => ['enabled' => true],
                'api-access' => ['enabled' => false],
                'white-label' => ['enabled' => false],
                'priority-support' => ['enabled' => false],
                'storage' => ['enabled' => true, 'limits' => ['max' => 5]],
                'team-members' => ['enabled' => true, 'limits' => ['max' => 2]]
            ],
            'professional' => [
                'bio-sites' => ['enabled' => true, 'limits' => ['max' => 10]],
                'courses' => ['enabled' => true, 'limits' => ['max' => 5]],
                'email-campaigns' => ['enabled' => true, 'limits' => ['max' => 50]],
                'custom-domain' => ['enabled' => true],
                'analytics' => ['enabled' => true],
                'api-access' => ['enabled' => true],
                'white-label' => ['enabled' => false],
                'priority-support' => ['enabled' => false],
                'storage' => ['enabled' => true, 'limits' => ['max' => 25]],
                'team-members' => ['enabled' => true, 'limits' => ['max' => 5]]
            ],
            'business' => [
                'bio-sites' => ['enabled' => true, 'limits' => ['max' => 50]],
                'courses' => ['enabled' => true, 'limits' => ['max' => 20]],
                'email-campaigns' => ['enabled' => true, 'limits' => ['max' => 200]],
                'custom-domain' => ['enabled' => true],
                'analytics' => ['enabled' => true],
                'api-access' => ['enabled' => true],
                'white-label' => ['enabled' => true],
                'priority-support' => ['enabled' => true],
                'storage' => ['enabled' => true, 'limits' => ['max' => 100]],
                'team-members' => ['enabled' => true, 'limits' => ['max' => 20]]
            ],
            'enterprise' => [
                'bio-sites' => ['enabled' => true, 'limits' => ['max' => -1]],
                'courses' => ['enabled' => true, 'limits' => ['max' => -1]],
                'email-campaigns' => ['enabled' => true, 'limits' => ['max' => -1]],
                'custom-domain' => ['enabled' => true],
                'analytics' => ['enabled' => true],
                'api-access' => ['enabled' => true],
                'white-label' => ['enabled' => true],
                'priority-support' => ['enabled' => true],
                'storage' => ['enabled' => true, 'limits' => ['max' => -1]],
                'team-members' => ['enabled' => true, 'limits' => ['max' => -1]]
            ]
        ];

        return $assignments[$planSlug][$featureSlug] ?? null;
    }
}