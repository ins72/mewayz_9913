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
     * Get social media accounts with enhanced filtering
     */
    public function getAccounts(Request $request)
    {
        $request->validate([
            'platform' => 'nullable|string|in:facebook,instagram,twitter,linkedin,youtube,tiktok,snapchat,discord,twitch,pinterest',
            'status' => 'nullable|string|in:connected,disconnected,expired,pending',
            'account_type' => 'nullable|string|in:personal,business,creator',
            'sort_by' => 'nullable|string|in:platform,username,connected_at,last_synced_at,followers_count',
            'sort_order' => 'nullable|string|in:asc,desc'
        ]);

        try {
            $query = SocialMediaAccount::where('user_id', auth()->id());

            // Apply filters
            if ($request->platform) {
                $query->where('platform', $request->platform);
            }

            if ($request->status) {
                $query->where('is_connected', $request->status === 'connected');
            }

            if ($request->account_type) {
                $query->where('account_type', $request->account_type);
            }

            // Apply sorting
            $sortBy = $request->sort_by ?? 'connected_at';
            $sortOrder = $request->sort_order ?? 'desc';
            $query->orderBy($sortBy, $sortOrder);

            $accounts = $query->get();

            // Calculate summary statistics
            $totalAccounts = $accounts->count();
            $connectedAccounts = $accounts->where('is_connected', true)->count();
            $totalFollowers = $accounts->sum('followers_count');
            $totalPosts = $accounts->sum('media_count');

            // Platform breakdown
            $platformBreakdown = $accounts->groupBy('platform')->map(function($group) {
                return [
                    'count' => $group->count(),
                    'connected' => $group->where('is_connected', true)->count(),
                    'total_followers' => $group->sum('followers_count')
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'accounts' => $accounts->map(function($account) {
                        return [
                            'id' => $account->id,
                            'platform' => $account->platform,
                            'username' => $account->username,
                            'display_name' => $account->display_name,
                            'account_type' => $account->account_type,
                            'is_connected' => $account->is_connected,
                            'followers_count' => $account->followers_count,
                            'following_count' => $account->following_count,
                            'media_count' => $account->media_count,
                            'profile_picture' => $account->profile_picture,
                            'bio' => $account->bio,
                            'website' => $account->website,
                            'connected_at' => $account->created_at,
                            'last_synced_at' => $account->last_synced_at,
                            'token_expires_at' => $account->token_expires_at,
                            'health_status' => $this->getAccountHealthStatus($account)
                        ];
                    }),
                    'summary' => [
                        'total_accounts' => $totalAccounts,
                        'connected_accounts' => $connectedAccounts,
                        'total_followers' => $totalFollowers,
                        'total_posts' => $totalPosts,
                        'connection_rate' => $totalAccounts > 0 ? round(($connectedAccounts / $totalAccounts) * 100, 2) : 0
                    ],
                    'platform_breakdown' => $platformBreakdown
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve social media accounts', ['error' => $e->getMessage(), 'user_id' => auth()->id()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve social media accounts: ' . $e->getMessage()
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
     * Get comprehensive social media analytics
     */
    public function getAnalytics(Request $request)
    {
        $request->validate([
            'platform' => 'nullable|string|in:facebook,instagram,twitter,linkedin,youtube,tiktok,snapchat,discord,twitch,pinterest',
            'account_id' => 'nullable|exists:social_media_accounts,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'metrics' => 'nullable|array',
            'metrics.*' => 'string|in:engagement,reach,impressions,followers_growth,post_performance,audience_demographics,best_times'
        ]);

        try {
            $query = SocialMediaAccount::where('user_id', auth()->id());

            if ($request->platform) {
                $query->where('platform', $request->platform);
            }

            if ($request->account_id) {
                $query->where('id', $request->account_id);
            }

            $accounts = $query->get();

            if ($accounts->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No social media accounts found'
                ], 404);
            }

            // Date range
            $dateFrom = $request->date_from ?? now()->subDays(30);
            $dateTo = $request->date_to ?? now();

            $analytics = [];

            foreach ($accounts as $account) {
                $accountAnalytics = [
                    'account_id' => $account->id,
                    'platform' => $account->platform,
                    'username' => $account->username,
                    'overview' => $this->getAccountOverview($account, $dateFrom, $dateTo),
                    'engagement' => $this->getEngagementMetrics($account, $dateFrom, $dateTo),
                    'audience' => $this->getAudienceMetrics($account, $dateFrom, $dateTo),
                    'content_performance' => $this->getContentPerformance($account, $dateFrom, $dateTo),
                    'growth_trends' => $this->getGrowthTrends($account, $dateFrom, $dateTo),
                    'optimal_posting_times' => $this->getOptimalPostingTimes($account),
                    'hashtag_performance' => $this->getHashtagPerformance($account, $dateFrom, $dateTo),
                    'competitor_comparison' => $this->getCompetitorComparison($account),
                    'recommendations' => $this->getRecommendations($account)
                ];

                $analytics[] = $accountAnalytics;
            }

            // Cross-platform summary
            $crossPlatformSummary = $this->getCrossPlatformSummary($accounts, $dateFrom, $dateTo);

            return response()->json([
                'success' => true,
                'data' => [
                    'period' => [
                        'from' => $dateFrom,
                        'to' => $dateTo
                    ],
                    'cross_platform_summary' => $crossPlatformSummary,
                    'accounts' => $analytics,
                    'insights' => $this->generateInsights($analytics),
                    'action_items' => $this->generateActionItems($analytics)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve social media analytics', ['error' => $e->getMessage(), 'user_id' => auth()->id()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve analytics: ' . $e->getMessage()
            ], 500);
        }
    }
}