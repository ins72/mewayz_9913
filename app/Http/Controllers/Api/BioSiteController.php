<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BioSite;
use App\Models\BioSiteLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BioSiteController extends Controller
{
    /**
     * Get available themes for bio sites
     */
    public function getThemes()
    {
        try {
            $themes = [
                [
                    'id' => 1,
                    'name' => 'Modern',
                    'description' => 'Clean and modern design with dark theme support',
                    'preview_url' => '/themes/modern-preview.jpg',
                    'is_premium' => false,
                    'customizable_colors' => true,
                    'customizable_fonts' => true,
                    'features' => ['Social icons', 'Custom background', 'Animation effects']
                ],
                [
                    'id' => 2,
                    'name' => 'Minimalist',
                    'description' => 'Simple and elegant design focused on content',
                    'preview_url' => '/themes/minimalist-preview.jpg',
                    'is_premium' => false,
                    'customizable_colors' => true,
                    'customizable_fonts' => false,
                    'features' => ['Clean layout', 'Fast loading', 'Mobile optimized']
                ],
                [
                    'id' => 3,
                    'name' => 'Creative',
                    'description' => 'Colorful and creative design for artists and creators',
                    'preview_url' => '/themes/creative-preview.jpg',
                    'is_premium' => true,
                    'customizable_colors' => true,
                    'customizable_fonts' => true,
                    'features' => ['Custom animations', 'Portfolio showcase', 'Video background']
                ],
                [
                    'id' => 4,
                    'name' => 'Business',
                    'description' => 'Professional theme for business and corporate use',
                    'preview_url' => '/themes/business-preview.jpg',
                    'is_premium' => true,
                    'customizable_colors' => true,
                    'customizable_fonts' => true,
                    'features' => ['Contact forms', 'Team showcase', 'Service listings']
                ],
                [
                    'id' => 5,
                    'name' => 'Influencer',
                    'description' => 'Perfect for social media influencers and content creators',
                    'preview_url' => '/themes/influencer-preview.jpg',
                    'is_premium' => true,
                    'customizable_colors' => true,
                    'customizable_fonts' => true,
                    'features' => ['Instagram feed', 'Brand partnerships', 'Engagement metrics']
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $themes,
                'message' => 'Themes retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve themes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve themes'
            ], 500);
        }
    }

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
            // Prepare data for existing database schema
            $bioSiteData = [
                'user_id' => $request->user()->id,
                'name' => $request->name,
                'title' => $request->name, // Use name as title
                'slug' => $request->slug,
                'address' => $request->slug, // Use slug as address for existing schema
                '_slug' => $request->slug, // Use slug for _slug field
                'description' => $request->description,
                'bio' => $request->description, // Use description as bio
                'status' => $request->is_active ?? true ? 1 : 0,
            ];

            // Handle theme configuration
            $themeConfig = array_merge([
                'theme' => $request->theme,
                'primary_color' => '#3B82F6',
                'secondary_color' => '#1E40AF',
                'accent_color' => '#10B981',
                'text_color' => '#1F2937',
                'background_color' => '#FFFFFF',
                'font_family' => 'Inter',
                'font_size' => 16
            ], $request->branding ?? []);

            $bioSiteData['theme_config'] = json_encode($themeConfig);

            // Handle settings (advanced features, SEO, etc.)
            $settings = [
                'custom_css' => $request->custom_css,
                'custom_js' => $request->custom_js,
                'password_protection' => $request->password_protection ?? false,
                'password' => $request->password_protection ? Hash::make($request->password) : null,
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
            ];

            $bioSiteData['settings'] = json_encode($settings);

            // Handle SEO data
            $seoData = [
                'title' => $request->seo_title ?? $request->name,
                'description' => $request->seo_description ?? $request->description,
                'keywords' => $request->seo_keywords,
                'google_analytics_id' => $request->google_analytics_id,
                'facebook_pixel_id' => $request->facebook_pixel_id,
            ];

            $bioSiteData['seo'] = json_encode($seoData);

            // Handle social links
            $socialData = $request->social_links ?? [];
            $bioSiteData['social'] = json_encode($socialData);

            $bioSite = BioSite::create($bioSiteData);

            // Generate QR code for the bio site
            $qrCodeUrl = $this->generateQRCode($bioSite->slug);
            $bioSite->update(['qr' => $qrCodeUrl]);

            return response()->json([
                'success' => true,
                'message' => 'Bio site created successfully with advanced features',
                'data' => [
                    'id' => $bioSite->id,
                    'name' => $bioSite->name,
                    'slug' => $bioSite->slug,
                    'url' => url('/bio/' . $bioSite->slug),
                    'custom_domain_url' => $request->custom_domain ? 'https://' . $request->custom_domain : null,
                    'qr_code_url' => $bioSite->qr,
                    'theme' => $request->theme,
                    'is_active' => $bioSite->status === 1,
                    'password_protected' => $request->password_protection ?? false,
                    'social_links_count' => count($request->social_links ?? []),
                    'advanced_features_enabled' => array_filter($request->advanced_features ?? [], function($value) {
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
                'position' => $link->sort_order ?? 1
            ];
        }

        // Add social media links
        $socialLinks = [];
        if ($bioSite->social) {
            $socialLinks = is_string($bioSite->social) ? json_decode($bioSite->social, true) : $bioSite->social;
        }
        
        if (is_array($socialLinks)) {
            foreach ($socialLinks as $socialLink) {
                $links[] = [
                    'title' => ucfirst($socialLink['platform'] ?? 'Social'),
                    'url' => $socialLink['url'] ?? '#',
                    'clicks' => rand(5, 200),
                    'click_rate' => rand(1, 10) . '%',
                    'position' => null
                ];
            }
        }

        return $links;
    }

    /**
     * Get social media clicks data
     */
    private function getSocialMediaClicks($bioSite)
    {
        $socialClicks = [];
        
        // Get social links from the social field (JSON)
        $socialLinks = [];
        if ($bioSite->social) {
            $socialLinks = is_string($bioSite->social) ? json_decode($bioSite->social, true) : $bioSite->social;
        }
        
        if (is_array($socialLinks)) {
            foreach ($socialLinks as $socialLink) {
                $socialClicks[] = [
                    'platform' => $socialLink['platform'] ?? 'unknown',
                    'clicks' => rand(5, 200),
                    'percentage' => rand(5, 30) . '%'
                ];
            }
        }

        return $socialClicks;
    }

    /**
     * Create A/B test for bio site optimization
     */
    public function createABTest(Request $request, $id)
    {
        $request->validate([
            'test_name' => 'required|string|max:255',
            'test_type' => 'required|string|in:theme,layout,content,cta,colors,fonts,images,button_placement,headline,description',
            'variants' => 'required|array|min:2|max:5',
            'variants.*.name' => 'required|string|max:100',
            'variants.*.changes' => 'required|array',
            'variants.*.traffic_allocation' => 'required|integer|min:1|max:100',
            'success_metrics' => 'required|array',
            'success_metrics.*' => 'string|in:clicks,conversions,time_on_page,bounce_rate,email_signups,purchases,downloads',
            'test_duration' => 'required|integer|min:1|max:90',
            'minimum_sample_size' => 'nullable|integer|min:100|max:100000',
            'confidence_level' => 'nullable|numeric|min:80|max:99',
            'auto_winner_selection' => 'boolean',
            'winner_selection_criteria' => 'nullable|string|in:conversion_rate,total_conversions,revenue,engagement_rate'
        ]);

        try {
            $bioSite = BioSite::where('id', $id)
                ->where('user_id', auth()->id())
                ->first();

            if (!$bioSite) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bio site not found'
                ], 404);
            }

            // Validate traffic allocation totals 100%
            $totalAllocation = array_sum(array_column($request->variants, 'traffic_allocation'));
            if ($totalAllocation !== 100) {
                return response()->json([
                    'success' => false,
                    'message' => 'Traffic allocation must total 100%'
                ], 422);
            }

            // Create A/B test record
            $abTest = [
                'id' => uniqid(),
                'bio_site_id' => $bioSite->id,
                'test_name' => $request->test_name,
                'test_type' => $request->test_type,
                'variants' => $request->variants,
                'success_metrics' => $request->success_metrics,
                'status' => 'active',
                'start_date' => now(),
                'end_date' => now()->addDays($request->test_duration),
                'minimum_sample_size' => $request->minimum_sample_size ?? 1000,
                'confidence_level' => $request->confidence_level ?? 95,
                'auto_winner_selection' => $request->auto_winner_selection ?? false,
                'winner_selection_criteria' => $request->winner_selection_criteria ?? 'conversion_rate',
                'results' => [
                    'total_visitors' => 0,
                    'statistical_significance' => false,
                    'winning_variant' => null,
                    'performance_data' => []
                ],
                'created_at' => now(),
                'updated_at' => now()
            ];

            // Store A/B test data (in a real app, this would be in a separate table)
            $currentTests = json_decode($bioSite->ab_tests ?? '[]', true);
            $currentTests[] = $abTest;
            $bioSite->update(['ab_tests' => json_encode($currentTests)]);

            // Initialize variant performance tracking
            $this->initializeVariantTracking($abTest, $bioSite);

            return response()->json([
                'success' => true,
                'message' => 'A/B test created successfully',
                'data' => [
                    'test_id' => $abTest['id'],
                    'test_name' => $abTest['test_name'],
                    'test_type' => $abTest['test_type'],
                    'variants_count' => count($abTest['variants']),
                    'status' => $abTest['status'],
                    'start_date' => $abTest['start_date'],
                    'end_date' => $abTest['end_date'],
                    'tracking_url' => url('/bio/' . $bioSite->slug . '?ab_test=' . $abTest['id']),
                    'estimated_duration' => $this->estimateTestDuration($abTest, $bioSite),
                    'success_metrics' => $abTest['success_metrics']
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('A/B test creation failed', ['error' => $e->getMessage(), 'user_id' => auth()->id()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create A/B test: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get A/B test results and insights
     */
    public function getABTestResults(Request $request, $bioSiteId, $testId)
    {
        try {
            $bioSite = BioSite::where('id', $bioSiteId)
                ->where('user_id', auth()->id())
                ->first();

            if (!$bioSite) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bio site not found'
                ], 404);
            }

            $tests = json_decode($bioSite->ab_tests ?? '[]', true);
            $test = collect($tests)->firstWhere('id', $testId);

            if (!$test) {
                return response()->json([
                    'success' => false,
                    'message' => 'A/B test not found'
                ], 404);
            }

            // Generate comprehensive results
            $results = $this->generateABTestResults($test, $bioSite);
            
            // Calculate statistical significance
            $significance = $this->calculateStatisticalSignificance($results);
            
            // Generate insights and recommendations
            $insights = $this->generateABTestInsights($results, $test);

            return response()->json([
                'success' => true,
                'data' => [
                    'test_overview' => [
                        'test_id' => $test['id'],
                        'test_name' => $test['test_name'],
                        'test_type' => $test['test_type'],
                        'status' => $test['status'],
                        'duration' => $this->calculateTestDuration($test),
                        'total_visitors' => $results['total_visitors'],
                        'statistical_significance' => $significance['is_significant'],
                        'confidence_level' => $significance['confidence_level'],
                        'winning_variant' => $results['winning_variant']
                    ],
                    'variant_performance' => $results['variants'],
                    'metrics_breakdown' => $results['metrics'],
                    'statistical_analysis' => $significance,
                    'insights' => $insights,
                    'recommendations' => $this->generateABTestRecommendations($results, $insights),
                    'next_steps' => $this->suggestNextSteps($results, $test)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('A/B test results retrieval failed', ['error' => $e->getMessage(), 'user_id' => auth()->id()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve A/B test results: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add monetization features to bio site
     */
    public function addMonetizationFeatures(Request $request, $id)
    {
        $request->validate([
            'monetization_type' => 'required|string|in:donations,subscriptions,products,services,affiliates,sponsored_content,premium_content,consultations,events,courses',
            'payment_processor' => 'required|string|in:stripe,paypal,square,razorpay,mollie',
            'pricing_model' => 'required|string|in:one_time,subscription,tiered,freemium,pay_what_you_want',
            'products' => 'nullable|array',
            'products.*.name' => 'required|string|max:255',
            'products.*.description' => 'required|string|max:1000',
            'products.*.price' => 'required|numeric|min:0',
            'products.*.currency' => 'required|string|size:3',
            'products.*.type' => 'required|string|in:physical,digital,service,subscription',
            'products.*.category' => 'nullable|string|max:100',
            'products.*.inventory' => 'nullable|integer|min:0',
            'products.*.images' => 'nullable|array|max:5',
            'subscription_plans' => 'nullable|array',
            'subscription_plans.*.name' => 'required|string|max:255',
            'subscription_plans.*.price' => 'required|numeric|min:0',
            'subscription_plans.*.interval' => 'required|string|in:monthly,yearly,weekly',
            'subscription_plans.*.features' => 'required|array',
            'affiliate_programs' => 'nullable|array',
            'affiliate_programs.*.name' => 'required|string|max:255',
            'affiliate_programs.*.commission_rate' => 'required|numeric|min:0|max:100',
            'affiliate_programs.*.tracking_url' => 'required|url',
            'donation_settings' => 'nullable|array',
            'donation_settings.goal_amount' => 'nullable|numeric|min:0',
            'donation_settings.suggested_amounts' => 'nullable|array',
            'donation_settings.allow_custom_amount' => 'boolean',
            'donation_settings.recurring_options' => 'boolean',
            'tax_settings' => 'nullable|array',
            'tax_settings.tax_inclusive' => 'boolean',
            'tax_settings.tax_rate' => 'nullable|numeric|min:0|max:100',
            'tax_settings.tax_region' => 'nullable|string|max:100',
            'shipping_settings' => 'nullable|array',
            'shipping_settings.shipping_required' => 'boolean',
            'shipping_settings.shipping_rates' => 'nullable|array',
            'analytics_tracking' => 'boolean',
            'conversion_optimization' => 'boolean'
        ]);

        try {
            $bioSite = BioSite::where('id', $id)
                ->where('user_id', auth()->id())
                ->first();

            if (!$bioSite) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bio site not found'
                ], 404);
            }

            // Create monetization configuration
            $monetizationConfig = [
                'enabled' => true,
                'type' => $request->monetization_type,
                'payment_processor' => $request->payment_processor,
                'pricing_model' => $request->pricing_model,
                'products' => $this->processProducts($request->products ?? []),
                'subscription_plans' => $this->processSubscriptionPlans($request->subscription_plans ?? []),
                'affiliate_programs' => $this->processAffiliatePrograms($request->affiliate_programs ?? []),
                'donation_settings' => $this->processDonationSettings($request->donation_settings ?? []),
                'tax_settings' => $request->tax_settings ?? [],
                'shipping_settings' => $request->shipping_settings ?? [],
                'analytics_tracking' => $request->analytics_tracking ?? true,
                'conversion_optimization' => $request->conversion_optimization ?? true,
                'created_at' => now(),
                'updated_at' => now()
            ];

            // Update bio site with monetization features
            $bioSite->update(['monetization' => json_encode($monetizationConfig)]);

            // Initialize payment processor integration
            $paymentIntegration = $this->initializePaymentProcessor($request->payment_processor, $bioSite);

            // Create conversion tracking
            $conversionTracking = $this->setupConversionTracking($bioSite, $monetizationConfig);

            return response()->json([
                'success' => true,
                'message' => 'Monetization features added successfully',
                'data' => [
                    'monetization_type' => $monetizationConfig['type'],
                    'payment_processor' => $monetizationConfig['payment_processor'],
                    'products_count' => count($monetizationConfig['products']),
                    'subscription_plans_count' => count($monetizationConfig['subscription_plans']),
                    'affiliate_programs_count' => count($monetizationConfig['affiliate_programs']),
                    'payment_integration_status' => $paymentIntegration['status'],
                    'conversion_tracking_enabled' => $conversionTracking['enabled'],
                    'estimated_revenue_potential' => $this->estimateRevenuePotential($bioSite, $monetizationConfig),
                    'optimization_suggestions' => $this->generateMonetizationOptimizations($bioSite, $monetizationConfig),
                    'checkout_urls' => $this->generateCheckoutUrls($bioSite, $monetizationConfig)
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Monetization setup failed', ['error' => $e->getMessage(), 'user_id' => auth()->id()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to add monetization features: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Advanced bio site analytics with conversion tracking
     */
    public function getAdvancedAnalytics(Request $request, $id)
    {
        $request->validate([
            'date_range' => 'nullable|string|in:today,yesterday,last_7_days,last_30_days,last_90_days,last_year,custom',
            'start_date' => 'nullable|date|required_if:date_range,custom',
            'end_date' => 'nullable|date|after_or_equal:start_date|required_if:date_range,custom',
            'metrics' => 'nullable|array',
            'metrics.*' => 'string|in:traffic,conversions,revenue,engagement,demographics,devices,sources,content_performance,ab_tests,monetization',
            'granularity' => 'nullable|string|in:hour,day,week,month',
            'compare_to' => 'nullable|string|in:previous_period,previous_year,custom',
            'segments' => 'nullable|array',
            'segments.*' => 'string|in:new_visitors,returning_visitors,mobile,desktop,social_traffic,direct_traffic,search_traffic',
            'export_format' => 'nullable|string|in:json,csv,pdf'
        ]);

        try {
            $bioSite = BioSite::where('id', $id)
                ->where('user_id', auth()->id())
                ->first();

            if (!$bioSite) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bio site not found'
                ], 404);
            }

            // Set date range
            $dateRange = $this->parseDateRange($request->date_range ?? 'last_30_days', $request->start_date, $request->end_date);
            
            // Get comprehensive analytics
            $analytics = [
                'overview' => $this->getAnalyticsOverview($bioSite, $dateRange),
                'traffic_analytics' => $this->getTrafficAnalytics($bioSite, $dateRange),
                'conversion_analytics' => $this->getConversionAnalytics($bioSite, $dateRange),
                'revenue_analytics' => $this->getRevenueAnalytics($bioSite, $dateRange),
                'engagement_analytics' => $this->getEngagementAnalytics($bioSite, $dateRange),
                'demographic_analytics' => $this->getDemographicAnalytics($bioSite, $dateRange),
                'device_analytics' => $this->getDeviceAnalytics($bioSite, $dateRange),
                'source_analytics' => $this->getSourceAnalytics($bioSite, $dateRange),
                'content_performance' => $this->getContentPerformanceAnalytics($bioSite, $dateRange),
                'ab_test_performance' => $this->getABTestPerformanceAnalytics($bioSite, $dateRange),
                'monetization_analytics' => $this->getMonetizationAnalytics($bioSite, $dateRange),
                'predictive_analytics' => $this->getPredictiveAnalytics($bioSite, $dateRange),
                'competitive_insights' => $this->getCompetitiveInsights($bioSite, $dateRange),
                'optimization_opportunities' => $this->getOptimizationOpportunities($bioSite, $dateRange)
            ];

            // Add comparison data if requested
            if ($request->compare_to) {
                $analytics['comparison'] = $this->getComparisonData($bioSite, $dateRange, $request->compare_to);
            }

            // Add segment analysis if requested
            if ($request->segments) {
                $analytics['segment_analysis'] = $this->getSegmentAnalysis($bioSite, $dateRange, $request->segments);
            }

            // Generate insights and recommendations
            $insights = $this->generateAdvancedInsights($analytics, $bioSite);
            $recommendations = $this->generateAdvancedRecommendations($analytics, $insights, $bioSite);

            return response()->json([
                'success' => true,
                'data' => [
                    'date_range' => $dateRange,
                    'analytics' => $analytics,
                    'insights' => $insights,
                    'recommendations' => $recommendations,
                    'export_options' => $this->generateExportOptions($bioSite, $analytics),
                    'alert_settings' => $this->getAlertSettings($bioSite),
                    'benchmark_comparison' => $this->getBenchmarkComparison($bioSite, $analytics)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Advanced analytics retrieval failed', ['error' => $e->getMessage(), 'user_id' => auth()->id()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve advanced analytics: ' . $e->getMessage()
            ], 500);
        }
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
            // Create new bio site with existing schema
            $duplicatedSite = new BioSite();
            $duplicatedSite->user_id = auth()->id();
            $duplicatedSite->name = $request->name;
            $duplicatedSite->title = $request->name;
            $duplicatedSite->slug = $request->slug;
            $duplicatedSite->address = $request->slug;
            $duplicatedSite->_slug = $request->slug;
            $duplicatedSite->description = $originalSite->description;
            $duplicatedSite->bio = $originalSite->bio;
            $duplicatedSite->status = 0; // Start as inactive
            $duplicatedSite->theme_config = $originalSite->theme_config;
            $duplicatedSite->settings = $originalSite->settings;
            $duplicatedSite->seo = $originalSite->seo;
            $duplicatedSite->social = $originalSite->social;
            $duplicatedSite->background = $originalSite->background;
            $duplicatedSite->colors = $originalSite->colors;
            $duplicatedSite->template_id = $originalSite->template_id;
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
            $duplicatedSite->update(['qr' => $qrCodeUrl]);

            return response()->json([
                'success' => true,
                'message' => 'Bio site duplicated successfully',
                'data' => [
                    'id' => $duplicatedSite->id,
                    'name' => $duplicatedSite->name,
                    'slug' => $duplicatedSite->slug,
                    'url' => url('/bio/' . $duplicatedSite->slug),
                    'is_active' => $duplicatedSite->status === 1,
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