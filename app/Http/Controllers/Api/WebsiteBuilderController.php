<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Website;
use App\Models\WebsitePage;
use App\Models\WebsiteComponent;
use App\Models\WebsiteTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WebsiteBuilderController extends Controller
{
    /**
     * Get all websites for the authenticated user
     */
    public function index(Request $request)
    {
        try {
            $websites = Website::where('user_id', $request->user()->id)
                ->with(['pages', 'template'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $websites,
                'message' => 'Websites retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve websites: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve websites'
            ], 500);
        }
    }

    /**
     * Create a new website
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255|unique:websites,domain',
            'template_id' => 'nullable|exists:website_templates,id',
            'description' => 'nullable|string|max:500',
            'settings' => 'nullable|array',
        ]);

        try {
            $website = Website::create([
                'user_id' => $request->user()->id,
                'name' => $request->name,
                'domain' => $request->domain,
                'template_id' => $request->template_id,
                'description' => $request->description,
                'settings' => $request->settings ?? [],
                'status' => 'draft',
            ]);

            // Create default home page
            $homePage = WebsitePage::create([
                'website_id' => $website->id,
                'name' => 'Home',
                'slug' => 'home',
                'title' => 'Welcome to ' . $website->name,
                'content' => [],
                'meta_description' => $request->description,
                'is_home' => true,
                'status' => 'draft',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Website created successfully',
                'data' => $website->load('pages')
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create website: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create website'
            ], 500);
        }
    }

    /**
     * Get a specific website with its pages and components
     */
    public function show(Request $request, $id)
    {
        try {
            $website = Website::where('id', $id)
                ->where('user_id', $request->user()->id)
                ->with(['pages.components', 'template'])
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => $website,
                'message' => 'Website retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve website: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Website not found'
            ], 404);
        }
    }

    /**
     * Update a website
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255|unique:websites,domain,' . $id,
            'description' => 'nullable|string|max:500',
            'settings' => 'nullable|array',
            'status' => 'nullable|in:draft,published,archived',
        ]);

        try {
            $website = Website::where('id', $id)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            $website->update([
                'name' => $request->name,
                'domain' => $request->domain,
                'description' => $request->description,
                'settings' => $request->settings ?? $website->settings,
                'status' => $request->status ?? $website->status,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Website updated successfully',
                'data' => $website
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update website: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update website'
            ], 500);
        }
    }

    /**
     * Delete a website
     */
    public function destroy(Request $request, $id)
    {
        try {
            $website = Website::where('id', $id)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            // Delete all pages and components
            foreach ($website->pages as $page) {
                $page->components()->delete();
                $page->delete();
            }

            $website->delete();

            return response()->json([
                'success' => true,
                'message' => 'Website deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete website: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete website'
            ], 500);
        }
    }

    /**
     * Get all available website templates
     */
    public function getTemplates()
    {
        try {
            $templates = WebsiteTemplate::where('is_active', true)
                ->orderBy('category')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $templates,
                'message' => 'Templates retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve templates: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve templates'
            ], 500);
        }
    }

    /**
     * Get available components for website building
     */
    public function getComponents()
    {
        try {
            $components = [
                'layout' => [
                    ['id' => 'header', 'name' => 'Header', 'icon' => 'header', 'category' => 'layout'],
                    ['id' => 'footer', 'name' => 'Footer', 'icon' => 'footer', 'category' => 'layout'],
                    ['id' => 'sidebar', 'name' => 'Sidebar', 'icon' => 'sidebar', 'category' => 'layout'],
                    ['id' => 'container', 'name' => 'Container', 'icon' => 'container', 'category' => 'layout'],
                    ['id' => 'grid', 'name' => 'Grid', 'icon' => 'grid', 'category' => 'layout'],
                    ['id' => 'columns', 'name' => 'Columns', 'icon' => 'columns', 'category' => 'layout'],
                ],
                'content' => [
                    ['id' => 'heading', 'name' => 'Heading', 'icon' => 'heading', 'category' => 'content'],
                    ['id' => 'paragraph', 'name' => 'Paragraph', 'icon' => 'paragraph', 'category' => 'content'],
                    ['id' => 'image', 'name' => 'Image', 'icon' => 'image', 'category' => 'content'],
                    ['id' => 'video', 'name' => 'Video', 'icon' => 'video', 'category' => 'content'],
                    ['id' => 'gallery', 'name' => 'Gallery', 'icon' => 'gallery', 'category' => 'content'],
                    ['id' => 'slider', 'name' => 'Slider', 'icon' => 'slider', 'category' => 'content'],
                ],
                'interactive' => [
                    ['id' => 'button', 'name' => 'Button', 'icon' => 'button', 'category' => 'interactive'],
                    ['id' => 'form', 'name' => 'Form', 'icon' => 'form', 'category' => 'interactive'],
                    ['id' => 'contact-form', 'name' => 'Contact Form', 'icon' => 'contact', 'category' => 'interactive'],
                    ['id' => 'newsletter', 'name' => 'Newsletter', 'icon' => 'newsletter', 'category' => 'interactive'],
                    ['id' => 'social-links', 'name' => 'Social Links', 'icon' => 'social', 'category' => 'interactive'],
                    ['id' => 'menu', 'name' => 'Navigation Menu', 'icon' => 'menu', 'category' => 'interactive'],
                ],
                'business' => [
                    ['id' => 'testimonials', 'name' => 'Testimonials', 'icon' => 'testimonials', 'category' => 'business'],
                    ['id' => 'team', 'name' => 'Team', 'icon' => 'team', 'category' => 'business'],
                    ['id' => 'services', 'name' => 'Services', 'icon' => 'services', 'category' => 'business'],
                    ['id' => 'pricing', 'name' => 'Pricing', 'icon' => 'pricing', 'category' => 'business'],
                    ['id' => 'faq', 'name' => 'FAQ', 'icon' => 'faq', 'category' => 'business'],
                    ['id' => 'blog', 'name' => 'Blog', 'icon' => 'blog', 'category' => 'business'],
                ],
                'ecommerce' => [
                    ['id' => 'product-showcase', 'name' => 'Product Showcase', 'icon' => 'product', 'category' => 'ecommerce'],
                    ['id' => 'product-grid', 'name' => 'Product Grid', 'icon' => 'grid', 'category' => 'ecommerce'],
                    ['id' => 'cart', 'name' => 'Shopping Cart', 'icon' => 'cart', 'category' => 'ecommerce'],
                    ['id' => 'checkout', 'name' => 'Checkout', 'icon' => 'checkout', 'category' => 'ecommerce'],
                ],
                'advanced' => [
                    ['id' => 'map', 'name' => 'Map', 'icon' => 'map', 'category' => 'advanced'],
                    ['id' => 'calendar', 'name' => 'Calendar', 'icon' => 'calendar', 'category' => 'advanced'],
                    ['id' => 'chat', 'name' => 'Live Chat', 'icon' => 'chat', 'category' => 'advanced'],
                    ['id' => 'search', 'name' => 'Search', 'icon' => 'search', 'category' => 'advanced'],
                    ['id' => 'analytics', 'name' => 'Analytics', 'icon' => 'analytics', 'category' => 'advanced'],
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $components,
                'message' => 'Components retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve components: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve components'
            ], 500);
        }
    }

    /**
     * Create a new page for a website
     */
    public function createPage(Request $request, $websiteId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'content' => 'nullable|array',
            'meta_description' => 'nullable|string|max:500',
            'settings' => 'nullable|array',
        ]);

        try {
            $website = Website::where('id', $websiteId)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            $page = WebsitePage::create([
                'website_id' => $website->id,
                'name' => $request->name,
                'slug' => $request->slug,
                'title' => $request->title,
                'content' => $request->content ?? [],
                'meta_description' => $request->meta_description,
                'settings' => $request->settings ?? [],
                'status' => 'draft',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Page created successfully',
                'data' => $page
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create page: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create page'
            ], 500);
        }
    }

    /**
     * Update a page
     */
    public function updatePage(Request $request, $websiteId, $pageId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'content' => 'nullable|array',
            'meta_description' => 'nullable|string|max:500',
            'settings' => 'nullable|array',
            'status' => 'nullable|in:draft,published,archived',
        ]);

        try {
            $website = Website::where('id', $websiteId)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            $page = WebsitePage::where('id', $pageId)
                ->where('website_id', $website->id)
                ->firstOrFail();

            $page->update([
                'name' => $request->name,
                'slug' => $request->slug,
                'title' => $request->title,
                'content' => $request->content ?? $page->content,
                'meta_description' => $request->meta_description,
                'settings' => $request->settings ?? $page->settings,
                'status' => $request->status ?? $page->status,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Page updated successfully',
                'data' => $page
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update page: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update page'
            ], 500);
        }
    }

    /**
     * Delete a page
     */
    public function deletePage(Request $request, $websiteId, $pageId)
    {
        try {
            $website = Website::where('id', $websiteId)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            $page = WebsitePage::where('id', $pageId)
                ->where('website_id', $website->id)
                ->firstOrFail();

            // Don't allow deletion of home page
            if ($page->is_home) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete home page'
                ], 400);
            }

            $page->components()->delete();
            $page->delete();

            return response()->json([
                'success' => true,
                'message' => 'Page deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete page: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete page'
            ], 500);
        }
    }

    /**
     * Publish a website
     */
    public function publish(Request $request, $id)
    {
        try {
            $website = Website::where('id', $id)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            $website->update([
                'status' => 'published',
                'published_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Website published successfully',
                'data' => $website
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to publish website: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to publish website'
            ], 500);
        }
    }

    /**
     * Get website analytics
     */
    public function getAnalytics(Request $request, $id)
    {
        try {
            $website = Website::where('id', $id)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            // Mock analytics data - in production, this would come from a real analytics service
            $analytics = [
                'overview' => [
                    'total_visits' => rand(1000, 50000),
                    'unique_visitors' => rand(500, 25000),
                    'page_views' => rand(2000, 75000),
                    'bounce_rate' => rand(20, 60) . '%',
                    'avg_session_duration' => rand(60, 300) . 's',
                    'conversion_rate' => rand(1, 15) . '%',
                ],
                'traffic_sources' => [
                    'organic' => rand(30, 60),
                    'direct' => rand(20, 40),
                    'social' => rand(10, 30),
                    'referral' => rand(5, 20),
                    'paid' => rand(5, 15),
                ],
                'top_pages' => [
                    ['page' => 'Home', 'visits' => rand(500, 5000)],
                    ['page' => 'About', 'visits' => rand(200, 2000)],
                    ['page' => 'Services', 'visits' => rand(150, 1500)],
                    ['page' => 'Contact', 'visits' => rand(100, 1000)],
                    ['page' => 'Blog', 'visits' => rand(75, 750)],
                ],
                'devices' => [
                    'desktop' => rand(40, 70),
                    'mobile' => rand(20, 50),
                    'tablet' => rand(5, 20),
                ],
                'locations' => [
                    'United States' => rand(100, 1000),
                    'United Kingdom' => rand(50, 500),
                    'Canada' => rand(30, 300),
                    'Australia' => rand(20, 200),
                    'Germany' => rand(15, 150),
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $analytics,
                'message' => 'Analytics retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve analytics: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve analytics'
            ], 500);
        }
    }
}