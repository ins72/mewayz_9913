<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\SocialMediaAccount;
use App\Models\SocialMediaPost;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;

class InstagramController extends Controller
{
    private $baseUrl = 'https://graph.facebook.com/v18.0';
    private $instagramUrl = 'https://api.instagram.com';

    /**
     * Initiate Instagram OAuth flow
     */
    public function initiateAuth(Request $request)
    {
        $clientId = config('services.instagram.client_id');
        $redirectUri = config('services.instagram.redirect_uri');
        
        $params = [
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'user_profile,user_media'
        ];

        $authUrl = $this->instagramUrl . '/oauth/authorize?' . http_build_query($params);

        return response()->json([
            'success' => true,
            'auth_url' => $authUrl,
            'message' => 'Redirect to Instagram for authorization'
        ]);
    }

    /**
     * Handle OAuth callback and exchange code for token
     */
    public function handleCallback(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'state' => 'nullable|string'
        ]);

        try {
            $tokenData = [
                'client_id' => config('services.instagram.client_id'),
                'client_secret' => config('services.instagram.client_secret'),
                'grant_type' => 'authorization_code',
                'redirect_uri' => config('services.instagram.redirect_uri'),
                'code' => $request->code
            ];

            $response = Http::asForm()->post($this->instagramUrl . '/oauth/access_token', $tokenData);

            if ($response->failed()) {
                Log::error('Instagram OAuth failed', ['response' => $response->body()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to authenticate with Instagram'
                ], 400);
            }

            $tokenInfo = $response->json();

            // Get user profile info
            $profileResponse = Http::get($this->baseUrl . '/me', [
                'fields' => 'id,username,account_type,media_count',
                'access_token' => $tokenInfo['access_token']
            ]);

            if ($profileResponse->failed()) {
                Log::error('Failed to get Instagram profile', ['response' => $profileResponse->body()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to get Instagram profile'
                ], 400);
            }

            $profile = $profileResponse->json();

            // Store or update social media account
            $socialAccount = SocialMediaAccount::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'platform' => 'instagram',
                    'platform_user_id' => $profile['id']
                ],
                [
                    'username' => $profile['username'],
                    'account_type' => $profile['account_type'] ?? 'personal',
                    'access_token' => Crypt::encryptString($tokenInfo['access_token']),
                    'token_expires_at' => Carbon::now()->addSeconds($tokenInfo['expires_in'] ?? 5184000),
                    'followers_count' => 0,
                    'following_count' => 0,
                    'media_count' => $profile['media_count'] ?? 0,
                    'is_connected' => true,
                    'last_synced_at' => Carbon::now()
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Instagram account connected successfully',
                'account' => [
                    'id' => $socialAccount->id,
                    'username' => $socialAccount->username,
                    'account_type' => $socialAccount->account_type,
                    'media_count' => $socialAccount->media_count,
                    'connected_at' => $socialAccount->updated_at
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Instagram OAuth error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Authentication failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get competitor analysis
     */
    public function getCompetitorAnalysis(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'account_id' => 'required|exists:social_media_accounts,id'
        ]);

        try {
            $account = SocialMediaAccount::where('id', $request->account_id)
                ->where('user_id', auth()->id())
                ->where('platform', 'instagram')
                ->first();

            if (!$account || !$account->is_connected) {
                return response()->json([
                    'success' => false,
                    'message' => 'Instagram account not connected'
                ], 400);
            }

            // Check if token is expired
            if (Carbon::now()->greaterThan($account->token_expires_at)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Instagram token expired. Please reconnect your account.'
                ], 401);
            }

            $accessToken = Crypt::decryptString($account->access_token);

            // Get competitor data via business discovery
            $params = [
                'fields' => "business_discovery.username({$request->username}){followers_count,media_count,biography,profile_picture_url,website}",
                'access_token' => $accessToken
            ];

            $response = Http::get($this->baseUrl . '/' . $account->platform_user_id, $params);

            if ($response->failed()) {
                Log::error('Competitor analysis failed', ['response' => $response->body()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch competitor data. Make sure you have a business account.'
                ], 400);
            }

            $data = $response->json();
            $competitor = $data['business_discovery'] ?? null;

            if (!$competitor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Competitor account not found or not accessible'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'username' => $request->username,
                    'followers_count' => $competitor['followers_count'] ?? 0,
                    'media_count' => $competitor['media_count'] ?? 0,
                    'biography' => $competitor['biography'] ?? '',
                    'profile_picture_url' => $competitor['profile_picture_url'] ?? '',
                    'website' => $competitor['website'] ?? '',
                    'analyzed_at' => Carbon::now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Competitor analysis error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to analyze competitor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get hashtag analysis
     */
    public function getHashtagAnalysis(Request $request)
    {
        $request->validate([
            'hashtag' => 'required|string|max:100',
            'account_id' => 'required|exists:social_media_accounts,id'
        ]);

        try {
            $account = SocialMediaAccount::where('id', $request->account_id)
                ->where('user_id', auth()->id())
                ->where('platform', 'instagram')
                ->first();

            if (!$account || !$account->is_connected) {
                return response()->json([
                    'success' => false,
                    'message' => 'Instagram account not connected'
                ], 400);
            }

            // Check if token is expired
            if (Carbon::now()->greaterThan($account->token_expires_at)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Instagram token expired. Please reconnect your account.'
                ], 401);
            }

            $accessToken = Crypt::decryptString($account->access_token);
            $hashtag = ltrim($request->hashtag, '#');

            // Search for hashtag
            $searchResponse = Http::get($this->baseUrl . '/ig_hashtag_search', [
                'user_id' => $account->platform_user_id,
                'q' => $hashtag,
                'access_token' => $accessToken
            ]);

            if ($searchResponse->failed()) {
                Log::error('Hashtag search failed', ['response' => $searchResponse->body()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to search hashtag. Business account required.'
                ], 400);
            }

            $searchData = $searchResponse->json();
            
            if (empty($searchData['data'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hashtag not found'
                ], 404);
            }

            $hashtagId = $searchData['data'][0]['id'];

            // Get top media for hashtag
            $topMediaResponse = Http::get($this->baseUrl . '/' . $hashtagId . '/top_media', [
                'user_id' => $account->platform_user_id,
                'fields' => 'id,media_type,media_url,permalink,timestamp,like_count,comments_count,caption',
                'access_token' => $accessToken,
                'limit' => 10
            ]);

            if ($topMediaResponse->failed()) {
                Log::error('Top media fetch failed', ['response' => $topMediaResponse->body()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch top media for hashtag'
                ], 400);
            }

            $topMedia = $topMediaResponse->json();

            // Get recent media for hashtag
            $recentMediaResponse = Http::get($this->baseUrl . '/' . $hashtagId . '/recent_media', [
                'user_id' => $account->platform_user_id,
                'fields' => 'id,media_type,media_url,permalink,timestamp,like_count,comments_count,caption',
                'access_token' => $accessToken,
                'limit' => 10
            ]);

            $recentMedia = $recentMediaResponse->failed() ? ['data' => []] : $recentMediaResponse->json();

            return response()->json([
                'success' => true,
                'data' => [
                    'hashtag' => '#' . $hashtag,
                    'hashtag_id' => $hashtagId,
                    'top_media' => $topMedia['data'] ?? [],
                    'recent_media' => $recentMedia['data'] ?? [],
                    'analyzed_at' => Carbon::now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Hashtag analysis error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to analyze hashtag: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's own Instagram analytics
     */
    public function getAnalytics(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:social_media_accounts,id',
            'period' => 'in:day,week,days_28',
            'since' => 'date',
            'until' => 'date'
        ]);

        try {
            $account = SocialMediaAccount::where('id', $request->account_id)
                ->where('user_id', auth()->id())
                ->where('platform', 'instagram')
                ->first();

            if (!$account || !$account->is_connected) {
                return response()->json([
                    'success' => false,
                    'message' => 'Instagram account not connected'
                ], 400);
            }

            // Check if token is expired
            if (Carbon::now()->greaterThan($account->token_expires_at)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Instagram token expired. Please reconnect your account.'
                ], 401);
            }

            $accessToken = Crypt::decryptString($account->access_token);

            // Get account insights (for business accounts)
            $insightsParams = [
                'metric' => 'impressions,reach,profile_views,website_clicks',
                'period' => $request->period ?? 'day',
                'access_token' => $accessToken
            ];

            if ($request->since) {
                $insightsParams['since'] = Carbon::parse($request->since)->timestamp;
            }
            if ($request->until) {
                $insightsParams['until'] = Carbon::parse($request->until)->timestamp;
            }

            $insightsResponse = Http::get($this->baseUrl . '/' . $account->platform_user_id . '/insights', $insightsParams);

            $insights = $insightsResponse->failed() ? ['data' => []] : $insightsResponse->json();

            // Get recent media
            $mediaResponse = Http::get($this->baseUrl . '/' . $account->platform_user_id . '/media', [
                'fields' => 'id,media_type,media_url,permalink,timestamp,like_count,comments_count,caption',
                'access_token' => $accessToken,
                'limit' => 20
            ]);

            $media = $mediaResponse->failed() ? ['data' => []] : $mediaResponse->json();

            // Calculate basic stats
            $totalLikes = collect($media['data'] ?? [])->sum('like_count');
            $totalComments = collect($media['data'] ?? [])->sum('comments_count');
            $totalPosts = count($media['data'] ?? []);

            return response()->json([
                'success' => true,
                'data' => [
                    'account' => [
                        'username' => $account->username,
                        'account_type' => $account->account_type,
                        'followers_count' => $account->followers_count,
                        'following_count' => $account->following_count,
                        'media_count' => $account->media_count
                    ],
                    'insights' => $insights['data'] ?? [],
                    'recent_media' => $media['data'] ?? [],
                    'summary' => [
                        'total_posts' => $totalPosts,
                        'total_likes' => $totalLikes,
                        'total_comments' => $totalComments,
                        'avg_likes_per_post' => $totalPosts > 0 ? round($totalLikes / $totalPosts) : 0,
                        'avg_comments_per_post' => $totalPosts > 0 ? round($totalComments / $totalPosts) : 0,
                        'engagement_rate' => $account->followers_count > 0 ? round((($totalLikes + $totalComments) / $account->followers_count) * 100, 2) : 0
                    ],
                    'analyzed_at' => Carbon::now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Instagram analytics error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch analytics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refresh Instagram access token
     */
    public function refreshToken(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:social_media_accounts,id'
        ]);

        try {
            $account = SocialMediaAccount::where('id', $request->account_id)
                ->where('user_id', auth()->id())
                ->where('platform', 'instagram')
                ->first();

            if (!$account || !$account->is_connected) {
                return response()->json([
                    'success' => false,
                    'message' => 'Instagram account not connected'
                ], 400);
            }

            $accessToken = Crypt::decryptString($account->access_token);

            // Refresh long-lived token
            $response = Http::get($this->baseUrl . '/refresh_access_token', [
                'grant_type' => 'ig_refresh_token',
                'access_token' => $accessToken
            ]);

            if ($response->failed()) {
                Log::error('Token refresh failed', ['response' => $response->body()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to refresh token'
                ], 400);
            }

            $tokenData = $response->json();

            // Update token in database
            $account->update([
                'access_token' => Crypt::encryptString($tokenData['access_token']),
                'token_expires_at' => Carbon::now()->addSeconds($tokenData['expires_in']),
                'last_synced_at' => Carbon::now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Token refreshed successfully',
                'expires_at' => $account->token_expires_at->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('Token refresh error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh token: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get content optimization suggestions
     */
    public function getContentSuggestions(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:social_media_accounts,id',
            'content_type' => 'in:hashtags,captions,timing'
        ]);

        try {
            $account = SocialMediaAccount::where('id', $request->account_id)
                ->where('user_id', auth()->id())
                ->where('platform', 'instagram')
                ->first();

            if (!$account || !$account->is_connected) {
                return response()->json([
                    'success' => false,
                    'message' => 'Instagram account not connected'
                ], 400);
            }

            $accessToken = Crypt::decryptString($account->access_token);

            // Get recent media for analysis
            $mediaResponse = Http::get($this->baseUrl . '/' . $account->platform_user_id . '/media', [
                'fields' => 'id,media_type,caption,like_count,comments_count,timestamp',
                'access_token' => $accessToken,
                'limit' => 50
            ]);

            if ($mediaResponse->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch media for analysis'
                ], 400);
            }

            $media = $mediaResponse->json()['data'] ?? [];

            $suggestions = [];

            // Hashtag suggestions
            if ($request->content_type === 'hashtags' || !$request->content_type) {
                $hashtagAnalysis = $this->analyzeHashtags($media);
                $suggestions['hashtags'] = $hashtagAnalysis;
            }

            // Caption suggestions
            if ($request->content_type === 'captions' || !$request->content_type) {
                $captionAnalysis = $this->analyzeCaptions($media);
                $suggestions['captions'] = $captionAnalysis;
            }

            // Timing suggestions
            if ($request->content_type === 'timing' || !$request->content_type) {
                $timingAnalysis = $this->analyzePostTiming($media);
                $suggestions['timing'] = $timingAnalysis;
            }

            return response()->json([
                'success' => true,
                'data' => $suggestions,
                'analyzed_at' => Carbon::now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('Content suggestions error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate suggestions: ' . $e->getMessage()
            ], 500);
        }
    }

    private function analyzeHashtags($media)
    {
        $hashtagStats = [];
        
        foreach ($media as $post) {
            if (!isset($post['caption'])) continue;
            
            preg_match_all('/#(\w+)/', $post['caption'], $matches);
            $hashtags = $matches[1];
            
            foreach ($hashtags as $hashtag) {
                $hashtag = strtolower($hashtag);
                if (!isset($hashtagStats[$hashtag])) {
                    $hashtagStats[$hashtag] = [
                        'count' => 0,
                        'total_likes' => 0,
                        'total_comments' => 0
                    ];
                }
                
                $hashtagStats[$hashtag]['count']++;
                $hashtagStats[$hashtag]['total_likes'] += $post['like_count'] ?? 0;
                $hashtagStats[$hashtag]['total_comments'] += $post['comments_count'] ?? 0;
            }
        }

        // Calculate performance metrics
        $topHashtags = [];
        foreach ($hashtagStats as $hashtag => $stats) {
            if ($stats['count'] >= 2) { // Only hashtags used at least twice
                $avgLikes = $stats['total_likes'] / $stats['count'];
                $avgComments = $stats['total_comments'] / $stats['count'];
                
                $topHashtags[] = [
                    'hashtag' => '#' . $hashtag,
                    'usage_count' => $stats['count'],
                    'avg_likes' => round($avgLikes),
                    'avg_comments' => round($avgComments),
                    'performance_score' => round(($avgLikes + $avgComments) / 2)
                ];
            }
        }

        // Sort by performance score
        usort($topHashtags, function($a, $b) {
            return $b['performance_score'] - $a['performance_score'];
        });

        return [
            'top_performing_hashtags' => array_slice($topHashtags, 0, 10),
            'recommendations' => [
                'Use your best performing hashtags more frequently',
                'Mix popular hashtags with niche ones',
                'Keep hashtags relevant to your content',
                'Use 5-10 hashtags per post for optimal reach'
            ]
        ];
    }

    private function analyzeCaptions($media)
    {
        $captionStats = [];
        
        foreach ($media as $post) {
            if (!isset($post['caption'])) continue;
            
            $captionLength = strlen($post['caption']);
            $wordCount = str_word_count($post['caption']);
            $hasQuestion = strpos($post['caption'], '?') !== false;
            $hasEmoji = preg_match('/[\x{1F600}-\x{1F64F}]|[\x{1F300}-\x{1F5FF}]|[\x{1F680}-\x{1F6FF}]|[\x{2600}-\x{26FF}]|[\x{2700}-\x{27BF}]/u', $post['caption']);
            
            $captionStats[] = [
                'length' => $captionLength,
                'word_count' => $wordCount,
                'has_question' => $hasQuestion,
                'has_emoji' => $hasEmoji,
                'likes' => $post['like_count'] ?? 0,
                'comments' => $post['comments_count'] ?? 0,
                'engagement' => ($post['like_count'] ?? 0) + ($post['comments_count'] ?? 0)
            ];
        }

        // Analyze patterns
        $avgEngagement = collect($captionStats)->avg('engagement');
        $highEngagementPosts = collect($captionStats)->where('engagement', '>', $avgEngagement);
        
        $optimalLength = $highEngagementPosts->avg('length');
        $questionEngagement = $highEngagementPosts->where('has_question', true)->avg('engagement');
        $emojiEngagement = $highEngagementPosts->where('has_emoji', true)->avg('engagement');

        return [
            'optimal_caption_length' => round($optimalLength),
            'questions_boost_engagement' => $questionEngagement > $avgEngagement,
            'emojis_boost_engagement' => $emojiEngagement > $avgEngagement,
            'recommendations' => [
                'Keep captions around ' . round($optimalLength) . ' characters',
                $questionEngagement > $avgEngagement ? 'Ask questions to boost engagement' : 'Questions may not be effective for your audience',
                $emojiEngagement > $avgEngagement ? 'Use emojis to increase engagement' : 'Emojis may not be necessary for your content',
                'Include a clear call-to-action'
            ]
        ];
    }

    private function analyzePostTiming($media)
    {
        $timingStats = [];
        
        foreach ($media as $post) {
            if (!isset($post['timestamp'])) continue;
            
            $timestamp = Carbon::parse($post['timestamp']);
            $hour = $timestamp->hour;
            $dayOfWeek = $timestamp->dayOfWeek;
            
            $key = $dayOfWeek . '_' . $hour;
            if (!isset($timingStats[$key])) {
                $timingStats[$key] = [
                    'count' => 0,
                    'total_engagement' => 0,
                    'day' => $timestamp->format('l'),
                    'hour' => $hour
                ];
            }
            
            $timingStats[$key]['count']++;
            $timingStats[$key]['total_engagement'] += ($post['like_count'] ?? 0) + ($post['comments_count'] ?? 0);
        }

        // Calculate average engagement by time
        $timingPerformance = [];
        foreach ($timingStats as $key => $stats) {
            if ($stats['count'] >= 1) {
                $avgEngagement = $stats['total_engagement'] / $stats['count'];
                $timingPerformance[] = [
                    'day' => $stats['day'],
                    'hour' => $stats['hour'],
                    'hour_formatted' => sprintf('%02d:00', $stats['hour']),
                    'post_count' => $stats['count'],
                    'avg_engagement' => round($avgEngagement)
                ];
            }
        }

        // Sort by engagement
        usort($timingPerformance, function($a, $b) {
            return $b['avg_engagement'] - $a['avg_engagement'];
        });

        return [
            'best_times' => array_slice($timingPerformance, 0, 5),
            'recommendations' => [
                'Post during your audience\'s most active hours',
                'Maintain consistent posting schedule',
                'Test different times to find optimal posting windows',
                'Consider time zones of your target audience'
            ]
        ];
    }
}