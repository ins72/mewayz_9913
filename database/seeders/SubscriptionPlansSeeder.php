<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubscriptionPlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free Plan',
                'slug' => 'free',
                'description' => 'Perfect for getting started with basic features and limited functionality.',
                'monthly_price' => 0.00,
                'yearly_price' => 0.00,
                'features_limit' => 10,
                'workspaces_limit' => 1,
                'team_members_limit' => 3,
                'storage_limit' => 1, // GB
                'bandwidth_limit' => 10, // GB
                'is_active' => true,
                'is_popular' => false,
                'sort_order' => 1,
                'metadata' => json_encode([
                    'features_included' => [
                        'drag-drop-builder',
                        'dashboard-analytics',
                        'basic-templates',
                        'community-support'
                    ],
                    'limitations' => [
                        'mewayz_branding' => true,
                        'limited_customization' => true,
                        'basic_support' => true
                    ],
                    'billing_cycle' => 'monthly'
                ])
            ],
            [
                'name' => 'Professional Plan',
                'slug' => 'professional',
                'description' => 'Ideal for growing businesses and professionals who need advanced features.',
                'monthly_price' => 1.00, // per feature
                'yearly_price' => 10.00, // per feature
                'features_limit' => null, // unlimited
                'workspaces_limit' => 5,
                'team_members_limit' => 10,
                'storage_limit' => 100, // GB
                'bandwidth_limit' => 1000, // GB
                'is_active' => true,
                'is_popular' => true,
                'sort_order' => 2,
                'metadata' => json_encode([
                    'features_included' => 'all_standard_features',
                    'limitations' => [
                        'no_white_label' => true,
                        'standard_support' => true
                    ],
                    'billing_cycle' => 'per_feature',
                    'pricing_model' => 'pay_per_feature'
                ])
            ],
            [
                'name' => 'Enterprise Plan',
                'slug' => 'enterprise',
                'description' => 'Complete solution for large organizations with white-label options and priority support.',
                'monthly_price' => 1.50, // per feature
                'yearly_price' => 15.00, // per feature
                'features_limit' => null, // unlimited
                'workspaces_limit' => null, // unlimited
                'team_members_limit' => null, // unlimited
                'storage_limit' => null, // unlimited
                'bandwidth_limit' => null, // unlimited
                'is_active' => true,
                'is_popular' => false,
                'sort_order' => 3,
                'metadata' => json_encode([
                    'features_included' => 'all_features_including_enterprise',
                    'benefits' => [
                        'white_label_solutions' => true,
                        'custom_branding' => true,
                        'priority_support' => true,
                        'dedicated_account_manager' => true,
                        'custom_integrations' => true,
                        'advanced_security' => true,
                        'compliance_features' => true
                    ],
                    'billing_cycle' => 'per_feature',
                    'pricing_model' => 'pay_per_feature_premium'
                ])
            ]
        ];

        foreach ($plans as $plan) {
            DB::table('subscription_plans')->insert(array_merge($plan, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }
    }
}