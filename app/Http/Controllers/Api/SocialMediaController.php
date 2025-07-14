<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SocialMediaAccount;
use App\Models\SocialMediaPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class SocialMediaController extends Controller
{
    /**
     * Get connected social media accounts for the authenticated user
     */
    public function getAccounts(Request $request)
    {
        try {
            $accounts = SocialMediaAccount::where('user_id', $request->user()->id)
                ->select(['id', 'platform', 'username', 'display_name', 'followers_count', 'is_active', 'connected_at'])
                ->orderBy('platform')
                ->get()
                ->map(function ($account) {
                    return [
                        'id' => $account->id,
                        'platform' => $account->platform,
                        'username' => $account->username,
                        'display_name' => $account->display_name,
                        'connected' => $account->is_active,
                        'followers' => $account->followers_count ?? 0,
                        'connected_at' => $account->connected_at,
                        'avatar' => $account->avatar_url,
                    ];
                });

            // Add disconnected platforms
            $connectedPlatforms = $accounts->pluck('platform')->toArray();
            $allPlatforms = ['instagram', 'facebook', 'twitter', 'linkedin', 'tiktok', 'youtube'];
            
            foreach ($allPlatforms as $platform) {
                if (!in_array($platform, $connectedPlatforms)) {
                    $accounts->push([
                        'id' => null,
                        'platform' => $platform,
                        'username' => null,
                        'display_name' => ucfirst($platform),
                        'connected' => false,
                        'followers' => 0,
                        'connected_at' => null,
                        'avatar' => null,
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'data' => $accounts->values(),
                'message' => 'Social media accounts retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve social media accounts: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve social media accounts'
            ], 500);
        }
    }

    /**
     * Connect a social media account
     */
    public function connectAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'platform' => 'required|in:instagram,facebook,twitter,linkedin,tiktok,youtube',
            'username' => 'required|string|max:255',
            'display_name' => 'nullable|string|max:255',
            'access_token' => 'required|string',
            'access_token_secret' => 'nullable|string', // For Twitter OAuth 1.0
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Check if account already exists for this user
            $existingAccount = SocialMediaAccount::where([
                'user_id' => $request->user()->id,
                'platform' => $request->platform,
                'username' => $request->username
            ])->first();

            if ($existingAccount) {
                // Update existing account
                $existingAccount->update([
                    'access_token' => encrypt($request->access_token),
                    'access_token_secret' => $request->access_token_secret ? encrypt($request->access_token_secret) : null,
                    'is_active' => true,
                    'connected_at' => now(),
                    'display_name' => $request->display_name ?? $request->username,
                ]);

                $account = $existingAccount;
            } else {
                // Create new account
                $account = SocialMediaAccount::create([
                    'user_id' => $request->user()->id,
                    'platform' => $request->platform,
                    'username' => $request->username,
                    'display_name' => $request->display_name ?? $request->username,
                    'access_token' => encrypt($request->access_token),
                    'access_token_secret' => $request->access_token_secret ? encrypt($request->access_token_secret) : null,
                    'is_active' => true,
                    'connected_at' => now(),
                ]);
            }

            // Log the connection
            Log::info("Social media account connected", [
                'user_id' => $request->user()->id,
                'platform' => $request->platform,
                'username' => $request->username
            ]);

            return response()->json([
                'success' => true,
                'message' => ucfirst($request->platform) . ' account connected successfully',
                'data' => [
                    'id' => $account->id,
                    'platform' => $account->platform,
                    'username' => $account->username,
                    'display_name' => $account->display_name,
                    'connected' => true,
                    'connected_at' => $account->connected_at,
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Failed to connect social media account: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to connect account. Please try again.'
            ], 500);
        }
    }

    /**
     * Disconnect a social media account
     */
    public function disconnectAccount(Request $request, $accountId)
    {
        try {
            $account = SocialMediaAccount::where([
                'id' => $accountId,
                'user_id' => $request->user()->id
            ])->first();

            if (!$account) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account not found or unauthorized'
                ], 404);
            }

            $platform = $account->platform;
            $account->update(['is_active' => false]);

            Log::info("Social media account disconnected", [
                'user_id' => $request->user()->id,
                'platform' => $platform,
                'account_id' => $accountId
            ]);

            return response()->json([
                'success' => true,
                'message' => ucfirst($platform) . ' account disconnected successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to disconnect social media account: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to disconnect account'
            ], 500);
        }
    }

    /**
     * Get scheduled posts
     */
    public function getScheduledPosts(Request $request)
    {
        try {
            $posts = SocialMediaPost::with('accounts')
                ->where('user_id', $request->user()->id)
                ->where('status', 'scheduled')
                ->where('scheduled_at', '>', now())
                ->orderBy('scheduled_at', 'asc')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $posts,
                'message' => 'Scheduled posts retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve scheduled posts: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve scheduled posts'
            ], 500);
        }
    }

    /**
     * Create and schedule a post
     */
    public function createPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:2200', // Instagram limit
            'platforms' => 'required|array|min:1',
            'platforms.*' => 'exists:social_media_accounts,id',
            'scheduled_at' => 'nullable|date|after:now',
            'media' => 'nullable|array|max:10',
            'media.*' => 'string', // Base64 encoded images or URLs
            'hashtags' => 'nullable|array',
            'hashtags.*' => 'string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Verify all selected accounts belong to the user
            $accounts = SocialMediaAccount::whereIn('id', $request->platforms)
                ->where('user_id', $request->user()->id)
                ->where('is_active', true)
                ->get();

            if ($accounts->count() !== count($request->platforms)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Some selected accounts are invalid or not connected'
                ], 422);
            }

            // Create the post
            $post = SocialMediaPost::create([
                'user_id' => $request->user()->id,
                'content' => $request->content,
                'media_urls' => $request->media ? json_encode($request->media) : null,
                'hashtags' => $request->hashtags ? json_encode($request->hashtags) : null,
                'scheduled_at' => $request->scheduled_at ? Carbon::parse($request->scheduled_at) : now(),
                'status' => $request->scheduled_at ? 'scheduled' : 'draft',
                'post_type' => $request->media ? 'media' : 'text',
            ]);

            // Attach the post to the selected accounts
            $post->accounts()->attach($request->platforms);

            // If not scheduled, publish immediately
            if (!$request->scheduled_at) {
                // TODO: Implement immediate posting to social platforms
                $post->update(['status' => 'published', 'published_at' => now()]);
            }

            Log::info("Social media post created", [
                'user_id' => $request->user()->id,
                'post_id' => $post->id,
                'platforms' => $accounts->pluck('platform')->toArray(),
                'scheduled_at' => $post->scheduled_at
            ]);

            return response()->json([
                'success' => true,
                'message' => $request->scheduled_at ? 'Post scheduled successfully' : 'Post published successfully',
                'data' => [
                    'id' => $post->id,
                    'content' => $post->content,
                    'scheduled_at' => $post->scheduled_at,
                    'status' => $post->status,
                    'platforms' => $accounts->pluck('platform')->toArray(),
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Failed to create social media post: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create post. Please try again.'
            ], 500);
        }
    }

    /**
     * Get analytics for social media accounts
     */
    public function getAnalytics(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'period' => 'nullable|in:7d,30d,90d',
            'platform' => 'nullable|in:instagram,facebook,twitter,linkedin,tiktok,youtube',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $period = $request->get('period', '30d');
            $days = (int) str_replace('d', '', $period);
            $startDate = Carbon::now()->subDays($days);

            $query = SocialMediaPost::where('user_id', $request->user()->id)
                ->where('created_at', '>=', $startDate);

            if ($request->platform) {
                $query->whereHas('accounts', function ($q) use ($request) {
                    $q->where('platform', $request->platform);
                });
            }

            $posts = $query->get();

            $analytics = [
                'total_posts' => $posts->count(),
                'published_posts' => $posts->where('status', 'published')->count(),
                'scheduled_posts' => $posts->where('status', 'scheduled')->count(),
                'draft_posts' => $posts->where('status', 'draft')->count(),
                'engagement_rate' => 0, // TODO: Calculate based on actual platform data
                'reach' => 0, // TODO: Calculate based on actual platform data
                'period' => $period,
                'platforms_breakdown' => [],
            ];

            // Get platform breakdown
            $connectedAccounts = SocialMediaAccount::where('user_id', $request->user()->id)
                ->where('is_active', true)
                ->get();

            foreach ($connectedAccounts as $account) {
                $platformPosts = $posts->filter(function ($post) use ($account) {
                    return $post->accounts->contains('id', $account->id);
                });

                $analytics['platforms_breakdown'][$account->platform] = [
                    'posts' => $platformPosts->count(),
                    'followers' => $account->followers_count ?? 0,
                    'username' => $account->username,
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $analytics,
                'message' => 'Analytics retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve social media analytics: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve analytics'
            ], 500);
        }
    }
}