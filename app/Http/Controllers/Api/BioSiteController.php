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
     * Get analytics for a bio site
     */
    public function getAnalytics(Request $request, $bioSiteId)
    {
        try {
            $bioSite = BioSite::with('links')
                ->where('id', $bioSiteId)
                ->where('user_id', $request->user()->id)
                ->first();

            if (!$bioSite) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bio site not found or unauthorized'
                ], 404);
            }

            $analytics = [
                'total_views' => $bioSite->view_count ?? 0,
                'total_clicks' => $bioSite->click_count ?? 0,
                'click_through_rate' => $bioSite->view_count > 0 ? round(($bioSite->click_count / $bioSite->view_count) * 100, 2) : 0,
                'total_links' => $bioSite->links->count(),
                'active_links' => $bioSite->links->where('is_active', true)->count(),
                'top_links' => $bioSite->links->sortByDesc('click_count')->take(5)->map(function ($link) {
                    return [
                        'id' => $link->id,
                        'title' => $link->title,
                        'url' => $link->url,
                        'clicks' => $link->click_count ?? 0,
                    ];
                })->values(),
                'recent_activity' => [], // TODO: Implement activity tracking
            ];

            return response()->json([
                'success' => true,
                'data' => $analytics,
                'message' => 'Analytics retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve bio site analytics: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve analytics'
            ], 500);
        }
    }
}