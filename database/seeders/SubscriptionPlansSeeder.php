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
                'type' => 'free',
                'base_price' => 0.00,
                'feature_price_monthly' => 0.00,
                'feature_price_yearly' => 0.00,
                'max_features' => 10,
                'has_branding' => 1,
                'has_priority_support' => 0,
                'has_custom_domain' => 0,
                'has_api_access' => 0,
                'included_features' => json_encode([
                    'drag-drop-builder',
                    'dashboard-analytics',
                    'basic-templates',
                    'community-support'
                ]),
                'metadata' => json_encode([
                    'limitations' => [
                        'mewayz_branding' => true,
                        'limited_customization' => true,
                        'basic_support' => true
                    ],
                    'billing_cycle' => 'monthly'
                ]),
                'is_active' => 1
            ],
            [
                'name' => 'Professional Plan',
                'slug' => 'professional',
                'description' => 'Ideal for growing businesses and professionals who need advanced features.',
                'type' => 'professional',
                'base_price' => 0.00,
                'feature_price_monthly' => 1.00,
                'feature_price_yearly' => 10.00,
                'max_features' => null,
                'has_branding' => 0,
                'has_priority_support' => 0,
                'has_custom_domain' => 1,
                'has_api_access' => 1,
                'included_features' => json_encode('all_standard_features'),
                'metadata' => json_encode([
                    'limitations' => [
                        'no_white_label' => true,
                        'standard_support' => true
                    ],
                    'billing_cycle' => 'per_feature',
                    'pricing_model' => 'pay_per_feature'
                ]),
                'is_active' => 1
            ],
            [
                'name' => 'Enterprise Plan',
                'slug' => 'enterprise',
                'description' => 'Complete solution for large organizations with white-label options and priority support.',
                'type' => 'enterprise',
                'base_price' => 0.00,
                'feature_price_monthly' => 1.50,
                'feature_price_yearly' => 15.00,
                'max_features' => null,
                'has_branding' => 0,
                'has_priority_support' => 1,
                'has_custom_domain' => 1,
                'has_api_access' => 1,
                'included_features' => json_encode('all_features_including_enterprise'),
                'metadata' => json_encode([
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
                ]),
                'is_active' => 1
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