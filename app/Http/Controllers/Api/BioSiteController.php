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
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:bio_sites|regex:/^[a-zA-Z0-9\-_]+$/',
            'description' => 'nullable|string|max:500',
            'template_id' => 'nullable|integer',
            'theme_config' => 'nullable|array',
            'theme_config.primary_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'theme_config.background_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'theme_config.text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'theme_config.font_family' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Create default theme config if not provided
            $defaultTheme = [
                'primary_color' => '#FDFDFD',
                'background_color' => '#101010',
                'text_color' => '#F1F1F1',
                'button_style' => 'rounded',
                'font_family' => 'Inter',
                'layout' => 'center',
            ];

            $themeConfig = array_merge($defaultTheme, $request->theme_config ?? []);

            $bioSite = BioSite::create([
                'user_id' => $request->user()->id,
                'title' => $request->title,
                'slug' => Str::lower($request->slug),
                'description' => $request->description,
                'template_id' => $request->template_id ?? 1,
                'theme_config' => json_encode($themeConfig),
                'status' => 'draft',
                'view_count' => 0,
                'click_count' => 0,
            ]);

            Log::info("Bio site created", [
                'user_id' => $request->user()->id,
                'bio_site_id' => $bioSite->id,
                'slug' => $bioSite->slug
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bio site created successfully',
                'data' => [
                    'id' => $bioSite->id,
                    'title' => $bioSite->title,
                    'slug' => $bioSite->slug,
                    'description' => $bioSite->description,
                    'status' => $bioSite->status,
                    'url' => url("/bio/{$bioSite->slug}"),
                    'theme_config' => json_decode($bioSite->theme_config, true),
                    'created_at' => $bioSite->created_at,
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Failed to create bio site: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create bio site. Please try again.'
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