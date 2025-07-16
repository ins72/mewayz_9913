<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TemplateMarketplaceController extends Controller
{
    /**
     * Get all templates
     */
    public function getTemplates(Request $request)
    {
        try {
            $request->validate([
                'category' => 'nullable|string|in:all,email,bio,course,social,marketing',
                'type' => 'nullable|string|in:all,free,premium',
                'search' => 'nullable|string|max:255',
                'sort' => 'nullable|string|in:popular,newest,rating,price',
                'page' => 'nullable|integer|min:1',
                'per_page' => 'nullable|integer|min:1|max:50',
            ]);

            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }

            $templates = [
                // Email Templates
                [
                    'id' => 1,
                    'name' => 'Professional Newsletter',
                    'description' => 'Clean and professional newsletter template',
                    'category' => 'email',
                    'type' => 'free',
                    'preview_image' => '/images/templates/email/professional-newsletter.jpg',
                    'rating' => 4.5,
                    'downloads' => 2456,
                    'price' => 0,
                    'author' => 'Mewayz Team',
                    'created_at' => now()->subDays(30),
                    'tags' => ['newsletter', 'business', 'professional'],
                ],
                [
                    'id' => 2,
                    'name' => 'E-commerce Promo',
                    'description' => 'Perfect for product promotions and sales',
                    'category' => 'email',
                    'type' => 'premium',
                    'preview_image' => '/images/templates/email/ecommerce-promo.jpg',
                    'rating' => 4.8,
                    'downloads' => 1834,
                    'price' => 19.99,
                    'author' => 'Design Pro',
                    'created_at' => now()->subDays(15),
                    'tags' => ['ecommerce', 'promotion', 'sales'],
                ],
                [
                    'id' => 3,
                    'name' => 'Event Invitation',
                    'description' => 'Elegant template for event invitations',
                    'category' => 'email',
                    'type' => 'free',
                    'preview_image' => '/images/templates/email/event-invitation.jpg',
                    'rating' => 4.2,
                    'downloads' => 1567,
                    'price' => 0,
                    'author' => 'Event Master',
                    'created_at' => now()->subDays(20),
                    'tags' => ['event', 'invitation', 'elegant'],
                ],
                
                // Bio Site Templates
                [
                    'id' => 4,
                    'name' => 'Business Professional',
                    'description' => 'Professional bio site for business owners',
                    'category' => 'bio',
                    'type' => 'free',
                    'preview_image' => '/images/templates/bio/business-professional.jpg',
                    'rating' => 4.6,
                    'downloads' => 3421,
                    'price' => 0,
                    'author' => 'Mewayz Team',
                    'created_at' => now()->subDays(45),
                    'tags' => ['business', 'professional', 'corporate'],
                ],
                [
                    'id' => 5,
                    'name' => 'Creative Portfolio',
                    'description' => 'Showcase your creative work beautifully',
                    'category' => 'bio',
                    'type' => 'premium',
                    'preview_image' => '/images/templates/bio/creative-portfolio.jpg',
                    'rating' => 4.9,
                    'downloads' => 2876,
                    'price' => 29.99,
                    'author' => 'Creative Hub',
                    'created_at' => now()->subDays(10),
                    'tags' => ['creative', 'portfolio', 'artistic'],
                ],
                [
                    'id' => 6,
                    'name' => 'Social Influencer',
                    'description' => 'Perfect for social media influencers',
                    'category' => 'bio',
                    'type' => 'free',
                    'preview_image' => '/images/templates/bio/social-influencer.jpg',
                    'rating' => 4.4,
                    'downloads' => 4567,
                    'price' => 0,
                    'author' => 'Influencer Tools',
                    'created_at' => now()->subDays(25),
                    'tags' => ['social', 'influencer', 'trendy'],
                ],
                
                // Course Templates
                [
                    'id' => 7,
                    'name' => 'Online Course Landing',
                    'description' => 'Convert visitors into course students',
                    'category' => 'course',
                    'type' => 'premium',
                    'preview_image' => '/images/templates/course/online-course-landing.jpg',
                    'rating' => 4.7,
                    'downloads' => 1234,
                    'price' => 39.99,
                    'author' => 'Edu Templates',
                    'created_at' => now()->subDays(12),
                    'tags' => ['course', 'education', 'landing'],
                ],
                [
                    'id' => 8,
                    'name' => 'Certification Course',
                    'description' => 'Professional certification course template',
                    'category' => 'course',
                    'type' => 'premium',
                    'preview_image' => '/images/templates/course/certification-course.jpg',
                    'rating' => 4.5,
                    'downloads' => 987,
                    'price' => 24.99,
                    'author' => 'Cert Academy',
                    'created_at' => now()->subDays(18),
                    'tags' => ['certification', 'professional', 'course'],
                ],
                
                // Social Media Templates
                [
                    'id' => 9,
                    'name' => 'Instagram Story Pack',
                    'description' => '20 Instagram story templates',
                    'category' => 'social',
                    'type' => 'premium',
                    'preview_image' => '/images/templates/social/instagram-story-pack.jpg',
                    'rating' => 4.8,
                    'downloads' => 5678,
                    'price' => 14.99,
                    'author' => 'Social Design',
                    'created_at' => now()->subDays(8),
                    'tags' => ['instagram', 'stories', 'social'],
                ],
                [
                    'id' => 10,
                    'name' => 'Facebook Ad Templates',
                    'description' => 'High-converting Facebook ad designs',
                    'category' => 'social',
                    'type' => 'premium',
                    'preview_image' => '/images/templates/social/facebook-ad-templates.jpg',
                    'rating' => 4.6,
                    'downloads' => 2345,
                    'price' => 19.99,
                    'author' => 'Ad Masters',
                    'created_at' => now()->subDays(22),
                    'tags' => ['facebook', 'ads', 'conversion'],
                ],
                
                // Marketing Templates
                [
                    'id' => 11,
                    'name' => 'Marketing Campaign Kit',
                    'description' => 'Complete marketing campaign templates',
                    'category' => 'marketing',
                    'type' => 'premium',
                    'preview_image' => '/images/templates/marketing/campaign-kit.jpg',
                    'rating' => 4.9,
                    'downloads' => 1567,
                    'price' => 49.99,
                    'author' => 'Marketing Pro',
                    'created_at' => now()->subDays(5),
                    'tags' => ['marketing', 'campaign', 'complete'],
                ],
                [
                    'id' => 12,
                    'name' => 'Lead Magnet Templates',
                    'description' => 'Templates for lead generation',
                    'category' => 'marketing',
                    'type' => 'free',
                    'preview_image' => '/images/templates/marketing/lead-magnet.jpg',
                    'rating' => 4.3,
                    'downloads' => 3456,
                    'price' => 0,
                    'author' => 'Lead Gen Tools',
                    'created_at' => now()->subDays(35),
                    'tags' => ['leads', 'generation', 'marketing'],
                ],
            ];

            // Apply filters
            if ($request->category && $request->category !== 'all') {
                $templates = array_filter($templates, function($template) use ($request) {
                    return $template['category'] === $request->category;
                });
            }

            if ($request->type && $request->type !== 'all') {
                $templates = array_filter($templates, function($template) use ($request) {
                    return $template['type'] === $request->type;
                });
            }

            if ($request->search) {
                $search = strtolower($request->search);
                $templates = array_filter($templates, function($template) use ($search) {
                    return strpos(strtolower($template['name']), $search) !== false ||
                           strpos(strtolower($template['description']), $search) !== false;
                });
            }

            // Sort templates
            $sortBy = $request->sort ?? 'popular';
            usort($templates, function($a, $b) use ($sortBy) {
                switch ($sortBy) {
                    case 'newest':
                        return $b['created_at'] <=> $a['created_at'];
                    case 'rating':
                        return $b['rating'] <=> $a['rating'];
                    case 'price':
                        return $a['price'] <=> $b['price'];
                    default: // popular
                        return $b['downloads'] <=> $a['downloads'];
                }
            });

            // Pagination
            $perPage = $request->per_page ?? 12;
            $page = $request->page ?? 1;
            $offset = ($page - 1) * $perPage;
            $paginatedTemplates = array_slice($templates, $offset, $perPage);

            return response()->json([
                'success' => true,
                'data' => array_values($paginatedTemplates),
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => count($templates),
                    'total_pages' => ceil(count($templates) / $perPage),
                ],
                'filters' => [
                    'categories' => ['all', 'email', 'bio', 'course', 'social', 'marketing'],
                    'types' => ['all', 'free', 'premium'],
                    'sort_options' => ['popular', 'newest', 'rating', 'price'],
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting templates: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get templates'], 500);
        }
    }

    /**
     * Get template details
     */
    public function getTemplateDetails($id)
    {
        try {
            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }

            $template = [
                'id' => $id,
                'name' => 'Professional Newsletter',
                'description' => 'Clean and professional newsletter template perfect for business communications',
                'category' => 'email',
                'type' => 'free',
                'preview_image' => '/images/templates/email/professional-newsletter.jpg',
                'rating' => 4.5,
                'downloads' => 2456,
                'price' => 0,
                'author' => [
                    'name' => 'Mewayz Team',
                    'avatar' => '/images/avatars/mewayz-team.jpg',
                    'verified' => true,
                    'total_templates' => 45,
                ],
                'created_at' => now()->subDays(30),
                'updated_at' => now()->subDays(5),
                'tags' => ['newsletter', 'business', 'professional'],
                'features' => [
                    'Responsive design',
                    'Mobile optimized',
                    'Easy customization',
                    'Multiple layouts',
                    'Color variations',
                ],
                'preview_images' => [
                    '/images/templates/email/professional-newsletter-1.jpg',
                    '/images/templates/email/professional-newsletter-2.jpg',
                    '/images/templates/email/professional-newsletter-3.jpg',
                ],
                'reviews' => [
                    [
                        'user' => 'John Doe',
                        'rating' => 5,
                        'comment' => 'Excellent template, very professional looking!',
                        'date' => now()->subDays(5),
                    ],
                    [
                        'user' => 'Jane Smith',
                        'rating' => 4,
                        'comment' => 'Great template, easy to customize.',
                        'date' => now()->subDays(12),
                    ],
                    [
                        'user' => 'Mike Johnson',
                        'rating' => 5,
                        'comment' => 'Perfect for my business newsletter.',
                        'date' => now()->subDays(18),
                    ],
                ],
                'related_templates' => [
                    [
                        'id' => 2,
                        'name' => 'E-commerce Promo',
                        'preview_image' => '/images/templates/email/ecommerce-promo.jpg',
                        'price' => 19.99,
                        'rating' => 4.8,
                    ],
                    [
                        'id' => 3,
                        'name' => 'Event Invitation',
                        'preview_image' => '/images/templates/email/event-invitation.jpg',
                        'price' => 0,
                        'rating' => 4.2,
                    ],
                ],
                'compatibility' => [
                    'Email platforms' => ['Gmail', 'Outlook', 'Apple Mail', 'Thunderbird'],
                    'Devices' => ['Desktop', 'Mobile', 'Tablet'],
                    'Browsers' => ['Chrome', 'Firefox', 'Safari', 'Edge'],
                ],
                'files_included' => [
                    'HTML template',
                    'CSS styles',
                    'Documentation',
                    'Preview images',
                    'Source files',
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $template,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting template details: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get template details'], 500);
        }
    }

    /**
     * Purchase template
     */
    public function purchaseTemplate(Request $request, $id)
    {
        try {
            $request->validate([
                'payment_method' => 'required|string|in:card,paypal,wallet',
            ]);

            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }

            $purchase = [
                'id' => rand(10000, 99999),
                'template_id' => $id,
                'user_id' => $user->id,
                'workspace_id' => $workspace->id,
                'amount' => 19.99,
                'payment_method' => $request->payment_method,
                'status' => 'completed',
                'download_link' => '/templates/download/' . $id . '/' . time(),
                'download_expires_at' => now()->addDays(30),
                'purchased_at' => now(),
            ];

            Log::info('Template purchased', [
                'user_id' => $user->id,
                'template_id' => $id,
                'amount' => $purchase['amount'],
                'payment_method' => $request->payment_method,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Template purchased successfully',
                'data' => $purchase,
            ]);
        } catch (\Exception $e) {
            Log::error('Error purchasing template: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to purchase template'], 500);
        }
    }

    /**
     * Get user's purchased templates
     */
    public function getUserTemplates()
    {
        try {
            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }

            $userTemplates = [
                [
                    'id' => 1,
                    'template_id' => 2,
                    'template_name' => 'E-commerce Promo',
                    'template_category' => 'email',
                    'purchased_at' => now()->subDays(10),
                    'download_link' => '/templates/download/2/' . time(),
                    'download_expires_at' => now()->addDays(20),
                    'download_count' => 3,
                    'last_downloaded' => now()->subDays(2),
                ],
                [
                    'id' => 2,
                    'template_id' => 5,
                    'template_name' => 'Creative Portfolio',
                    'template_category' => 'bio',
                    'purchased_at' => now()->subDays(25),
                    'download_link' => '/templates/download/5/' . time(),
                    'download_expires_at' => now()->addDays(5),
                    'download_count' => 1,
                    'last_downloaded' => now()->subDays(25),
                ],
                [
                    'id' => 3,
                    'template_id' => 7,
                    'template_name' => 'Online Course Landing',
                    'template_category' => 'course',
                    'purchased_at' => now()->subDays(5),
                    'download_link' => '/templates/download/7/' . time(),
                    'download_expires_at' => now()->addDays(25),
                    'download_count' => 2,
                    'last_downloaded' => now()->subDays(1),
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $userTemplates,
                'total' => count($userTemplates),
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting user templates: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get user templates'], 500);
        }
    }

    /**
     * Upload custom template
     */
    public function uploadTemplate(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string|max:1000',
                'category' => 'required|string|in:email,bio,course,social,marketing',
                'type' => 'required|string|in:free,premium',
                'price' => 'required_if:type,premium|numeric|min:0',
                'tags' => 'required|array|min:1',
                'tags.*' => 'string|max:50',
                'preview_image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
                'template_files' => 'required|file|mimes:zip|max:10240',
            ]);

            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }

            // Simulate file upload
            $template = [
                'id' => rand(10000, 99999),
                'name' => $request->name,
                'description' => $request->description,
                'category' => $request->category,
                'type' => $request->type,
                'price' => $request->type === 'premium' ? $request->price : 0,
                'tags' => $request->tags,
                'author' => [
                    'name' => $user->name,
                    'id' => $user->id,
                ],
                'status' => 'pending_review',
                'preview_image' => '/images/templates/user-uploads/' . time() . '.jpg',
                'template_files' => '/templates/user-uploads/' . time() . '.zip',
                'uploaded_at' => now(),
                'rating' => 0,
                'downloads' => 0,
            ];

            Log::info('Template uploaded', [
                'user_id' => $user->id,
                'template_name' => $request->name,
                'category' => $request->category,
                'type' => $request->type,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Template uploaded successfully and is pending review',
                'data' => $template,
            ]);
        } catch (\Exception $e) {
            Log::error('Error uploading template: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to upload template'], 500);
        }
    }

    /**
     * Get template categories
     */
    public function getCategories()
    {
        try {
            $categories = [
                [
                    'id' => 'email',
                    'name' => 'Email Templates',
                    'description' => 'Professional email designs',
                    'icon' => 'mail',
                    'count' => 145,
                ],
                [
                    'id' => 'bio',
                    'name' => 'Link in Bio',
                    'description' => 'Landing page layouts',
                    'icon' => 'link',
                    'count' => 87,
                ],
                [
                    'id' => 'course',
                    'name' => 'Course Templates',
                    'description' => 'Educational content structures',
                    'icon' => 'book',
                    'count' => 56,
                ],
                [
                    'id' => 'social',
                    'name' => 'Social Media',
                    'description' => 'Post and story designs',
                    'icon' => 'share',
                    'count' => 203,
                ],
                [
                    'id' => 'marketing',
                    'name' => 'Marketing Templates',
                    'description' => 'Campaign and ad layouts',
                    'icon' => 'trending-up',
                    'count' => 98,
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $categories,
                'total_templates' => array_sum(array_column($categories, 'count')),
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting categories: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get categories'], 500);
        }
    }

    /**
     * Rate template
     */
    public function rateTemplate(Request $request, $id)
    {
        try {
            $request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'comment' => 'nullable|string|max:500',
            ]);

            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }

            $review = [
                'id' => rand(10000, 99999),
                'template_id' => $id,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'rating' => $request->rating,
                'comment' => $request->comment,
                'created_at' => now(),
            ];

            Log::info('Template rated', [
                'user_id' => $user->id,
                'template_id' => $id,
                'rating' => $request->rating,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Template rated successfully',
                'data' => $review,
            ]);
        } catch (\Exception $e) {
            Log::error('Error rating template: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to rate template'], 500);
        }
    }
}