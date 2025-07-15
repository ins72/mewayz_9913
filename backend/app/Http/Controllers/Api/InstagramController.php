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
    use InstagramAdvancedHelpers;
    
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
            'account_id' => 'nullable|exists:social_media_accounts,id',
            'period' => 'in:day,week,days_28',
            'since' => 'date',
            'until' => 'date'
        ]);

        try {
            $user = $request->user();
            
            // If no account_id provided, get the first Instagram account
            if (!$request->account_id) {
                $account = SocialMediaAccount::where('user_id', $user->id)
                    ->where('platform', 'instagram')
                    ->where('is_active', true)
                    ->first();
                    
                if (!$account) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No Instagram account connected'
                    ], 400);
                }
            } else {
                $account = SocialMediaAccount::where('id', $request->account_id)
                    ->where('user_id', $user->id)
                    ->where('platform', 'instagram')
                    ->first();
            }

            if (!$account) {
                return response()->json([
                    'success' => false,
                    'message' => 'Instagram account not found or not connected'
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
     * Advanced AI-powered competitor intelligence with benchmarking
     */
    public function getAdvancedCompetitorAnalysis(Request $request)
    {
        $request->validate([
            'competitors' => 'required|array|min:1|max:10',
            'competitors.*' => 'required|string|max:255',
            'account_id' => 'required|exists:social_media_accounts,id',
            'analysis_depth' => 'required|string|in:basic,advanced,comprehensive',
            'metrics' => 'nullable|array',
            'metrics.*' => 'string|in:engagement,growth,content_strategy,audience_overlap,posting_frequency,hashtag_strategy,influencer_partnerships,brand_mentions',
            'time_period' => 'nullable|string|in:7d,30d,90d,1y',
            'include_predictions' => 'boolean',
            'benchmark_against' => 'nullable|string|in:industry_average,top_performers,similar_accounts'
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
            $competitors = $request->competitors;
            $analysisDepth = $request->analysis_depth;
            $timePeriod = $request->time_period ?? '30d';

            $competitorData = [];
            $industryBenchmarks = [];
            $aiInsights = [];

            foreach ($competitors as $competitorUsername) {
                $competitorAnalysis = $this->analyzeCompetitorInDepth($competitorUsername, $accessToken, $account, $analysisDepth, $timePeriod);
                $competitorData[] = $competitorAnalysis;
            }

            // Generate AI-powered insights and recommendations
            $aiInsights = $this->generateAICompetitorInsights($competitorData, $account);
            
            // Calculate industry benchmarks
            $industryBenchmarks = $this->calculateIndustryBenchmarks($competitorData);
            
            // Generate competitive positioning
            $competitivePositioning = $this->analyzeCompetitivePositioning($account, $competitorData);
            
            // Content gap analysis
            $contentGaps = $this->identifyContentGaps($account, $competitorData);
            
            // Opportunity identification
            $opportunities = $this->identifyCompetitiveOpportunities($competitorData, $account);

            // Predict future performance if requested
            $predictions = [];
            if ($request->include_predictions) {
                $predictions = $this->generatePerformancePredictions($competitorData, $account);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'analysis_summary' => [
                        'competitors_analyzed' => count($competitorData),
                        'analysis_depth' => $analysisDepth,
                        'time_period' => $timePeriod,
                        'your_ranking' => $this->calculateYourRanking($account, $competitorData),
                        'overall_score' => $this->calculateOverallCompetitiveScore($account, $competitorData)
                    ],
                    'competitor_profiles' => $competitorData,
                    'industry_benchmarks' => $industryBenchmarks,
                    'competitive_positioning' => $competitivePositioning,
                    'content_gap_analysis' => $contentGaps,
                    'opportunities' => $opportunities,
                    'ai_insights' => $aiInsights,
                    'predictions' => $predictions,
                    'recommendations' => $this->generateAdvancedRecommendations($competitorData, $account, $aiInsights)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Advanced competitor analysis failed', ['error' => $e->getMessage(), 'user_id' => auth()->id()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to analyze competitors: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * AI-powered content performance prediction
     */
    public function predictContentPerformance(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:social_media_accounts,id',
            'content_text' => 'required|string|max:2200',
            'hashtags' => 'nullable|array|max:30',
            'hashtags.*' => 'string|max:100',
            'post_type' => 'required|string|in:photo,video,carousel,story,reel',
            'scheduled_time' => 'nullable|date_format:H:i',
            'scheduled_day' => 'nullable|string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'include_media_analysis' => 'boolean',
            'media_urls' => 'nullable|array|max:10',
            'media_urls.*' => 'url',
            'target_audience' => 'nullable|array',
            'comparison_posts' => 'nullable|array|max:5',
            'comparison_posts.*' => 'string'
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

            // Analyze content sentiment and engagement factors
            $contentAnalysis = $this->analyzeContentSentiment($request->content_text);
            $hashtagAnalysis = $this->analyzeHashtagPotential($request->hashtags ?? [], $account);
            $timingAnalysis = $this->analyzePostingTiming($request->scheduled_time, $request->scheduled_day, $account);
            $audienceAlignment = $this->analyzeAudienceAlignment($request->content_text, $request->target_audience, $account);

            // Historical performance analysis
            $historicalData = $this->getHistoricalPerformanceData($account, $request->post_type);
            
            // AI prediction algorithms
            $engagementPrediction = $this->predictEngagement($contentAnalysis, $hashtagAnalysis, $timingAnalysis, $historicalData);
            $reachPrediction = $this->predictReach($contentAnalysis, $hashtagAnalysis, $account);
            $viralPotential = $this->calculateViralPotential($contentAnalysis, $hashtagAnalysis, $timingAnalysis, $account);

            // Content optimization suggestions
            $optimizationSuggestions = $this->generateContentOptimizations($contentAnalysis, $hashtagAnalysis, $account);

            // Competitive benchmarking
            $competitiveBenchmark = $this->benchmarkAgainstCompetitors($contentAnalysis, $account);

            return response()->json([
                'success' => true,
                'data' => [
                    'prediction_summary' => [
                        'predicted_engagement_rate' => $engagementPrediction['rate'],
                        'predicted_likes' => $engagementPrediction['likes'],
                        'predicted_comments' => $engagementPrediction['comments'],
                        'predicted_shares' => $engagementPrediction['shares'],
                        'predicted_reach' => $reachPrediction,
                        'viral_potential_score' => $viralPotential,
                        'overall_performance_score' => $this->calculateOverallScore($engagementPrediction, $reachPrediction, $viralPotential),
                        'confidence_level' => $this->calculatePredictionConfidence($historicalData)
                    ],
                    'content_analysis' => [
                        'sentiment_score' => $contentAnalysis['sentiment'],
                        'emotion_analysis' => $contentAnalysis['emotions'],
                        'readability_score' => $contentAnalysis['readability'],
                        'keyword_density' => $contentAnalysis['keywords'],
                        'call_to_action_strength' => $contentAnalysis['cta_strength'],
                        'audience_alignment' => $audienceAlignment
                    ],
                    'hashtag_analysis' => [
                        'hashtag_performance_score' => $hashtagAnalysis['performance_score'],
                        'trending_hashtags' => $hashtagAnalysis['trending'],
                        'niche_hashtags' => $hashtagAnalysis['niche'],
                        'competition_level' => $hashtagAnalysis['competition'],
                        'reach_potential' => $hashtagAnalysis['reach_potential']
                    ],
                    'timing_analysis' => [
                        'optimal_timing_score' => $timingAnalysis['score'],
                        'audience_activity_level' => $timingAnalysis['activity'],
                        'competition_density' => $timingAnalysis['competition'],
                        'suggested_alternatives' => $timingAnalysis['alternatives']
                    ],
                    'optimization_suggestions' => $optimizationSuggestions,
                    'competitive_benchmark' => $competitiveBenchmark,
                    'improvement_recommendations' => $this->generateImprovementRecommendations($contentAnalysis, $hashtagAnalysis, $timingAnalysis)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Content performance prediction failed', ['error' => $e->getMessage(), 'user_id' => auth()->id()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to predict content performance: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Advanced audience intelligence and segmentation
     */
    public function getAdvancedAudienceIntelligence(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:social_media_accounts,id',
            'analysis_type' => 'required|string|in:demographic,psychographic,behavioral,interest_based,lookalike',
            'segment_size' => 'nullable|integer|min:100|max:100000',
            'time_period' => 'nullable|string|in:7d,30d,90d,1y',
            'include_predictions' => 'boolean',
            'export_format' => 'nullable|string|in:json,csv,pdf'
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
            $analysisType = $request->analysis_type;
            $timePeriod = $request->time_period ?? '30d';

            // Advanced audience segmentation
            $audienceSegments = $this->performAdvancedAudienceSegmentation($account, $analysisType, $timePeriod);
            
            // Behavioral pattern analysis
            $behaviorPatterns = $this->analyzeBehaviorPatterns($account, $timePeriod);
            
            // Interest and affinity analysis
            $interestAnalysis = $this->analyzeAudienceInterests($account, $timePeriod);
            
            // Engagement pattern analysis
            $engagementPatterns = $this->analyzeEngagementPatterns($account, $timePeriod);
            
            // Lookalike audience identification
            $lookalikeSuggestions = $this->identifyLookalikeAudiences($account, $audienceSegments);
            
            // Audience growth predictions
            $growthPredictions = [];
            if ($request->include_predictions) {
                $growthPredictions = $this->predictAudienceGrowth($account, $audienceSegments, $behaviorPatterns);
            }

            // Content preferences analysis
            $contentPreferences = $this->analyzeContentPreferences($account, $audienceSegments);
            
            // Influencer alignment analysis
            $influencerAlignment = $this->analyzeInfluencerAlignment($account, $audienceSegments);

            return response()->json([
                'success' => true,
                'data' => [
                    'audience_overview' => [
                        'total_audience_size' => $account->followers_count,
                        'active_audience_percentage' => $this->calculateActiveAudiencePercentage($account),
                        'audience_quality_score' => $this->calculateAudienceQualityScore($audienceSegments),
                        'engagement_quality' => $this->calculateEngagementQuality($engagementPatterns),
                        'growth_rate' => $this->calculateGrowthRate($account, $timePeriod)
                    ],
                    'audience_segments' => $audienceSegments,
                    'behavioral_patterns' => $behaviorPatterns,
                    'interest_analysis' => $interestAnalysis,
                    'engagement_patterns' => $engagementPatterns,
                    'content_preferences' => $contentPreferences,
                    'lookalike_suggestions' => $lookalikeSuggestions,
                    'influencer_alignment' => $influencerAlignment,
                    'growth_predictions' => $growthPredictions,
                    'actionable_insights' => $this->generateAudienceInsights($audienceSegments, $behaviorPatterns, $interestAnalysis),
                    'targeting_recommendations' => $this->generateTargetingRecommendations($audienceSegments, $contentPreferences)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Advanced audience intelligence failed', ['error' => $e->getMessage(), 'user_id' => auth()->id()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to analyze audience intelligence: ' . $e->getMessage()
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

    /**
     * Get AI-powered content suggestions for Instagram
     */
    public function getContentSuggestions(Request $request)
    {
        try {
            $user = $request->user();
            
            // Get connected Instagram account
            $account = SocialMediaAccount::where('user_id', $user->id)
                ->where('platform', 'instagram')
                ->where('is_active', true)
                ->first();

            if (!$account) {
                return response()->json([
                    'success' => false,
                    'message' => 'No Instagram account connected'
                ], 400);
            }

            // Mock content suggestions (in production, this would use AI)
            $suggestions = [
                [
                    'type' => 'post',
                    'content' => 'Share behind-the-scenes content from your workspace',
                    'hashtags' => ['#behindthescenes', '#workspace', '#productivity'],
                    'best_time' => '18:00',
                    'engagement_prediction' => 'High',
                    'confidence' => 85
                ],
                [
                    'type' => 'story',
                    'content' => 'Quick tip or tutorial related to your niche',
                    'hashtags' => ['#tips', '#tutorial', '#learn'],
                    'best_time' => '12:00',
                    'engagement_prediction' => 'Medium',
                    'confidence' => 72
                ],
                [
                    'type' => 'reel',
                    'content' => 'Trending audio with your product/service showcase',
                    'hashtags' => ['#trending', '#reel', '#viral'],
                    'best_time' => '20:00',
                    'engagement_prediction' => 'Very High',
                    'confidence' => 92
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'suggestions' => $suggestions,
                    'generated_at' => now(),
                    'account' => $account->username
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Instagram content suggestions error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get content suggestions'
            ], 500);
        }
    }
}