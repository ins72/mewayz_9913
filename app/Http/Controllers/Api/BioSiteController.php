<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BioSite;
use App\Models\BioSiteLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BioSiteController extends Controller
{
    /**
     * Get all bio sites for the authenticated user
     */
    public function index(Request $request)
    {
        try {
            $bioSites = BioSite::with(['links' => function ($query) {
                $query->where('is_active', true)->orderBy('sort_order');
            }])
            ->where('user_id', $request->user()->id)
            ->select(['id', 'title', 'slug', 'description', 'status', 'template_id', 'theme_config', 'view_count', 'click_count', 'created_at', 'updated_at'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($bioSite) {
                return [
                    'id' => $bioSite->id,
                    'title' => $bioSite->title,
                    'slug' => $bioSite->slug,
                    'description' => $bioSite->description,
                    'status' => $bioSite->status,
                    'template_id' => $bioSite->template_id,
                    'theme_config' => $bioSite->theme_config ? json_decode($bioSite->theme_config, true) : null,
                    'links_count' => $bioSite->links->count(),
                    'view_count' => $bioSite->view_count ?? 0,
                    'click_count' => $bioSite->click_count ?? 0,
                    'url' => url("/bio/{$bioSite->slug}"),
                    'qr_code' => url("/api/bio-sites/{$bioSite->id}/qr-code"),
                    'created_at' => $bioSite->created_at,
                    'updated_at' => $bioSite->updated_at,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $bioSites,
                'message' => 'Bio sites retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve bio sites: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve bio sites'
            ], 500);
        }
    }

    /**
     * Create a new bio site
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:bio_sites,slug|regex:/^[a-zA-Z0-9-_]+$/',
            'description' => 'nullable|string|max:500',
            'theme' => 'required|string|in:minimal,modern,gradient,neon,elegant,creative,professional,dark,light,colorful',
            'is_active' => 'boolean',
            'profile_image' => 'nullable|string',
            'cover_image' => 'nullable|string',
            'custom_css' => 'nullable|string|max:10000',
            'custom_js' => 'nullable|string|max:5000',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
            'seo_keywords' => 'nullable|string|max:500',
            'google_analytics_id' => 'nullable|string|max:50',
            'facebook_pixel_id' => 'nullable|string|max:50',
            'custom_domain' => 'nullable|string|max:255|unique:bio_sites,custom_domain',
            'password_protection' => 'boolean',
            'password' => 'nullable|string|min:6|required_if:password_protection,true',
            'social_links' => 'nullable|array',
            'social_links.*.platform' => 'required|string|in:instagram,facebook,twitter,linkedin,youtube,tiktok,snapchat,discord,twitch,github,behance,dribbble,pinterest,whatsapp,telegram,email',
            'social_links.*.url' => 'required|url',
            'social_links.*.display_name' => 'nullable|string|max:50',
            'branding' => 'nullable|array',
            'branding.primary_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'branding.secondary_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'branding.accent_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'branding.text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'branding.background_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'branding.font_family' => 'nullable|string|in:Inter,Roboto,Open Sans,Lato,Montserrat,Poppins,Nunito,Source Sans Pro,Raleway,Ubuntu',
            'branding.font_size' => 'nullable|integer|min:12|max:24',
            'advanced_features' => 'nullable|array',
            'advanced_features.email_capture' => 'boolean',
            'advanced_features.email_capture_text' => 'nullable|string|max:255',
            'advanced_features.contact_form' => 'boolean',
            'advanced_features.appointment_booking' => 'boolean',
            'advanced_features.music_player' => 'boolean',
            'advanced_features.countdown_timer' => 'boolean',
            'advanced_features.countdown_end_date' => 'nullable|date|after:now',
            'advanced_features.age_gate' => 'boolean',
            'advanced_features.age_gate_message' => 'nullable|string|max:255',
            'advanced_features.cookie_consent' => 'boolean',
            'advanced_features.gdpr_compliant' => 'boolean'
        ]);

        try {
            $bioSite = BioSite::create([
                'user_id' => auth()->id(),
                'name' => $request->name,
                'slug' => $request->slug,
                'description' => $request->description,
                'theme' => $request->theme,
                'is_active' => $request->is_active ?? true,
                'profile_image' => $request->profile_image,
                'cover_image' => $request->cover_image,
                'custom_css' => $request->custom_css,
                'custom_js' => $request->custom_js,
                'seo_title' => $request->seo_title ?? $request->name,
                'seo_description' => $request->seo_description ?? $request->description,
                'seo_keywords' => $request->seo_keywords,
                'google_analytics_id' => $request->google_analytics_id,
                'facebook_pixel_id' => $request->facebook_pixel_id,
                'custom_domain' => $request->custom_domain,
                'password_protection' => $request->password_protection ?? false,
                'password' => $request->password_protection ? Hash::make($request->password) : null,
                'social_links' => $request->social_links ?? [],
                'branding' => array_merge([
                    'primary_color' => '#3B82F6',
                    'secondary_color' => '#1E40AF',
                    'accent_color' => '#10B981',
                    'text_color' => '#1F2937',
                    'background_color' => '#FFFFFF',
                    'font_family' => 'Inter',
                    'font_size' => 16
                ], $request->branding ?? []),
                'advanced_features' => array_merge([
                    'email_capture' => false,
                    'email_capture_text' => 'Stay updated with my latest content',
                    'contact_form' => false,
                    'appointment_booking' => false,
                    'music_player' => false,
                    'countdown_timer' => false,
                    'countdown_end_date' => null,
                    'age_gate' => false,
                    'age_gate_message' => 'You must be 18 or older to view this content',
                    'cookie_consent' => false,
                    'gdpr_compliant' => false
                ], $request->advanced_features ?? [])
            ]);

            // Generate QR code for the bio site
            $qrCodeUrl = $this->generateQRCode($bioSite->slug);
            $bioSite->update(['qr_code_url' => $qrCodeUrl]);

            return response()->json([
                'success' => true,
                'message' => 'Bio site created successfully with advanced features',
                'data' => [
                    'id' => $bioSite->id,
                    'name' => $bioSite->name,
                    'slug' => $bioSite->slug,
                    'url' => url('/bio/' . $bioSite->slug),
                    'custom_domain_url' => $bioSite->custom_domain ? 'https://' . $bioSite->custom_domain : null,
                    'qr_code_url' => $bioSite->qr_code_url,
                    'theme' => $bioSite->theme,
                    'is_active' => $bioSite->is_active,
                    'password_protected' => $bioSite->password_protection,
                    'social_links_count' => count($bioSite->social_links),
                    'advanced_features_enabled' => array_filter($bioSite->advanced_features, function($value) {
                        return $value === true;
                    }),
                    'created_at' => $bioSite->created_at,
                    'updated_at' => $bioSite->updated_at
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Bio site creation failed', ['error' => $e->getMessage(), 'user_id' => auth()->id()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create bio site: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific bio site with its links
     */
    public function show($id)
    {
        try {
            $bioSite = BioSite::with(['links' => function ($query) {
                $query->where('is_active', true)->orderBy('sort_order');
            }])
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

            if (!$bioSite) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bio site not found or unauthorized'
                ], 404);
            }

            $data = [
                'id' => $bioSite->id,
                'title' => $bioSite->title,
                'slug' => $bioSite->slug,
                'description' => $bioSite->description,
                'status' => $bioSite->status,
                'template_id' => $bioSite->template_id,
                'theme_config' => $bioSite->theme_config ? json_decode($bioSite->theme_config, true) : null,
                'view_count' => $bioSite->view_count ?? 0,
                'click_count' => $bioSite->click_count ?? 0,
                'url' => url("/bio/{$bioSite->slug}"),
                'qr_code' => url("/api/bio-sites/{$bioSite->id}/qr-code"),
                'links' => $bioSite->links->map(function ($link) {
                    return [
                        'id' => $link->id,
                        'title' => $link->title,
                        'url' => $link->url,
                        'description' => $link->description,
                        'type' => $link->type,
                        'icon' => $link->icon,
                        'click_count' => $link->click_count ?? 0,
                        'sort_order' => $link->sort_order,
                        'is_active' => $link->is_active,
                        'created_at' => $link->created_at,
                    ];
                }),
                'created_at' => $bioSite->created_at,
                'updated_at' => $bioSite->updated_at,
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Bio site retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve bio site: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve bio site'
            ], 500);
        }
    }

    /**
     * Update a bio site
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|regex:/^[a-zA-Z0-9\-_]+$/|unique:bio_sites,slug,' . $id,
            'description' => 'nullable|string|max:500',
            'template_id' => 'nullable|integer',
            'theme_config' => 'nullable|array',
            'status' => 'nullable|in:draft,published,archived',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $bioSite = BioSite::where('id', $id)
                ->where('user_id', $request->user()->id)
                ->first();

            if (!$bioSite) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bio site not found or unauthorized'
                ], 404);
            }

            $updateData = [
                'title' => $request->title,
                'slug' => Str::lower($request->slug),
                'description' => $request->description,
                'template_id' => $request->template_id ?? $bioSite->template_id,
                'status' => $request->status ?? $bioSite->status,
                'updated_at' => now(),
            ];

            if ($request->has('theme_config')) {
                $currentTheme = $bioSite->theme_config ? json_decode($bioSite->theme_config, true) : [];
                $newTheme = array_merge($currentTheme, $request->theme_config);
                $updateData['theme_config'] = json_encode($newTheme);
            }

            $bioSite->update($updateData);

            Log::info("Bio site updated", [
                'user_id' => $request->user()->id,
                'bio_site_id' => $bioSite->id,
                'changes' => array_keys($updateData)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bio site updated successfully',
                'data' => [
                    'id' => $bioSite->id,
                    'title' => $bioSite->title,
                    'slug' => $bioSite->slug,
                    'description' => $bioSite->description,
                    'status' => $bioSite->status,
                    'url' => url("/bio/{$bioSite->slug}"),
                    'updated_at' => $bioSite->updated_at,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update bio site: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update bio site'
            ], 500);
        }
    }

    /**
     * Delete a bio site
     */
    public function destroy($id)
    {
        try {
            $bioSite = BioSite::where('id', $id)
                ->where('user_id', auth()->id())
                ->first();

            if (!$bioSite) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bio site not found or unauthorized'
                ], 404);
            }

            // Delete associated links
            BioSiteLink::where('bio_site_id', $bioSite->id)->delete();
            
            // Delete the bio site
            $bioSite->delete();

            Log::info("Bio site deleted", [
                'user_id' => auth()->id(),
                'bio_site_id' => $id,
                'slug' => $bioSite->slug
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bio site deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to delete bio site: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete bio site'
            ], 500);
        }
    }

    /**
     * Add a link to a bio site
     */
    public function addLink(Request $request, $bioSiteId)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'url' => 'required|url|max:2048',
            'description' => 'nullable|string|max:300',
            'type' => 'nullable|in:link,email,phone,social,custom',
            'icon' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $bioSite = BioSite::where('id', $bioSiteId)
                ->where('user_id', $request->user()->id)
                ->first();

            if (!$bioSite) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bio site not found or unauthorized'
                ], 404);
            }

            // Get the next sort order
            $nextSortOrder = BioSiteLink::where('bio_site_id', $bioSiteId)->max('sort_order') + 1;

            $link = BioSiteLink::create([
                'bio_site_id' => $bioSiteId,
                'title' => $request->title,
                'url' => $request->url,
                'description' => $request->description,
                'type' => $request->type ?? 'link',
                'icon' => $request->icon,
                'sort_order' => $nextSortOrder,
                'is_active' => true,
                'click_count' => 0,
            ]);

            Log::info("Bio site link added", [
                'user_id' => $request->user()->id,
                'bio_site_id' => $bioSiteId,
                'link_id' => $link->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Link added successfully',
                'data' => [
                    'id' => $link->id,
                    'title' => $link->title,
                    'url' => $link->url,
                    'description' => $link->description,
                    'type' => $link->type,
                    'icon' => $link->icon,
                    'sort_order' => $link->sort_order,
                    'is_active' => $link->is_active,
                    'created_at' => $link->created_at,
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Failed to add bio site link: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to add link'
            ], 500);
        }
    }

    /**
     * Update link order
     */
    public function updateLinkOrder(Request $request, $bioSiteId)
    {
        $validator = Validator::make($request->all(), [
            'links' => 'required|array',
            'links.*.id' => 'required|integer',
            'links.*.sort_order' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $bioSite = BioSite::where('id', $bioSiteId)
                ->where('user_id', $request->user()->id)
                ->first();

            if (!$bioSite) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bio site not found or unauthorized'
                ], 404);
            }

            foreach ($request->links as $linkData) {
                BioSiteLink::where('id', $linkData['id'])
                    ->where('bio_site_id', $bioSiteId)
                    ->update(['sort_order' => $linkData['sort_order']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Link order updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update link order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update link order'
            ], 500);
        }
    }

    /**
     * Get analytics for a specific bio site
     */
    public function getAnalytics(Request $request, $id)
    {
        $bioSite = BioSite::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Get date range from request or default to last 30 days
        $startDate = $request->input('start_date', now()->subDays(30));
        $endDate = $request->input('end_date', now());

        // In a real application, you would have a separate analytics table
        // For now, we'll return mock data with proper structure
        $analytics = [
            'overview' => [
                'total_views' => rand(100, 10000),
                'unique_visitors' => rand(50, 5000),
                'link_clicks' => rand(20, 2000),
                'engagement_rate' => rand(5, 25) . '%',
                'avg_session_duration' => rand(30, 300) . 's',
                'bounce_rate' => rand(20, 80) . '%'
            ],
            'traffic_sources' => [
                'direct' => rand(20, 50),
                'social_media' => rand(15, 40),
                'search' => rand(10, 30),
                'referral' => rand(5, 20),
                'email' => rand(5, 15)
            ],
            'top_countries' => [
                ['country' => 'United States', 'visits' => rand(100, 1000)],
                ['country' => 'United Kingdom', 'visits' => rand(50, 500)],
                ['country' => 'Canada', 'visits' => rand(30, 300)],
                ['country' => 'Australia', 'visits' => rand(20, 200)],
                ['country' => 'Germany', 'visits' => rand(15, 150)]
            ],
            'device_breakdown' => [
                'mobile' => rand(50, 80),
                'desktop' => rand(15, 40),
                'tablet' => rand(5, 15)
            ],
            'daily_views' => $this->generateDailyViews($startDate, $endDate),
            'link_performance' => $this->getLinkPerformance($bioSite),
            'social_media_clicks' => $this->getSocialMediaClicks($bioSite)
        ];

        return response()->json([
            'success' => true,
            'data' => $analytics,
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
        ]);
    }

    /**
     * Generate QR code for bio site
     */
    private function generateQRCode($slug)
    {
        // In a real implementation, you would use a QR code library like SimpleSoftwareIO/simple-qrcode
        // For now, we'll return a placeholder URL
        $bioUrl = url('/bio/' . $slug);
        return "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($bioUrl);
    }

    /**
     * Generate daily views data for analytics
     */
    private function generateDailyViews($startDate, $endDate)
    {
        $views = [];
        $current = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        while ($current <= $end) {
            $views[] = [
                'date' => $current->format('Y-m-d'),
                'views' => rand(10, 200),
                'unique_visitors' => rand(5, 100)
            ];
            $current->addDay();
        }

        return $views;
    }

    /**
     * Get link performance data
     */
    private function getLinkPerformance($bioSite)
    {
        $links = [];
        
        // Get bio site links
        $bioSiteLinks = BioSiteLink::where('bio_site_id', $bioSite->id)->get();
        
        foreach ($bioSiteLinks as $link) {
            $links[] = [
                'title' => $link->title,
                'url' => $link->url,
                'clicks' => rand(10, 500),
                'click_rate' => rand(1, 15) . '%',
                'position' => $link->position
            ];
        }

        // Add social media links
        foreach ($bioSite->social_links as $socialLink) {
            $links[] = [
                'title' => ucfirst($socialLink['platform']),
                'url' => $socialLink['url'],
                'clicks' => rand(5, 200),
                'click_rate' => rand(1, 10) . '%',
                'position' => null
            ];
        }

        return $links;
    }

    /**
     * Get social media clicks data
     */
    private function getSocialMediaClicks($bioSite)
    {
        $socialClicks = [];
        
        foreach ($bioSite->social_links as $socialLink) {
            $socialClicks[] = [
                'platform' => $socialLink['platform'],
                'clicks' => rand(5, 200),
                'percentage' => rand(5, 30) . '%'
            ];
        }

        return $socialClicks;
    }

    /**
     * Get themes available for bio sites
     */
    public function getThemes()
    {
        $themes = [
            'minimal' => [
                'name' => 'Minimal',
                'description' => 'Clean and simple design with focus on content',
                'preview_url' => '/themes/minimal.jpg',
                'features' => ['Clean layout', 'Typography focused', 'Fast loading']
            ],
            'modern' => [
                'name' => 'Modern',
                'description' => 'Contemporary design with smooth animations',
                'preview_url' => '/themes/modern.jpg',
                'features' => ['Smooth animations', 'Modern UI', 'Responsive']
            ],
            'gradient' => [
                'name' => 'Gradient',
                'description' => 'Vibrant gradient backgrounds with modern styling',
                'preview_url' => '/themes/gradient.jpg',
                'features' => ['Gradient backgrounds', 'Modern styling', 'Eye-catching']
            ],
            'neon' => [
                'name' => 'Neon',
                'description' => 'Dark theme with neon accents and glow effects',
                'preview_url' => '/themes/neon.jpg',
                'features' => ['Dark theme', 'Neon effects', 'Glow animations']
            ],
            'elegant' => [
                'name' => 'Elegant',
                'description' => 'Sophisticated design with refined typography',
                'preview_url' => '/themes/elegant.jpg',
                'features' => ['Sophisticated', 'Refined typography', 'Professional']
            ],
            'creative' => [
                'name' => 'Creative',
                'description' => 'Artistic layout with creative elements',
                'preview_url' => '/themes/creative.jpg',
                'features' => ['Artistic layout', 'Creative elements', 'Unique design']
            ],
            'professional' => [
                'name' => 'Professional',
                'description' => 'Business-oriented design for professionals',
                'preview_url' => '/themes/professional.jpg',
                'features' => ['Business-oriented', 'Professional look', 'Clean structure']
            ],
            'dark' => [
                'name' => 'Dark',
                'description' => 'Dark theme with high contrast',
                'preview_url' => '/themes/dark.jpg',
                'features' => ['Dark theme', 'High contrast', 'Eye-friendly']
            ],
            'light' => [
                'name' => 'Light',
                'description' => 'Bright and airy design with light colors',
                'preview_url' => '/themes/light.jpg',
                'features' => ['Bright design', 'Light colors', 'Airy feel']
            ],
            'colorful' => [
                'name' => 'Colorful',
                'description' => 'Vibrant and playful design with multiple colors',
                'preview_url' => '/themes/colorful.jpg',
                'features' => ['Vibrant colors', 'Playful design', 'Multiple colors']
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $themes
        ]);
    }

    /**
     * Duplicate an existing bio site
     */
    public function duplicate(Request $request, $id)
    {
        $originalSite = BioSite::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:bio_sites,slug|regex:/^[a-zA-Z0-9-_]+$/'
        ]);

        try {
            $duplicatedSite = $originalSite->replicate();
            $duplicatedSite->name = $request->name;
            $duplicatedSite->slug = $request->slug;
            $duplicatedSite->is_active = false; // Start as inactive
            $duplicatedSite->custom_domain = null; // Clear custom domain
            $duplicatedSite->password = null; // Clear password
            $duplicatedSite->password_protection = false; // Disable password protection
            $duplicatedSite->save();

            // Duplicate bio site links
            $originalLinks = BioSiteLink::where('bio_site_id', $originalSite->id)->get();
            foreach ($originalLinks as $link) {
                $duplicatedLink = $link->replicate();
                $duplicatedLink->bio_site_id = $duplicatedSite->id;
                $duplicatedLink->save();
            }

            // Generate QR code for the duplicated bio site
            $qrCodeUrl = $this->generateQRCode($duplicatedSite->slug);
            $duplicatedSite->update(['qr_code_url' => $qrCodeUrl]);

            return response()->json([
                'success' => true,
                'message' => 'Bio site duplicated successfully',
                'data' => [
                    'id' => $duplicatedSite->id,
                    'name' => $duplicatedSite->name,
                    'slug' => $duplicatedSite->slug,
                    'url' => url('/bio/' . $duplicatedSite->slug),
                    'is_active' => $duplicatedSite->is_active,
                    'created_at' => $duplicatedSite->created_at
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Bio site duplication failed', ['error' => $e->getMessage(), 'user_id' => auth()->id()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to duplicate bio site: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export bio site data
     */
    public function export(Request $request, $id)
    {
        $bioSite = BioSite::where('id', $id)
            ->where('user_id', auth()->id())
            ->with('links')
            ->firstOrFail();

        $exportData = [
            'bio_site' => [
                'name' => $bioSite->name,
                'slug' => $bioSite->slug,
                'description' => $bioSite->description,
                'theme' => $bioSite->theme,
                'branding' => $bioSite->branding,
                'social_links' => $bioSite->social_links,
                'advanced_features' => $bioSite->advanced_features,
                'custom_css' => $bioSite->custom_css,
                'custom_js' => $bioSite->custom_js,
                'seo_title' => $bioSite->seo_title,
                'seo_description' => $bioSite->seo_description,
                'seo_keywords' => $bioSite->seo_keywords
            ],
            'links' => $bioSite->links->map(function($link) {
                return [
                    'title' => $link->title,
                    'url' => $link->url,
                    'description' => $link->description,
                    'position' => $link->position,
                    'is_active' => $link->is_active
                ];
            }),
            'exported_at' => now()->toISOString()
        ];

        return response()->json([
            'success' => true,
            'data' => $exportData
        ]);
    }

    /**
     * Get links for a bio site
     */
    public function getLinks($bioSiteId)
    {
        try {
            $bioSite = BioSite::where('id', $bioSiteId)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            $links = BioSiteLink::where('bio_site_id', $bioSiteId)
                ->orderBy('sort_order')
                ->get()
                ->map(function ($link) {
                    return [
                        'id' => $link->id,
                        'title' => $link->title,
                        'url' => $link->url,
                        'description' => $link->description,
                        'type' => $link->type,
                        'icon' => $link->icon,
                        'sort_order' => $link->sort_order,
                        'is_active' => $link->is_active,
                        'click_count' => $link->click_count ?? 0,
                        'created_at' => $link->created_at,
                        'updated_at' => $link->updated_at,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $links,
                'message' => 'Bio site links retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve bio site links: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve bio site links'
            ], 500);
        }
    }

    /**
     * Create a new link for a bio site
     */
    public function createLink(Request $request, $bioSiteId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|url|max:2048',
            'description' => 'nullable|string|max:300',
            'type' => 'nullable|in:link,email,phone,social,custom',
            'icon' => 'nullable|string|max:100',
            'is_active' => 'boolean'
        ]);

        try {
            $bioSite = BioSite::where('id', $bioSiteId)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            // Get the next sort order
            $nextSortOrder = BioSiteLink::where('bio_site_id', $bioSiteId)->max('sort_order') + 1;

            $link = BioSiteLink::create([
                'bio_site_id' => $bioSiteId,
                'title' => $request->title,
                'url' => $request->url,
                'description' => $request->description,
                'type' => $request->type ?? 'link',
                'icon' => $request->icon,
                'sort_order' => $nextSortOrder,
                'is_active' => $request->is_active ?? true,
                'click_count' => 0,
            ]);

            Log::info("Bio site link created", [
                'user_id' => auth()->id(),
                'bio_site_id' => $bioSiteId,
                'link_id' => $link->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Link created successfully',
                'data' => [
                    'id' => $link->id,
                    'title' => $link->title,
                    'url' => $link->url,
                    'description' => $link->description,
                    'type' => $link->type,
                    'icon' => $link->icon,
                    'sort_order' => $link->sort_order,
                    'is_active' => $link->is_active,
                    'click_count' => $link->click_count,
                    'created_at' => $link->created_at,
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Failed to create bio site link: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create link'
            ], 500);
        }
    }

    /**
     * Update a bio site link
     */
    public function updateLink(Request $request, $bioSiteId, $linkId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|url|max:2048',
            'description' => 'nullable|string|max:300',
            'type' => 'nullable|in:link,email,phone,social,custom',
            'icon' => 'nullable|string|max:100',
            'is_active' => 'boolean'
        ]);

        try {
            $bioSite = BioSite::where('id', $bioSiteId)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            $link = BioSiteLink::where('id', $linkId)
                ->where('bio_site_id', $bioSiteId)
                ->firstOrFail();

            $link->update([
                'title' => $request->title,
                'url' => $request->url,
                'description' => $request->description,
                'type' => $request->type ?? $link->type,
                'icon' => $request->icon,
                'is_active' => $request->is_active ?? $link->is_active,
            ]);

            Log::info("Bio site link updated", [
                'user_id' => auth()->id(),
                'bio_site_id' => $bioSiteId,
                'link_id' => $linkId
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Link updated successfully',
                'data' => [
                    'id' => $link->id,
                    'title' => $link->title,
                    'url' => $link->url,
                    'description' => $link->description,
                    'type' => $link->type,
                    'icon' => $link->icon,
                    'sort_order' => $link->sort_order,
                    'is_active' => $link->is_active,
                    'click_count' => $link->click_count,
                    'updated_at' => $link->updated_at,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update bio site link: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update link'
            ], 500);
        }
    }

    /**
     * Delete a bio site link
     */
    public function deleteLink($bioSiteId, $linkId)
    {
        try {
            $bioSite = BioSite::where('id', $bioSiteId)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            $link = BioSiteLink::where('id', $linkId)
                ->where('bio_site_id', $bioSiteId)
                ->firstOrFail();

            $link->delete();

            Log::info("Bio site link deleted", [
                'user_id' => auth()->id(),
                'bio_site_id' => $bioSiteId,
                'link_id' => $linkId
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Link deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to delete bio site link: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete link'
            ], 500);
        }
    }
}