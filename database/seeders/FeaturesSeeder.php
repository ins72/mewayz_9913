<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeaturesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $features = [
            // Instagram Management Features
            [
                'name' => 'Instagram Profile Search',
                'slug' => 'instagram-profile-search',
                'description' => 'Advanced Instagram profile search with filters for followers, engagement, location, and hashtags.',
                'icon' => 'search',
                'category' => 'Instagram Management',
                'goals' => json_encode([1]),
                'monthly_price' => 2.00,
                'yearly_price' => 20.00,
                'is_free' => false,
                'sort_order' => 1,
                'metadata' => json_encode(['integration' => 'instagram_api', 'limits' => ['searches_per_day' => 100]])
            ],
            [
                'name' => 'Lead Generation & Export',
                'slug' => 'lead-generation-export',
                'description' => 'Export Instagram profiles with contact information, engagement metrics, and CSV/Excel formats.',
                'icon' => 'download',
                'category' => 'Instagram Management',
                'goals' => json_encode([1]),
                'monthly_price' => 3.00,
                'yearly_price' => 30.00,
                'is_free' => false,
                'sort_order' => 2,
                'metadata' => json_encode(['formats' => ['csv', 'excel'], 'limits' => ['exports_per_month' => 50]])
            ],
            [
                'name' => 'Competitor Analysis',
                'slug' => 'competitor-analysis',
                'description' => 'Advanced competitor analysis with growth tracking, content analysis, and engagement metrics.',
                'icon' => 'trending',
                'category' => 'Instagram Management',
                'goals' => json_encode([1]),
                'monthly_price' => 2.50,
                'yearly_price' => 25.00,
                'is_free' => false,
                'sort_order' => 3,
                'metadata' => json_encode(['competitors_limit' => 10, 'analysis_depth' => 'advanced'])
            ],
            [
                'name' => 'Content Scheduling',
                'slug' => 'content-scheduling',
                'description' => 'Schedule Instagram posts, stories, and reels with optimal timing suggestions.',
                'icon' => 'calendar',
                'category' => 'Instagram Management',
                'goals' => json_encode([1]),
                'monthly_price' => 1.50,
                'yearly_price' => 15.00,
                'is_free' => false,
                'sort_order' => 4,
                'metadata' => json_encode(['posts_per_month' => 100, 'auto_timing' => true])
            ],
            [
                'name' => 'Hashtag Research',
                'slug' => 'hashtag-research',
                'description' => 'AI-powered hashtag research with trending tags, performance analytics, and suggestions.',
                'icon' => 'search',
                'category' => 'Instagram Management',
                'goals' => json_encode([1]),
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'is_free' => false,
                'sort_order' => 5,
                'metadata' => json_encode(['hashtag_suggestions' => 50, 'trending_analysis' => true])
            ],

            // Link in Bio Features
            [
                'name' => 'Drag & Drop Builder',
                'slug' => 'drag-drop-builder',
                'description' => 'Visual drag-and-drop interface for creating stunning bio link pages.',
                'icon' => 'edit',
                'category' => 'Link in Bio',
                'goals' => json_encode([2]),
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'is_free' => true,
                'sort_order' => 6,
                'metadata' => json_encode(['templates' => 50, 'custom_css' => false])
            ],
            [
                'name' => 'Custom Domains',
                'slug' => 'custom-domains',
                'description' => 'Connect your own domain to your bio link page for professional branding.',
                'icon' => 'globe',
                'category' => 'Link in Bio',
                'goals' => json_encode([2]),
                'monthly_price' => 2.00,
                'yearly_price' => 20.00,
                'is_free' => false,
                'sort_order' => 7,
                'metadata' => json_encode(['ssl_included' => true, 'subdomain_support' => true])
            ],
            [
                'name' => 'Bio Link Analytics',
                'slug' => 'bio-link-analytics',
                'description' => 'Detailed analytics for your bio link with click tracking, visitor insights, and conversion metrics.',
                'icon' => 'chart',
                'category' => 'Link in Bio',
                'goals' => json_encode([2, 6]),
                'monthly_price' => 1.50,
                'yearly_price' => 15.00,
                'is_free' => false,
                'sort_order' => 8,
                'metadata' => json_encode(['real_time' => true, 'export_reports' => true])
            ],
            [
                'name' => 'QR Code Generator',
                'slug' => 'qr-code-generator',
                'description' => 'Automatic QR code generation for your bio links with customizable designs.',
                'icon' => 'qr-code',
                'category' => 'Link in Bio',
                'goals' => json_encode([2]),
                'monthly_price' => 0.50,
                'yearly_price' => 5.00,
                'is_free' => false,
                'sort_order' => 9,
                'metadata' => json_encode(['custom_designs' => 10, 'high_resolution' => true])
            ],
            [
                'name' => 'E-commerce Integration',
                'slug' => 'ecommerce-integration-bio',
                'description' => 'Integrate your e-commerce store with bio links for direct product sales.',
                'icon' => 'shopping-bag',
                'category' => 'Link in Bio',
                'goals' => json_encode([2, 4]),
                'monthly_price' => 2.50,
                'yearly_price' => 25.00,
                'is_free' => false,
                'sort_order' => 10,
                'metadata' => json_encode(['product_showcase' => true, 'cart_integration' => true])
            ],

            // Courses & Community Features
            [
                'name' => 'Course Creation',
                'slug' => 'course-creation',
                'description' => 'Create and manage online courses with video hosting, quizzes, and assignments.',
                'icon' => 'book',
                'category' => 'Courses & Community',
                'goals' => json_encode([3]),
                'monthly_price' => 3.00,
                'yearly_price' => 30.00,
                'is_free' => false,
                'sort_order' => 11,
                'metadata' => json_encode(['courses_limit' => 10, 'video_storage' => '100GB'])
            ],
            [
                'name' => 'Video Hosting',
                'slug' => 'video-hosting',
                'description' => 'Professional video hosting with adaptive streaming and progress tracking.',
                'icon' => 'video',
                'category' => 'Courses & Community',
                'goals' => json_encode([3]),
                'monthly_price' => 2.00,
                'yearly_price' => 20.00,
                'is_free' => false,
                'sort_order' => 12,
                'metadata' => json_encode(['storage_limit' => '500GB', 'adaptive_streaming' => true])
            ],
            [
                'name' => 'Community Forums',
                'slug' => 'community-forums',
                'description' => 'Build engaged communities with discussion forums, moderation tools, and user profiles.',
                'icon' => 'chat',
                'category' => 'Courses & Community',
                'goals' => json_encode([3]),
                'monthly_price' => 1.50,
                'yearly_price' => 15.00,
                'is_free' => false,
                'sort_order' => 13,
                'metadata' => json_encode(['forums_limit' => 50, 'moderation_tools' => true])
            ],
            [
                'name' => 'Progress Tracking',
                'slug' => 'progress-tracking',
                'description' => 'Track student progress with completion certificates and achievement badges.',
                'icon' => 'check',
                'category' => 'Courses & Community',
                'goals' => json_encode([3]),
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'is_free' => false,
                'sort_order' => 14,
                'metadata' => json_encode(['certificates' => true, 'badges' => true])
            ],
            [
                'name' => 'Live Streaming',
                'slug' => 'live-streaming',
                'description' => 'Host live streaming sessions for courses with interactive chat and Q&A.',
                'icon' => 'video',
                'category' => 'Courses & Community',
                'goals' => json_encode([3]),
                'monthly_price' => 4.00,
                'yearly_price' => 40.00,
                'is_free' => false,
                'sort_order' => 15,
                'metadata' => json_encode(['concurrent_viewers' => 100, 'recording' => true])
            ],

            // E-commerce & Marketplace Features
            [
                'name' => 'Product Catalog',
                'slug' => 'product-catalog',
                'description' => 'Comprehensive product catalog with variants, images, and inventory management.',
                'icon' => 'shopping-bag',
                'category' => 'E-commerce & Marketplace',
                'goals' => json_encode([4]),
                'monthly_price' => 2.00,
                'yearly_price' => 20.00,
                'is_free' => false,
                'sort_order' => 16,
                'metadata' => json_encode(['products_limit' => 1000, 'variants' => true])
            ],
            [
                'name' => 'Payment Processing',
                'slug' => 'payment-processing',
                'description' => 'Secure payment processing with multiple gateways and international support.',
                'icon' => 'dollar',
                'category' => 'E-commerce & Marketplace',
                'goals' => json_encode([4]),
                'monthly_price' => 2.50,
                'yearly_price' => 25.00,
                'is_free' => false,
                'sort_order' => 17,
                'metadata' => json_encode(['gateways' => ['stripe', 'paypal', 'apple_pay'], 'currencies' => 50])
            ],
            [
                'name' => 'Inventory Management',
                'slug' => 'inventory-management',
                'description' => 'Track inventory levels with low stock alerts and automatic reorder points.',
                'icon' => 'chart',
                'category' => 'E-commerce & Marketplace',
                'goals' => json_encode([4]),
                'monthly_price' => 1.50,
                'yearly_price' => 15.00,
                'is_free' => false,
                'sort_order' => 18,
                'metadata' => json_encode(['warehouses' => 5, 'alerts' => true])
            ],
            [
                'name' => 'Marketplace Features',
                'slug' => 'marketplace-features',
                'description' => 'Multi-vendor marketplace with seller management and commission tracking.',
                'icon' => 'users',
                'category' => 'E-commerce & Marketplace',
                'goals' => json_encode([4]),
                'monthly_price' => 5.00,
                'yearly_price' => 50.00,
                'is_free' => false,
                'sort_order' => 19,
                'metadata' => json_encode(['vendors_limit' => 100, 'commission_tracking' => true])
            ],
            [
                'name' => 'Digital Products',
                'slug' => 'digital-products',
                'description' => 'Sell digital products with secure download links and license management.',
                'icon' => 'download',
                'category' => 'E-commerce & Marketplace',
                'goals' => json_encode([4]),
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'is_free' => false,
                'sort_order' => 20,
                'metadata' => json_encode(['file_size_limit' => '1GB', 'download_limits' => true])
            ],

            // CRM & Email Marketing Features
            [
                'name' => 'Contact Management',
                'slug' => 'contact-management',
                'description' => 'Advanced contact database with custom fields, tags, and segmentation.',
                'icon' => 'users',
                'category' => 'CRM & Email Marketing',
                'goals' => json_encode([5]),
                'monthly_price' => 1.50,
                'yearly_price' => 15.00,
                'is_free' => false,
                'sort_order' => 21,
                'metadata' => json_encode(['contacts_limit' => 10000, 'custom_fields' => 50])
            ],
            [
                'name' => 'Lead Scoring',
                'slug' => 'lead-scoring',
                'description' => 'AI-powered lead scoring with behavioral tracking and qualification criteria.',
                'icon' => 'trending',
                'category' => 'CRM & Email Marketing',
                'goals' => json_encode([5]),
                'monthly_price' => 2.00,
                'yearly_price' => 20.00,
                'is_free' => false,
                'sort_order' => 22,
                'metadata' => json_encode(['scoring_rules' => 20, 'ai_powered' => true])
            ],
            [
                'name' => 'Email Campaigns',
                'slug' => 'email-campaigns',
                'description' => 'Create and send professional email campaigns with templates and automation.',
                'icon' => 'mail',
                'category' => 'CRM & Email Marketing',
                'goals' => json_encode([5]),
                'monthly_price' => 2.50,
                'yearly_price' => 25.00,
                'is_free' => false,
                'sort_order' => 23,
                'metadata' => json_encode(['emails_per_month' => 10000, 'templates' => 100])
            ],
            [
                'name' => 'Marketing Automation',
                'slug' => 'marketing-automation',
                'description' => 'Advanced automation workflows with triggers, conditions, and actions.',
                'icon' => 'lightbulb',
                'category' => 'CRM & Email Marketing',
                'goals' => json_encode([5]),
                'monthly_price' => 3.00,
                'yearly_price' => 30.00,
                'is_free' => false,
                'sort_order' => 24,
                'metadata' => json_encode(['workflows' => 50, 'triggers' => 20])
            ],
            [
                'name' => 'SMS Marketing',
                'slug' => 'sms-marketing',
                'description' => 'Send SMS campaigns and automated messages with delivery tracking.',
                'icon' => 'chat',
                'category' => 'CRM & Email Marketing',
                'goals' => json_encode([5]),
                'monthly_price' => 2.00,
                'yearly_price' => 20.00,
                'is_free' => false,
                'sort_order' => 25,
                'metadata' => json_encode(['sms_per_month' => 1000, 'international' => true])
            ],

            // Analytics & Reporting Features
            [
                'name' => 'Dashboard Analytics',
                'slug' => 'dashboard-analytics',
                'description' => 'Comprehensive analytics dashboard with real-time metrics and KPIs.',
                'icon' => 'chart',
                'category' => 'Analytics & Reporting',
                'goals' => json_encode([6]),
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'is_free' => true,
                'sort_order' => 26,
                'metadata' => json_encode(['widgets' => 20, 'real_time' => true])
            ],
            [
                'name' => 'Custom Reports',
                'slug' => 'custom-reports',
                'description' => 'Create custom reports with drag-and-drop builder and scheduled delivery.',
                'icon' => 'edit',
                'category' => 'Analytics & Reporting',
                'goals' => json_encode([6]),
                'monthly_price' => 2.00,
                'yearly_price' => 20.00,
                'is_free' => false,
                'sort_order' => 27,
                'metadata' => json_encode(['reports_limit' => 50, 'scheduled_delivery' => true])
            ],
            [
                'name' => 'Business Intelligence',
                'slug' => 'business-intelligence',
                'description' => 'Advanced BI tools with predictive analytics and trend analysis.',
                'icon' => 'lightbulb',
                'category' => 'Analytics & Reporting',
                'goals' => json_encode([6]),
                'monthly_price' => 4.00,
                'yearly_price' => 40.00,
                'is_free' => false,
                'sort_order' => 28,
                'metadata' => json_encode(['predictive_models' => 10, 'ml_powered' => true])
            ],
            [
                'name' => 'Social Media Analytics',
                'slug' => 'social-media-analytics',
                'description' => 'Comprehensive social media analytics across all platforms.',
                'icon' => 'chart',
                'category' => 'Analytics & Reporting',
                'goals' => json_encode([6, 1]),
                'monthly_price' => 1.50,
                'yearly_price' => 15.00,
                'is_free' => false,
                'sort_order' => 29,
                'metadata' => json_encode(['platforms' => 10, 'competitor_tracking' => true])
            ],
            [
                'name' => 'E-commerce Analytics',
                'slug' => 'ecommerce-analytics',
                'description' => 'Detailed e-commerce analytics with sales performance and customer insights.',
                'icon' => 'shopping-bag',
                'category' => 'Analytics & Reporting',
                'goals' => json_encode([6, 4]),
                'monthly_price' => 2.00,
                'yearly_price' => 20.00,
                'is_free' => false,
                'sort_order' => 30,
                'metadata' => json_encode(['sales_tracking' => true, 'customer_lifetime_value' => true])
            ],

            // Website Builder Features
            [
                'name' => 'Website Templates',
                'slug' => 'website-templates',
                'description' => 'Professional website templates with responsive design and customization.',
                'icon' => 'globe',
                'category' => 'Website Builder',
                'goals' => json_encode([7]),
                'monthly_price' => 1.00,
                'yearly_price' => 10.00,
                'is_free' => false,
                'sort_order' => 31,
                'metadata' => json_encode(['templates' => 100, 'responsive' => true])
            ],
            [
                'name' => 'SEO Optimization',
                'slug' => 'seo-optimization',
                'description' => 'Built-in SEO tools with meta tags, sitemap generation, and optimization suggestions.',
                'icon' => 'search',
                'category' => 'Website Builder',
                'goals' => json_encode([7]),
                'monthly_price' => 1.50,
                'yearly_price' => 15.00,
                'is_free' => false,
                'sort_order' => 32,
                'metadata' => json_encode(['seo_analysis' => true, 'sitemap' => true])
            ],
            [
                'name' => 'Custom Code',
                'slug' => 'custom-code',
                'description' => 'Add custom HTML, CSS, and JavaScript for advanced website customization.',
                'icon' => 'edit',
                'category' => 'Website Builder',
                'goals' => json_encode([7]),
                'monthly_price' => 2.00,
                'yearly_price' => 20.00,
                'is_free' => false,
                'sort_order' => 33,
                'metadata' => json_encode(['html_css_js' => true, 'code_editor' => true])
            ],

            // Booking & Scheduling Features
            [
                'name' => 'Appointment Scheduling',
                'slug' => 'appointment-scheduling',
                'description' => 'Complete appointment scheduling with calendar integration and automated reminders.',
                'icon' => 'calendar',
                'category' => 'Booking & Scheduling',
                'goals' => json_encode([8]),
                'monthly_price' => 2.00,
                'yearly_price' => 20.00,
                'is_free' => false,
                'sort_order' => 34,
                'metadata' => json_encode(['bookings_per_month' => 500, 'reminders' => true])
            ],
            [
                'name' => 'Calendar Integration',
                'slug' => 'calendar-integration',
                'description' => 'Sync with Google Calendar, Outlook, and Apple Calendar for seamless scheduling.',
                'icon' => 'calendar',
                'category' => 'Booking & Scheduling',
                'goals' => json_encode([8]),
                'monthly_price' => 1.50,
                'yearly_price' => 15.00,
                'is_free' => false,
                'sort_order' => 35,
                'metadata' => json_encode(['providers' => ['google', 'outlook', 'apple'], 'two_way_sync' => true])
            ],
            [
                'name' => 'Payment Collection',
                'slug' => 'payment-collection-booking',
                'description' => 'Collect payments and deposits at the time of booking with automatic processing.',
                'icon' => 'dollar',
                'category' => 'Booking & Scheduling',
                'goals' => json_encode([8, 4]),
                'monthly_price' => 2.50,
                'yearly_price' => 25.00,
                'is_free' => false,
                'sort_order' => 36,
                'metadata' => json_encode(['deposits' => true, 'auto_processing' => true])
            ],

            // AI & Automation Features
            [
                'name' => 'Content Generation',
                'slug' => 'content-generation',
                'description' => 'AI-powered content generation for social media, blogs, and marketing materials.',
                'icon' => 'lightbulb',
                'category' => 'AI & Automation',
                'goals' => json_encode([9]),
                'monthly_price' => 3.00,
                'yearly_price' => 30.00,
                'is_free' => false,
                'sort_order' => 37,
                'metadata' => json_encode(['generations_per_month' => 100, 'content_types' => 10])
            ],
            [
                'name' => 'Smart Recommendations',
                'slug' => 'smart-recommendations',
                'description' => 'AI-powered recommendations for content optimization and business growth.',
                'icon' => 'lightbulb',
                'category' => 'AI & Automation',
                'goals' => json_encode([9]),
                'monthly_price' => 2.00,
                'yearly_price' => 20.00,
                'is_free' => false,
                'sort_order' => 38,
                'metadata' => json_encode(['recommendations_per_day' => 50, 'ml_powered' => true])
            ],
            [
                'name' => 'Automated Workflows',
                'slug' => 'automated-workflows',
                'description' => 'Create complex automation workflows across all platform features.',
                'icon' => 'lightbulb',
                'category' => 'AI & Automation',
                'goals' => json_encode([9]),
                'monthly_price' => 4.00,
                'yearly_price' => 40.00,
                'is_free' => false,
                'sort_order' => 39,
                'metadata' => json_encode(['workflows' => 100, 'cross_platform' => true])
            ],

            // Financial Management Features
            [
                'name' => 'Invoicing System',
                'slug' => 'invoicing-system',
                'description' => 'Professional invoicing with templates, automated reminders, and payment tracking.',
                'icon' => 'dollar',
                'category' => 'Financial Management',
                'goals' => json_encode([10]),
                'monthly_price' => 2.00,
                'yearly_price' => 20.00,
                'is_free' => false,
                'sort_order' => 40,
                'metadata' => json_encode(['invoices_per_month' => 100, 'templates' => 20])
            ],
            [
                'name' => 'Expense Tracking',
                'slug' => 'expense-tracking',
                'description' => 'Track business expenses with receipt scanning and categorization.',
                'icon' => 'chart',
                'category' => 'Financial Management',
                'goals' => json_encode([10]),
                'monthly_price' => 1.50,
                'yearly_price' => 15.00,
                'is_free' => false,
                'sort_order' => 41,
                'metadata' => json_encode(['receipt_scanning' => true, 'categories' => 50])
            ],
            [
                'name' => 'Financial Reporting',
                'slug' => 'financial-reporting',
                'description' => 'Comprehensive financial reports with profit & loss, cash flow, and tax preparation.',
                'icon' => 'chart',
                'category' => 'Financial Management',
                'goals' => json_encode([10, 6]),
                'monthly_price' => 2.50,
                'yearly_price' => 25.00,
                'is_free' => false,
                'sort_order' => 42,
                'metadata' => json_encode(['reports' => 15, 'tax_prep' => true])
            ],

            // Additional Advanced Features
            [
                'name' => 'White Label Solutions',
                'slug' => 'white-label-solutions',
                'description' => 'Complete white-label platform with custom branding and domain.',
                'icon' => 'globe',
                'category' => 'Enterprise',
                'goals' => json_encode([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]),
                'monthly_price' => 10.00,
                'yearly_price' => 100.00,
                'is_free' => false,
                'sort_order' => 43,
                'metadata' => json_encode(['custom_branding' => true, 'custom_domain' => true])
            ],
            [
                'name' => 'API Access',
                'slug' => 'api-access',
                'description' => 'Full API access for custom integrations and third-party applications.',
                'icon' => 'link',
                'category' => 'Enterprise',
                'goals' => json_encode([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]),
                'monthly_price' => 5.00,
                'yearly_price' => 50.00,
                'is_free' => false,
                'sort_order' => 44,
                'metadata' => json_encode(['rate_limits' => 'high', 'webhook_support' => true])
            ],
            [
                'name' => 'Priority Support',
                'slug' => 'priority-support',
                'description' => '24/7 priority support with dedicated account manager and phone support.',
                'icon' => 'chat',
                'category' => 'Enterprise',
                'goals' => json_encode([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]),
                'monthly_price' => 3.00,
                'yearly_price' => 30.00,
                'is_free' => false,
                'sort_order' => 45,
                'metadata' => json_encode(['response_time' => '1 hour', 'phone_support' => true])
            ],
            [
                'name' => 'Advanced Security',
                'slug' => 'advanced-security',
                'description' => 'Enterprise-grade security with SSO, audit logs, and compliance features.',
                'icon' => 'shield',
                'category' => 'Enterprise',
                'goals' => json_encode([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]),
                'monthly_price' => 4.00,
                'yearly_price' => 40.00,
                'is_free' => false,
                'sort_order' => 46,
                'metadata' => json_encode(['sso' => true, 'audit_logs' => true, 'compliance' => ['gdpr', 'ccpa']])
            ],
            [
                'name' => 'Multi-Language Support',
                'slug' => 'multi-language-support',
                'description' => 'Support for multiple languages with automatic translation and localization.',
                'icon' => 'globe',
                'category' => 'Enterprise',
                'goals' => json_encode([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]),
                'monthly_price' => 2.00,
                'yearly_price' => 20.00,
                'is_free' => false,
                'sort_order' => 47,
                'metadata' => json_encode(['languages' => 25, 'auto_translation' => true])
            ],
            [
                'name' => 'Advanced Integrations',
                'slug' => 'advanced-integrations',
                'description' => 'Connect with 1000+ third-party applications and services.',
                'icon' => 'link',
                'category' => 'Enterprise',
                'goals' => json_encode([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]),
                'monthly_price' => 3.00,
                'yearly_price' => 30.00,
                'is_free' => false,
                'sort_order' => 48,
                'metadata' => json_encode(['integrations' => 1000, 'zapier' => true])
            ]
        ];

        foreach ($features as $feature) {
            DB::table('features')->insert(array_merge($feature, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }
    }
}