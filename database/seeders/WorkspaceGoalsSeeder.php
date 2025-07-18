<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkspaceGoalsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $goals = [
            [
                'name' => 'Instagram Management',
                'slug' => 'instagram',
                'description' => 'Complete Instagram database, lead generation, competitor analysis, and content management.',
                'icon' => 'instagram',
                'color' => '#E1306C',
                'sort_order' => 1,
                'metadata' => json_encode([
                    'features' => ['profile_search', 'lead_generation', 'competitor_analysis', 'content_scheduling'],
                    'integrations' => ['instagram_api', 'instagram_basic_display']
                ])
            ],
            [
                'name' => 'Link in Bio',
                'slug' => 'link-in-bio',
                'description' => 'Drag-and-drop bio link builder with custom domains, analytics, and e-commerce integration.',
                'icon' => 'link',
                'color' => '#10B981',
                'sort_order' => 2,
                'metadata' => json_encode([
                    'features' => ['drag_drop_builder', 'custom_domains', 'analytics', 'qr_codes'],
                    'integrations' => ['domain_providers', 'payment_gateways']
                ])
            ],
            [
                'name' => 'Courses & Community',
                'slug' => 'courses',
                'description' => 'Complete course creation platform with community features, progress tracking, and gamification.',
                'icon' => 'book',
                'color' => '#F59E0B',
                'sort_order' => 3,
                'metadata' => json_encode([
                    'features' => ['course_creation', 'video_hosting', 'community_forums', 'progress_tracking'],
                    'integrations' => ['video_platforms', 'payment_processing']
                ])
            ],
            [
                'name' => 'E-commerce & Marketplace',
                'slug' => 'ecommerce',
                'description' => 'Full e-commerce platform with product management, payment processing, and marketplace features.',
                'icon' => 'shopping-bag',
                'color' => '#8B5CF6',
                'sort_order' => 4,
                'metadata' => json_encode([
                    'features' => ['product_catalog', 'payment_processing', 'inventory_management', 'marketplace'],
                    'integrations' => ['stripe', 'paypal', 'shipping_providers']
                ])
            ],
            [
                'name' => 'CRM & Email Marketing',
                'slug' => 'crm',
                'description' => 'Advanced CRM with contact management, lead scoring, email campaigns, and automation workflows.',
                'icon' => 'users',
                'color' => '#EF4444',
                'sort_order' => 5,
                'metadata' => json_encode([
                    'features' => ['contact_management', 'lead_scoring', 'email_campaigns', 'automation'],
                    'integrations' => ['email_providers', 'sms_providers']
                ])
            ],
            [
                'name' => 'Analytics & Reporting',
                'slug' => 'analytics',
                'description' => 'Comprehensive analytics dashboard with real-time metrics, custom reports, and business intelligence.',
                'icon' => 'chart',
                'color' => '#3B82F6',
                'sort_order' => 6,
                'metadata' => json_encode([
                    'features' => ['dashboard_analytics', 'custom_reports', 'real_time_metrics', 'business_intelligence'],
                    'integrations' => ['google_analytics', 'facebook_pixel']
                ])
            ],
            [
                'name' => 'Website Builder',
                'slug' => 'website-builder',
                'description' => 'Professional website builder with responsive templates, SEO optimization, and custom code support.',
                'icon' => 'globe',
                'color' => '#06B6D4',
                'sort_order' => 7,
                'metadata' => json_encode([
                    'features' => ['drag_drop_website', 'responsive_templates', 'seo_optimization', 'custom_code'],
                    'integrations' => ['domain_providers', 'cdn_services']
                ])
            ],
            [
                'name' => 'Booking & Scheduling',
                'slug' => 'booking',
                'description' => 'Complete booking system with calendar management, appointment scheduling, and payment integration.',
                'icon' => 'calendar',
                'color' => '#F97316',
                'sort_order' => 8,
                'metadata' => json_encode([
                    'features' => ['appointment_scheduling', 'calendar_integration', 'payment_collection', 'staff_management'],
                    'integrations' => ['google_calendar', 'outlook', 'apple_calendar']
                ])
            ],
            [
                'name' => 'AI & Automation',
                'slug' => 'ai-automation',
                'description' => 'AI-powered content generation, smart recommendations, automated workflows, and business insights.',
                'icon' => 'lightbulb',
                'color' => '#A855F7',
                'sort_order' => 9,
                'metadata' => json_encode([
                    'features' => ['content_generation', 'smart_recommendations', 'automated_workflows', 'ai_insights'],
                    'integrations' => ['openai', 'anthropic', 'google_ai']
                ])
            ],
            [
                'name' => 'Financial Management',
                'slug' => 'financial',
                'description' => 'Complete financial management with invoicing, payment processing, expense tracking, and reporting.',
                'icon' => 'dollar',
                'color' => '#059669',
                'sort_order' => 10,
                'metadata' => json_encode([
                    'features' => ['invoicing', 'payment_processing', 'expense_tracking', 'financial_reporting'],
                    'integrations' => ['accounting_software', 'tax_services']
                ])
            ]
        ];

        foreach ($goals as $goal) {
            DB::table('workspace_goals')->insert(array_merge($goal, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }
    }
}