<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TemplateCategory;
use App\Models\Template;
use App\Models\User;

class TemplateMarketplaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create template categories
        $this->createTemplateCategories();
        
        // Create sample templates
        $this->createSampleTemplates();
    }

    private function createTemplateCategories()
    {
        $categories = [
            [
                'name' => 'Email Templates',
                'slug' => 'email-templates',
                'description' => 'Professional email templates for marketing campaigns',
                'icon' => '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/></svg>',
                'color' => '#3B82F6',
                'sort_order' => 1,
            ],
            [
                'name' => 'Bio Page Templates',
                'slug' => 'bio-page-templates',
                'description' => 'Stunning bio page templates for social media',
                'icon' => '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>',
                'color' => '#10B981',
                'sort_order' => 2,
            ],
            [
                'name' => 'Landing Page Templates',
                'slug' => 'landing-page-templates',
                'description' => 'High-converting landing page templates',
                'icon' => '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" clip-rule="evenodd"/></svg>',
                'color' => '#F59E0B',
                'sort_order' => 3,
            ],
            [
                'name' => 'Course Templates',
                'slug' => 'course-templates',
                'description' => 'Educational course templates and layouts',
                'icon' => '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                'color' => '#8B5CF6',
                'sort_order' => 4,
            ],
            [
                'name' => 'Social Media Templates',
                'slug' => 'social-media-templates',
                'description' => 'Templates for social media posts and stories',
                'icon' => '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 5a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2h-2.22l.123.489.804.804A1 1 0 0113 18H7a1 1 0 01-.707-1.707l.804-.804L7.22 15H5a2 2 0 01-2-2V5zm5.771 7H5V5h10v7H8.771z" clip-rule="evenodd"/></svg>',
                'color' => '#EF4444',
                'sort_order' => 5,
            ],
            [
                'name' => 'Marketing Templates',
                'slug' => 'marketing-templates',
                'description' => 'Marketing campaign templates and designs',
                'icon' => '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"/></svg>',
                'color' => '#06B6D4',
                'sort_order' => 6,
            ],
        ];

        foreach ($categories as $category) {
            TemplateCategory::create($category);
        }
    }

    private function createSampleTemplates()
    {
        // Get the first user (admin) to be the creator
        $user = User::first();
        
        if (!$user) {
            return;
        }

        $templates = [
            // Email Templates
            [
                'name' => 'Welcome Email Template',
                'description' => 'A professional welcome email template for new users',
                'type' => 'email',
                'category_id' => TemplateCategory::where('slug', 'email-templates')->first()->id,
                'user_id' => $user->id,
                'template_data' => [
                    'subject' => 'Welcome to {{company_name}}!',
                    'html' => '<html><body><h1>Welcome!</h1><p>Thanks for joining us.</p></body></html>',
                    'variables' => ['company_name', 'user_name', 'activation_link'],
                ],
                'tags' => ['welcome', 'onboarding', 'professional'],
                'price' => 0.00,
                'status' => 'published',
                'is_featured' => true,
                'rating' => 4.5,
                'rating_count' => 25,
                'downloads' => 150,
            ],
            [
                'name' => 'Newsletter Template',
                'description' => 'Modern newsletter template with sections for news and updates',
                'type' => 'email',
                'category_id' => TemplateCategory::where('slug', 'email-templates')->first()->id,
                'user_id' => $user->id,
                'template_data' => [
                    'subject' => '{{newsletter_title}} - {{month}} {{year}}',
                    'html' => '<html><body><div class="newsletter"><h2>{{newsletter_title}}</h2><div class="content">{{newsletter_content}}</div></div></body></html>',
                    'variables' => ['newsletter_title', 'month', 'year', 'newsletter_content'],
                ],
                'tags' => ['newsletter', 'updates', 'modern'],
                'price' => 5.00,
                'status' => 'published',
                'is_premium' => true,
                'rating' => 4.2,
                'rating_count' => 18,
                'downloads' => 89,
            ],
            
            // Bio Page Templates
            [
                'name' => 'Creator Bio Page',
                'description' => 'Perfect bio page template for content creators and influencers',
                'type' => 'bio-page',
                'category_id' => TemplateCategory::where('slug', 'bio-page-templates')->first()->id,
                'user_id' => $user->id,
                'template_data' => [
                    'layout' => 'single-column',
                    'sections' => [
                        'header' => ['avatar', 'name', 'bio'],
                        'links' => ['youtube', 'instagram', 'tiktok', 'website'],
                        'footer' => ['contact', 'copyright'],
                    ],
                    'colors' => ['primary' => '#3B82F6', 'secondary' => '#10B981'],
                ],
                'tags' => ['creator', 'influencer', 'social-media'],
                'price' => 0.00,
                'status' => 'published',
                'is_featured' => true,
                'rating' => 4.8,
                'rating_count' => 42,
                'downloads' => 320,
            ],
            [
                'name' => 'Business Bio Page',
                'description' => 'Professional bio page template for businesses and entrepreneurs',
                'type' => 'bio-page',
                'category_id' => TemplateCategory::where('slug', 'bio-page-templates')->first()->id,
                'user_id' => $user->id,
                'template_data' => [
                    'layout' => 'two-column',
                    'sections' => [
                        'header' => ['logo', 'company_name', 'tagline'],
                        'about' => ['description', 'services'],
                        'contact' => ['phone', 'email', 'address'],
                        'social' => ['linkedin', 'twitter', 'facebook'],
                    ],
                    'colors' => ['primary' => '#1F2937', 'secondary' => '#F59E0B'],
                ],
                'tags' => ['business', 'professional', 'entrepreneur'],
                'price' => 10.00,
                'status' => 'published',
                'is_premium' => true,
                'rating' => 4.6,
                'rating_count' => 31,
                'downloads' => 185,
            ],
            
            // Landing Page Templates
            [
                'name' => 'Product Launch Landing Page',
                'description' => 'High-converting landing page template for product launches',
                'type' => 'landing-page',
                'category_id' => TemplateCategory::where('slug', 'landing-page-templates')->first()->id,
                'user_id' => $user->id,
                'template_data' => [
                    'layout' => 'hero-features-testimonials-cta',
                    'sections' => [
                        'hero' => ['headline', 'subheadline', 'hero_image', 'cta_button'],
                        'features' => ['feature_1', 'feature_2', 'feature_3'],
                        'testimonials' => ['testimonial_1', 'testimonial_2', 'testimonial_3'],
                        'pricing' => ['price', 'features_list', 'cta_button'],
                    ],
                    'colors' => ['primary' => '#7C3AED', 'secondary' => '#EC4899'],
                ],
                'tags' => ['product-launch', 'conversion', 'marketing'],
                'price' => 15.00,
                'status' => 'published',
                'is_premium' => true,
                'rating' => 4.7,
                'rating_count' => 28,
                'downloads' => 95,
            ],
            
            // Course Templates
            [
                'name' => 'Online Course Template',
                'description' => 'Complete course template with lessons, quizzes, and certificates',
                'type' => 'course',
                'category_id' => TemplateCategory::where('slug', 'course-templates')->first()->id,
                'user_id' => $user->id,
                'template_data' => [
                    'structure' => [
                        'modules' => 5,
                        'lessons_per_module' => 3,
                        'quizzes_per_module' => 1,
                        'final_exam' => true,
                    ],
                    'components' => ['video_player', 'text_content', 'quiz_engine', 'progress_tracker'],
                    'certification' => ['enabled' => true, 'template' => 'professional'],
                ],
                'tags' => ['education', 'online-learning', 'certification'],
                'price' => 0.00,
                'status' => 'published',
                'is_featured' => true,
                'rating' => 4.4,
                'rating_count' => 22,
                'downloads' => 78,
            ],
            
            // Social Media Templates
            [
                'name' => 'Instagram Story Template Pack',
                'description' => 'Collection of Instagram story templates for different occasions',
                'type' => 'social-media',
                'category_id' => TemplateCategory::where('slug', 'social-media-templates')->first()->id,
                'user_id' => $user->id,
                'template_data' => [
                    'format' => 'instagram-story',
                    'dimensions' => '1080x1920',
                    'templates' => [
                        'quote' => ['background', 'text_overlay', 'author'],
                        'product' => ['product_image', 'title', 'price', 'cta'],
                        'announcement' => ['headline', 'details', 'background'],
                    ],
                    'colors' => ['primary' => '#E1306C', 'secondary' => '#FFDC80'],
                ],
                'tags' => ['instagram', 'stories', 'social-media'],
                'price' => 8.00,
                'status' => 'published',
                'is_premium' => true,
                'rating' => 4.3,
                'rating_count' => 35,
                'downloads' => 125,
            ],
        ];

        foreach ($templates as $template) {
            Template::create($template);
        }
    }
}
