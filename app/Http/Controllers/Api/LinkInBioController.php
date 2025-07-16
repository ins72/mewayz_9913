<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class LinkInBioController extends Controller
{
    /**
     * Get bio sites for user
     */
    public function getBioSites(Request $request)
    {
        try {
            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }

            $bioSites = [
                [
                    'id' => 1,
                    'name' => 'Main Business Profile',
                    'url' => 'mewayz.bio/main-business',
                    'status' => 'active',
                    'template' => 'business-modern',
                    'visits' => 1247,
                    'clicks' => 156,
                    'conversion_rate' => 12.5,
                    'created_at' => now()->subDays(30),
                    'updated_at' => now()->subDays(2),
                ],
                [
                    'id' => 2,
                    'name' => 'Product Launch',
                    'url' => 'mewayz.bio/product-launch',
                    'status' => 'active',
                    'template' => 'product-showcase',
                    'visits' => 892,
                    'clicks' => 89,
                    'conversion_rate' => 10.0,
                    'created_at' => now()->subDays(15),
                    'updated_at' => now()->subDays(1),
                ],
                [
                    'id' => 3,
                    'name' => 'Social Media Hub',
                    'url' => 'mewayz.bio/social-hub',
                    'status' => 'draft',
                    'template' => 'social-links',
                    'visits' => 0,
                    'clicks' => 0,
                    'conversion_rate' => 0,
                    'created_at' => now()->subDays(5),
                    'updated_at' => now()->subDays(5),
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $bioSites,
                'total' => count($bioSites),
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting bio sites: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get bio sites'], 500);
        }
    }

    /**
     * Get bio site builder interface
     */
    public function getBioSiteBuilder($id)
    {
        try {
            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }

            $bioSite = [
                'id' => $id,
                'name' => 'Main Business Profile',
                'url' => 'mewayz.bio/main-business',
                'template' => 'business-modern',
                'components' => [
                    [
                        'id' => 'comp_1',
                        'type' => 'header',
                        'position' => 1,
                        'data' => [
                            'title' => 'Welcome to My Business',
                            'subtitle' => 'Professional services for modern businesses',
                            'image' => '/images/profile-header.jpg',
                            'style' => 'center'
                        ]
                    ],
                    [
                        'id' => 'comp_2',
                        'type' => 'link_button',
                        'position' => 2,
                        'data' => [
                            'text' => 'Book a Consultation',
                            'url' => 'https://calendly.com/mybusiness',
                            'style' => 'primary',
                            'icon' => 'calendar'
                        ]
                    ],
                    [
                        'id' => 'comp_3',
                        'type' => 'link_button',
                        'position' => 3,
                        'data' => [
                            'text' => 'View Portfolio',
                            'url' => 'https://mybusiness.com/portfolio',
                            'style' => 'secondary',
                            'icon' => 'briefcase'
                        ]
                    ],
                    [
                        'id' => 'comp_4',
                        'type' => 'social_links',
                        'position' => 4,
                        'data' => [
                            'links' => [
                                ['platform' => 'instagram', 'url' => 'https://instagram.com/mybusiness'],
                                ['platform' => 'twitter', 'url' => 'https://twitter.com/mybusiness'],
                                ['platform' => 'linkedin', 'url' => 'https://linkedin.com/company/mybusiness'],
                            ]
                        ]
                    ],
                    [
                        'id' => 'comp_5',
                        'type' => 'contact_form',
                        'position' => 5,
                        'data' => [
                            'title' => 'Get in Touch',
                            'fields' => ['name', 'email', 'message'],
                            'submit_text' => 'Send Message'
                        ]
                    ]
                ],
                'settings' => [
                    'theme' => 'dark',
                    'background_color' => '#1a1a1a',
                    'text_color' => '#ffffff',
                    'accent_color' => '#3b82f6',
                    'font_family' => 'Inter',
                    'custom_css' => ''
                ],
                'seo' => [
                    'title' => 'My Business - Professional Services',
                    'description' => 'Professional business services for modern companies',
                    'keywords' => 'business, services, professional, consulting',
                    'favicon' => '/images/favicon.png'
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $bioSite,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting bio site builder: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get bio site builder'], 500);
        }
    }

    /**
     * Get available templates
     */
    public function getTemplates()
    {
        try {
            $templates = [
                [
                    'id' => 'business-modern',
                    'name' => 'Business Modern',
                    'description' => 'Professional template for business use',
                    'category' => 'business',
                    'preview_image' => '/images/templates/business-modern.jpg',
                    'components' => ['header', 'link_button', 'social_links', 'contact_form'],
                    'is_premium' => false,
                ],
                [
                    'id' => 'product-showcase',
                    'name' => 'Product Showcase',
                    'description' => 'Perfect for product launches and showcases',
                    'category' => 'ecommerce',
                    'preview_image' => '/images/templates/product-showcase.jpg',
                    'components' => ['header', 'product_grid', 'link_button', 'testimonials'],
                    'is_premium' => false,
                ],
                [
                    'id' => 'social-links',
                    'name' => 'Social Links',
                    'description' => 'Simple template for social media links',
                    'category' => 'social',
                    'preview_image' => '/images/templates/social-links.jpg',
                    'components' => ['header', 'social_links', 'link_button'],
                    'is_premium' => false,
                ],
                [
                    'id' => 'creator-portfolio',
                    'name' => 'Creator Portfolio',
                    'description' => 'Showcase your creative work',
                    'category' => 'portfolio',
                    'preview_image' => '/images/templates/creator-portfolio.jpg',
                    'components' => ['header', 'gallery', 'link_button', 'about_section'],
                    'is_premium' => true,
                ],
                [
                    'id' => 'course-landing',
                    'name' => 'Course Landing',
                    'description' => 'Perfect for course creators',
                    'category' => 'education',
                    'preview_image' => '/images/templates/course-landing.jpg',
                    'components' => ['header', 'course_list', 'testimonials', 'pricing_table'],
                    'is_premium' => true,
                ],
                [
                    'id' => 'restaurant-menu',
                    'name' => 'Restaurant Menu',
                    'description' => 'Digital menu for restaurants',
                    'category' => 'food',
                    'preview_image' => '/images/templates/restaurant-menu.jpg',
                    'components' => ['header', 'menu_items', 'contact_info', 'booking_form'],
                    'is_premium' => true,
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $templates,
                'categories' => ['business', 'ecommerce', 'social', 'portfolio', 'education', 'food'],
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting templates: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get templates'], 500);
        }
    }

    /**
     * Get available components
     */
    public function getComponents()
    {
        try {
            $components = [
                [
                    'type' => 'header',
                    'name' => 'Header',
                    'description' => 'Profile header with title and image',
                    'icon' => 'header',
                    'settings' => [
                        'title' => 'text',
                        'subtitle' => 'text',
                        'image' => 'image',
                        'style' => 'select:center,left,right'
                    ]
                ],
                [
                    'type' => 'link_button',
                    'name' => 'Link Button',
                    'description' => 'Clickable button with custom URL',
                    'icon' => 'link',
                    'settings' => [
                        'text' => 'text',
                        'url' => 'url',
                        'style' => 'select:primary,secondary,outline',
                        'icon' => 'icon'
                    ]
                ],
                [
                    'type' => 'social_links',
                    'name' => 'Social Links',
                    'description' => 'Social media platform links',
                    'icon' => 'share',
                    'settings' => [
                        'links' => 'array',
                        'style' => 'select:icons,buttons,list'
                    ]
                ],
                [
                    'type' => 'contact_form',
                    'name' => 'Contact Form',
                    'description' => 'Contact form with custom fields',
                    'icon' => 'mail',
                    'settings' => [
                        'title' => 'text',
                        'fields' => 'multiselect:name,email,phone,message',
                        'submit_text' => 'text'
                    ]
                ],
                [
                    'type' => 'gallery',
                    'name' => 'Image Gallery',
                    'description' => 'Photo gallery with lightbox',
                    'icon' => 'image',
                    'settings' => [
                        'images' => 'array',
                        'columns' => 'select:2,3,4',
                        'spacing' => 'select:tight,normal,loose'
                    ]
                ],
                [
                    'type' => 'video',
                    'name' => 'Video Player',
                    'description' => 'Embedded video player',
                    'icon' => 'play',
                    'settings' => [
                        'url' => 'url',
                        'autoplay' => 'boolean',
                        'controls' => 'boolean'
                    ]
                ],
                [
                    'type' => 'testimonials',
                    'name' => 'Testimonials',
                    'description' => 'Customer testimonials slider',
                    'icon' => 'quote',
                    'settings' => [
                        'testimonials' => 'array',
                        'style' => 'select:cards,quotes,minimal'
                    ]
                ],
                [
                    'type' => 'pricing_table',
                    'name' => 'Pricing Table',
                    'description' => 'Pricing plans comparison',
                    'icon' => 'dollar-sign',
                    'settings' => [
                        'plans' => 'array',
                        'style' => 'select:cards,table,minimal'
                    ]
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $components,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting components: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get components'], 500);
        }
    }

    /**
     * Save bio site
     */
    public function saveBioSite(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'components' => 'required|array',
                'settings' => 'required|array',
                'seo' => 'nullable|array',
            ]);

            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }

            // Simulate saving bio site
            $bioSite = [
                'id' => $id,
                'name' => $request->name,
                'url' => 'mewayz.bio/' . \Str::slug($request->name),
                'components' => $request->components,
                'settings' => $request->settings,
                'seo' => $request->seo,
                'updated_at' => now(),
            ];

            Log::info('Bio site saved', [
                'user_id' => $user->id,
                'workspace_id' => $workspace->id,
                'bio_site_id' => $id,
                'components_count' => count($request->components),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bio site saved successfully',
                'data' => $bioSite,
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving bio site: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to save bio site'], 500);
        }
    }

    /**
     * Create new bio site
     */
    public function createBioSite(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'template' => 'required|string',
            ]);

            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }

            $bioSite = [
                'id' => rand(1000, 9999),
                'name' => $request->name,
                'url' => 'mewayz.bio/' . \Str::slug($request->name),
                'status' => 'draft',
                'template' => $request->template,
                'visits' => 0,
                'clicks' => 0,
                'conversion_rate' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Bio site created successfully',
                'data' => $bioSite,
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating bio site: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create bio site'], 500);
        }
    }

    /**
     * Get bio site analytics
     */
    public function getBioSiteAnalytics($id)
    {
        try {
            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }

            $analytics = [
                'overview' => [
                    'total_visits' => 1247,
                    'total_clicks' => 156,
                    'conversion_rate' => 12.5,
                    'bounce_rate' => 23.4,
                    'average_time' => 145, // seconds
                ],
                'traffic_sources' => [
                    ['source' => 'Instagram', 'visits' => 567, 'percentage' => 45.5],
                    ['source' => 'Twitter', 'visits' => 234, 'percentage' => 18.8],
                    ['source' => 'Direct', 'visits' => 189, 'percentage' => 15.2],
                    ['source' => 'Google', 'visits' => 145, 'percentage' => 11.6],
                    ['source' => 'Other', 'visits' => 112, 'percentage' => 8.9],
                ],
                'top_links' => [
                    ['name' => 'Book Consultation', 'clicks' => 67, 'percentage' => 43.0],
                    ['name' => 'View Portfolio', 'clicks' => 45, 'percentage' => 28.8],
                    ['name' => 'Contact Form', 'clicks' => 28, 'percentage' => 17.9],
                    ['name' => 'Social Links', 'clicks' => 16, 'percentage' => 10.3],
                ],
                'visits_over_time' => [
                    ['date' => '2025-01-01', 'visits' => 89],
                    ['date' => '2025-01-02', 'visits' => 95],
                    ['date' => '2025-01-03', 'visits' => 78],
                    ['date' => '2025-01-04', 'visits' => 112],
                    ['date' => '2025-01-05', 'visits' => 134],
                    ['date' => '2025-01-06', 'visits' => 156],
                    ['date' => '2025-01-07', 'visits' => 178],
                ],
                'device_breakdown' => [
                    ['device' => 'Mobile', 'visits' => 748, 'percentage' => 60.0],
                    ['device' => 'Desktop', 'visits' => 374, 'percentage' => 30.0],
                    ['device' => 'Tablet', 'visits' => 125, 'percentage' => 10.0],
                ],
                'geographic_data' => [
                    ['country' => 'United States', 'visits' => 623, 'percentage' => 50.0],
                    ['country' => 'Canada', 'visits' => 125, 'percentage' => 10.0],
                    ['country' => 'United Kingdom', 'visits' => 100, 'percentage' => 8.0],
                    ['country' => 'Australia', 'visits' => 87, 'percentage' => 7.0],
                    ['country' => 'Other', 'visits' => 312, 'percentage' => 25.0],
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $analytics,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting bio site analytics: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get bio site analytics'], 500);
        }
    }

    /**
     * A/B test bio site
     */
    public function abTestBioSite(Request $request, $id)
    {
        try {
            $request->validate([
                'test_name' => 'required|string|max:255',
                'variant_a' => 'required|array',
                'variant_b' => 'required|array',
                'traffic_split' => 'required|integer|min:10|max:90',
                'duration_days' => 'required|integer|min:1|max:30',
            ]);

            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }

            $abTest = [
                'id' => rand(1000, 9999),
                'bio_site_id' => $id,
                'test_name' => $request->test_name,
                'status' => 'active',
                'variant_a' => $request->variant_a,
                'variant_b' => $request->variant_b,
                'traffic_split' => $request->traffic_split,
                'duration_days' => $request->duration_days,
                'start_date' => now(),
                'end_date' => now()->addDays($request->duration_days),
                'results' => [
                    'variant_a' => [
                        'visits' => 156,
                        'clicks' => 23,
                        'conversion_rate' => 14.7,
                    ],
                    'variant_b' => [
                        'visits' => 144,
                        'clicks' => 19,
                        'conversion_rate' => 13.2,
                    ],
                ],
                'created_at' => now(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'A/B test created successfully',
                'data' => $abTest,
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating A/B test: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create A/B test'], 500);
        }
    }
}