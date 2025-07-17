<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShortenedLink;
use App\Models\LinkClick;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class LinkShortenerController extends Controller
{
    /**
     * Create a new shortened link
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'original_url' => 'required|url|max:2048',
            'custom_slug' => 'nullable|string|max:50|regex:/^[a-zA-Z0-9_-]+$/',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'workspace_id' => 'required|exists:workspaces,id',
            'expires_at' => 'nullable|date|after:now',
            'password' => 'nullable|string|max:255',
            'is_public' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $user = $request->user();
            
            // Generate unique slug
            $slug = $request->custom_slug ?: $this->generateUniqueSlug();
            
            // Check if custom slug is already taken
            if (ShortenedLink::where('slug', $slug)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This custom slug is already taken'
                ], 400);
            }

            $shortenedLink = ShortenedLink::create([
                'user_id' => $user->id,
                'workspace_id' => $request->workspace_id,
                'original_url' => $request->original_url,
                'slug' => $slug,
                'title' => $request->title,
                'description' => $request->description,
                'expires_at' => $request->expires_at,
                'password' => $request->password ? bcrypt($request->password) : null,
                'is_public' => $request->is_public ?? true,
                'utm_source' => $request->utm_source,
                'utm_medium' => $request->utm_medium,
                'utm_campaign' => $request->utm_campaign,
                'utm_content' => $request->utm_content,
                'utm_term' => $request->utm_term,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Link shortened successfully',
                'data' => [
                    'id' => $shortenedLink->id,
                    'original_url' => $shortenedLink->original_url,
                    'short_url' => $shortenedLink->short_url,
                    'slug' => $shortenedLink->slug,
                    'title' => $shortenedLink->title,
                    'description' => $shortenedLink->description,
                    'clicks' => 0,
                    'expires_at' => $shortenedLink->expires_at,
                    'created_at' => $shortenedLink->created_at,
                ]
            ], 201);
        } catch (\Exception $e) {
            Log::error('Link shortening failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to shorten link'
            ], 500);
        }
    }

    /**
     * Get all shortened links for the user
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $workspaceId = $request->workspace_id;

            $query = ShortenedLink::where('user_id', $user->id)
                ->with('clicks')
                ->withCount('clicks');

            if ($workspaceId) {
                $query->where('workspace_id', $workspaceId);
            }

            $links = $query->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $links,
                'message' => 'Links retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve links: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve links'
            ], 500);
        }
    }

    /**
     * Get detailed analytics for a specific link
     */
    public function analytics(Request $request, $id)
    {
        try {
            $user = $request->user();
            
            $link = ShortenedLink::where('id', $id)
                ->where('user_id', $user->id)
                ->withCount('clicks')
                ->first();

            if (!$link) {
                return response()->json([
                    'success' => false,
                    'message' => 'Link not found'
                ], 404);
            }

            // Get click analytics
            $clicks = LinkClick::where('shortened_link_id', $id)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->limit(30)
                ->get();

            // Get referrer analytics
            $referrers = LinkClick::where('shortened_link_id', $id)
                ->selectRaw('referrer, COUNT(*) as count')
                ->groupBy('referrer')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get();

            // Get location analytics
            $locations = LinkClick::where('shortened_link_id', $id)
                ->selectRaw('country, COUNT(*) as count')
                ->groupBy('country')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get();

            // Get device analytics
            $devices = LinkClick::where('shortened_link_id', $id)
                ->selectRaw('device_type, COUNT(*) as count')
                ->groupBy('device_type')
                ->orderBy('count', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'link' => $link,
                    'total_clicks' => $link->clicks_count,
                    'clicks_over_time' => $clicks,
                    'top_referrers' => $referrers,
                    'top_locations' => $locations,
                    'device_breakdown' => $devices,
                ],
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

    /**
     * Update a shortened link
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'expires_at' => 'nullable|date|after:now',
            'password' => 'nullable|string|max:255',
            'is_public' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $user = $request->user();
            
            $link = ShortenedLink::where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$link) {
                return response()->json([
                    'success' => false,
                    'message' => 'Link not found'
                ], 404);
            }

            $updateData = [];
            
            if ($request->has('title')) {
                $updateData['title'] = $request->title;
            }
            
            if ($request->has('description')) {
                $updateData['description'] = $request->description;
            }
            
            if ($request->has('expires_at')) {
                $updateData['expires_at'] = $request->expires_at;
            }
            
            if ($request->has('password')) {
                $updateData['password'] = $request->password ? bcrypt($request->password) : null;
            }
            
            if ($request->has('is_public')) {
                $updateData['is_public'] = $request->is_public;
            }

            $link->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Link updated successfully',
                'data' => $link
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update link: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update link'
            ], 500);
        }
    }

    /**
     * Delete a shortened link
     */
    public function delete($id)
    {
        try {
            $user = Auth::user();
            
            $link = ShortenedLink::where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$link) {
                return response()->json([
                    'success' => false,
                    'message' => 'Link not found'
                ], 404);
            }

            $link->delete();

            return response()->json([
                'success' => true,
                'message' => 'Link deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete link: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete link'
            ], 500);
        }
    }

    /**
     * Redirect to original URL and track click
     */
    public function redirect($slug)
    {
        try {
            $link = ShortenedLink::where('slug', $slug)
                ->where('is_active', true)
                ->first();

            if (!$link) {
                return response()->json([
                    'success' => false,
                    'message' => 'Link not found'
                ], 404);
            }

            // Check if link is expired
            if ($link->expires_at && $link->expires_at < now()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Link has expired'
                ], 410);
            }

            // Check if link is password protected
            if ($link->password && !request()->has('password')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password required',
                    'requires_password' => true
                ], 401);
            }

            // Verify password if provided
            if ($link->password && request()->has('password')) {
                if (!password_verify(request()->password, $link->password)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid password'
                    ], 401);
                }
            }

            // Track the click
            $this->trackClick($link, request());

            return response()->json([
                'success' => true,
                'redirect_url' => $link->original_url,
                'message' => 'Redirecting...'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to redirect: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to redirect'
            ], 500);
        }
    }

    /**
     * Track a click on a shortened link
     */
    private function trackClick($link, $request)
    {
        try {
            $userAgent = $request->header('User-Agent', '');
            $ip = $request->ip();

            // Parse user agent for device info
            $deviceInfo = $this->parseUserAgent($userAgent);

            LinkClick::create([
                'shortened_link_id' => $link->id,
                'ip_address' => $ip,
                'user_agent' => $userAgent,
                'referrer' => $request->header('Referer'),
                'country' => $this->getCountryFromIP($ip),
                'device_type' => $deviceInfo['device_type'],
                'browser' => $deviceInfo['browser'],
                'platform' => $deviceInfo['platform'],
                'clicked_at' => now(),
            ]);

            // Increment click count
            $link->increment('click_count');
        } catch (\Exception $e) {
            Log::error('Failed to track click: ' . $e->getMessage());
        }
    }

    /**
     * Generate a unique slug for shortened links
     */
    private function generateUniqueSlug($length = 6)
    {
        do {
            $slug = Str::random($length);
        } while (ShortenedLink::where('slug', $slug)->exists());

        return $slug;
    }

    /**
     * Parse user agent for device information
     */
    private function parseUserAgent($userAgent)
    {
        $deviceType = 'Desktop';
        $browser = 'Unknown';
        $platform = 'Unknown';

        // Device type detection
        if (preg_match('/Mobile|Android|iPhone|iPad/', $userAgent)) {
            $deviceType = 'Mobile';
        } elseif (preg_match('/Tablet|iPad/', $userAgent)) {
            $deviceType = 'Tablet';
        }

        // Browser detection
        if (preg_match('/Chrome/', $userAgent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/Firefox/', $userAgent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/Safari/', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/Edge/', $userAgent)) {
            $browser = 'Edge';
        }

        // Platform detection
        if (preg_match('/Windows/', $userAgent)) {
            $platform = 'Windows';
        } elseif (preg_match('/Mac/', $userAgent)) {
            $platform = 'Mac';
        } elseif (preg_match('/Linux/', $userAgent)) {
            $platform = 'Linux';
        } elseif (preg_match('/Android/', $userAgent)) {
            $platform = 'Android';
        } elseif (preg_match('/iPhone|iPad/', $userAgent)) {
            $platform = 'iOS';
        }

        return [
            'device_type' => $deviceType,
            'browser' => $browser,
            'platform' => $platform,
        ];
    }

    /**
     * Get country from IP address (mock implementation)
     */
    private function getCountryFromIP($ip)
    {
        // This is a mock implementation
        // In production, you would use a service like MaxMind GeoIP
        return 'Unknown';
    }

    /**
     * Get bulk analytics for dashboard
     */
    public function bulkAnalytics(Request $request)
    {
        try {
            $user = Auth::user();
            $workspaceId = $request->workspace_id;

            $query = ShortenedLink::where('user_id', $user->id);

            if ($workspaceId) {
                $query->where('workspace_id', $workspaceId);
            }

            $totalLinks = $query->count();
            $totalClicks = $query->sum('click_count');

            // Get top performing links
            $topLinks = $query->orderBy('click_count', 'desc')
                ->limit(5)
                ->get(['id', 'title', 'slug', 'click_count', 'created_at']);

            // Get recent activity
            $recentActivity = LinkClick::whereHas('shortenedLink', function ($q) use ($user, $workspaceId) {
                $q->where('user_id', $user->id);
                if ($workspaceId) {
                    $q->where('workspace_id', $workspaceId);
                }
            })
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->with('shortenedLink:id,title,slug')
            ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_links' => $totalLinks,
                    'total_clicks' => $totalClicks,
                    'top_links' => $topLinks,
                    'recent_activity' => $recentActivity,
                ],
                'message' => 'Bulk analytics retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve bulk analytics: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve bulk analytics'
            ], 500);
        }
    }
}