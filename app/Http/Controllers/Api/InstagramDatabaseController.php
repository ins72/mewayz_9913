<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InstagramProfile;
use App\Models\InstagramPost;
use App\Models\InstagramAnalytics;
use App\Models\InstagramHashtag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class InstagramDatabaseController extends Controller
{
    /**
     * Search Instagram profiles with advanced filtering
     */
    public function searchProfiles(Request $request)
    {
        try {
            $request->validate([
                'query' => 'string|max:255',
                'min_followers' => 'integer|min:0',
                'max_followers' => 'integer|min:0',
                'min_following' => 'integer|min:0',
                'max_following' => 'integer|min:0',
                'min_engagement_rate' => 'numeric|min:0|max:100',
                'max_engagement_rate' => 'numeric|min:0|max:100',
                'location' => 'string|max:255',
                'category' => 'string|max:100',
                'is_business_account' => 'boolean',
                'is_verified' => 'boolean',
                'hashtags' => 'array',
                'bio_keywords' => 'array',
                'language' => 'string|max:10',
                'sort_by' => 'string|in:followers,following,engagement_rate,posts,recent_activity',
                'sort_order' => 'string|in:asc,desc',
                'per_page' => 'integer|min:1|max:100'
            ]);

            $query = InstagramProfile::query();

            // Text search
            if ($request->input('query')) {
                $query->where(function ($q) use ($request) {
                    $q->where('username', 'like', '%' . $request->input('query') . '%')
                      ->orWhere('display_name', 'like', '%' . $request->input('query') . '%')
                      ->orWhere('bio', 'like', '%' . $request->input('query') . '%');
                });
            }

            // Follower count filters
            if ($request->input('min_followers')) {
                $query->where('follower_count', '>=', $request->input('min_followers'));
            }
            if ($request->input('max_followers')) {
                $query->where('follower_count', '<=', $request->input('max_followers'));
            }

            // Following count filters
            if ($request->input('min_following')) {
                $query->where('following_count', '>=', $request->input('min_following'));
            }
            if ($request->input('max_following')) {
                $query->where('following_count', '<=', $request->input('max_following'));
            }

            // Engagement rate filters
            if ($request->input('min_engagement_rate')) {
                $query->where('engagement_rate', '>=', $request->input('min_engagement_rate'));
            }
            if ($request->input('max_engagement_rate')) {
                $query->where('engagement_rate', '<=', $request->input('max_engagement_rate'));
            }

            // Location filter
            if ($request->input('location')) {
                $query->where('location', 'like', '%' . $request->input('location') . '%');
            }

            // Category filter
            if ($request->input('category')) {
                $query->where('category', $request->input('category'));
            }

            // Account type filters
            if ($request->has('is_business_account')) {
                $query->where('is_business_account', $request->input('is_business_account'));
            }
            if ($request->has('is_verified')) {
                $query->where('is_verified', $request->input('is_verified'));
            }

            // Hashtag filter
            if ($request->input('hashtags') && is_array($request->input('hashtags'))) {
                $query->whereHas('hashtags', function ($q) use ($request) {
                    $q->whereIn('hashtag', $request->input('hashtags'));
                });
            }

            // Bio keywords filter
            if ($request->input('bio_keywords') && is_array($request->input('bio_keywords'))) {
                $query->where(function ($q) use ($request) {
                    foreach ($request->input('bio_keywords') as $keyword) {
                        $q->orWhere('bio', 'like', '%' . $keyword . '%');
                    }
                });
            }

            // Language filter
            if ($request->input('language')) {
                $query->where('language', $request->input('language'));
            }

            // Sorting
            $sortBy = $request->input('sort_by', 'follower_count');
            $sortOrder = $request->input('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Add secondary sort for consistency
            if ($sortBy !== 'username') {
                $query->orderBy('username', 'asc');
            }

            // Pagination
            $perPage = $request->input('per_page', 20);
            $profiles = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $profiles,
                'filters_applied' => $request->all(),
                'total_profiles' => $profiles->total()
            ]);

        } catch (\Exception $e) {
            Log::error('Instagram profile search failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to search profiles'
            ], 500);
        }
    }

    /**
     * Get detailed profile information
     */
    public function getProfile(Request $request, $id)
    {
        try {
            $profile = InstagramProfile::with(['posts', 'hashtags', 'analytics'])
                                     ->findOrFail($id);

            // Get recent posts
            $recentPosts = $profile->posts()
                                 ->orderBy('created_at', 'desc')
                                 ->limit(12)
                                 ->get();

            // Get engagement metrics
            $engagementMetrics = $this->calculateEngagementMetrics($profile);

            // Get hashtag analysis
            $hashtagAnalysis = $this->getHashtagAnalysis($profile);

            // Get audience insights
            $audienceInsights = $this->getAudienceInsights($profile);

            return response()->json([
                'success' => true,
                'data' => [
                    'profile' => $profile,
                    'recent_posts' => $recentPosts,
                    'engagement_metrics' => $engagementMetrics,
                    'hashtag_analysis' => $hashtagAnalysis,
                    'audience_insights' => $audienceInsights
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Instagram profile retrieval failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve profile'
            ], 500);
        }
    }

    /**
     * Scrape and update Instagram profile data
     */
    public function scrapeProfile(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required|string|max:255',
                'force_update' => 'boolean'
            ]);

            $username = $request->username;
            $forceUpdate = $request->force_update ?? false;

            // Check if profile exists and is recently updated
            $profile = InstagramProfile::where('username', $username)->first();
            
            if ($profile && !$forceUpdate && $profile->last_scraped && $profile->last_scraped->diffInHours(now()) < 24) {
                return response()->json([
                    'success' => true,
                    'message' => 'Profile data is up to date',
                    'data' => $profile
                ]);
            }

            // Perform scraping
            $scrapedData = $this->performInstagramScraping($username);
            
            if (!$scrapedData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to scrape profile data'
                ], 400);
            }

            // Update or create profile
            $profile = InstagramProfile::updateOrCreate(
                ['username' => $username],
                [
                    'display_name' => $scrapedData['display_name'] ?? '',
                    'bio' => $scrapedData['bio'] ?? '',
                    'follower_count' => $scrapedData['follower_count'] ?? 0,
                    'following_count' => $scrapedData['following_count'] ?? 0,
                    'post_count' => $scrapedData['post_count'] ?? 0,
                    'engagement_rate' => $scrapedData['engagement_rate'] ?? 0,
                    'location' => $scrapedData['location'] ?? '',
                    'category' => $scrapedData['category'] ?? '',
                    'email' => $scrapedData['email'] ?? '',
                    'phone' => $scrapedData['phone'] ?? '',
                    'website' => $scrapedData['website'] ?? '',
                    'profile_image_url' => $scrapedData['profile_image_url'] ?? '',
                    'is_business_account' => $scrapedData['is_business_account'] ?? false,
                    'is_verified' => $scrapedData['is_verified'] ?? false,
                    'language' => $scrapedData['language'] ?? 'en',
                    'last_scraped' => now(),
                    'workspace_id' => $request->user()->workspaces()->first()->id ?? null
                ]
            );

            // Update hashtags
            if (isset($scrapedData['hashtags'])) {
                $this->updateProfileHashtags($profile, $scrapedData['hashtags']);
            }

            // Update posts
            if (isset($scrapedData['posts'])) {
                $this->updateProfilePosts($profile, $scrapedData['posts']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Profile scraped successfully',
                'data' => $profile
            ]);

        } catch (\Exception $e) {
            Log::error('Instagram profile scraping failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to scrape profile'
            ], 500);
        }
    }

    /**
     * Export filtered profiles to CSV/Excel
     */
    public function exportProfiles(Request $request)
    {
        try {
            $request->validate([
                'format' => 'required|in:csv,excel',
                'fields' => 'required|array',
                'filters' => 'array'
            ]);

            // Apply the same filters as search
            $query = InstagramProfile::query();
            
            if ($request->filters) {
                $query = $this->applyFilters($query, $request->filters);
            }

            $profiles = $query->get();

            // Select requested fields
            $selectedFields = $request->fields;
            $exportData = $profiles->map(function ($profile) use ($selectedFields) {
                $data = [];
                foreach ($selectedFields as $field) {
                    $data[$field] = $profile->$field;
                }
                return $data;
            });

            // Generate export file
            $filename = 'instagram_profiles_' . date('Y-m-d_H-i-s') . '.' . $request->format;
            $exportFile = $this->generateExportFile($exportData, $request->format, $filename);

            return response()->json([
                'success' => true,
                'message' => 'Export generated successfully',
                'data' => [
                    'download_url' => $exportFile['url'],
                    'filename' => $filename,
                    'record_count' => $profiles->count()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Instagram profile export failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to export profiles'
            ], 500);
        }
    }

    /**
     * Bulk import profiles from CSV
     */
    public function importProfiles(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:csv,txt',
                'mapping' => 'required|array'
            ]);

            $file = $request->file('file');
            $mapping = $request->mapping;

            $csvData = array_map('str_getcsv', file($file->path()));
            $header = array_shift($csvData);

            $imported = 0;
            $errors = [];

            foreach ($csvData as $row) {
                try {
                    $data = array_combine($header, $row);
                    $profileData = [];

                    foreach ($mapping as $csvField => $dbField) {
                        if (isset($data[$csvField])) {
                            $profileData[$dbField] = $data[$csvField];
                        }
                    }

                    InstagramProfile::updateOrCreate(
                        ['username' => $profileData['username']],
                        array_merge($profileData, [
                            'workspace_id' => $request->user()->workspaces()->first()->id ?? null,
                            'last_scraped' => now()
                        ])
                    );

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Row " . ($imported + 1) . ": " . $e->getMessage();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Profiles imported successfully',
                'data' => [
                    'imported_count' => $imported,
                    'errors' => $errors
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Instagram profile import failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to import profiles'
            ], 500);
        }
    }

    /**
     * Get analytics for Instagram database
     */
    public function getAnalytics(Request $request)
    {
        try {
            // Get user's first workspace
            $workspace = $request->user()->workspaces()->first();
            $workspaceId = $workspace ? $workspace->id : null;
            
            $analytics = [
                'total_profiles' => InstagramProfile::where('workspace_id', $workspaceId)->count(),
                'verified_profiles' => InstagramProfile::where('workspace_id', $workspaceId)
                                                    ->where('is_verified', true)->count(),
                'business_accounts' => InstagramProfile::where('workspace_id', $workspaceId)
                                                     ->where('is_business_account', true)->count(),
                'avg_follower_count' => InstagramProfile::where('workspace_id', $workspaceId)
                                                       ->avg('follower_count'),
                'avg_engagement_rate' => InstagramProfile::where('workspace_id', $workspaceId)
                                                        ->avg('engagement_rate'),
                'top_categories' => InstagramProfile::where('workspace_id', $workspaceId)
                                                  ->groupBy('category')
                                                  ->selectRaw('category, COUNT(*) as count')
                                                  ->orderBy('count', 'desc')
                                                  ->limit(10)
                                                  ->get(),
                'top_locations' => InstagramProfile::where('workspace_id', $workspaceId)
                                                 ->whereNotNull('location')
                                                 ->groupBy('location')
                                                 ->selectRaw('location, COUNT(*) as count')
                                                 ->orderBy('count', 'desc')
                                                 ->limit(10)
                                                 ->get(),
                'follower_distribution' => $this->getFollowerDistribution($workspaceId),
                'recent_activity' => $this->getRecentActivity($workspaceId)
            ];

            return response()->json([
                'success' => true,
                'data' => $analytics
            ]);

        } catch (\Exception $e) {
            Log::error('Instagram analytics failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to get analytics'
            ], 500);
        }
    }

    /**
     * Private helper methods
     */
    private function performInstagramScraping($username)
    {
        // This is a placeholder for actual Instagram scraping logic
        // In production, you would use Instagram's official API or approved scraping methods
        // For now, returning mock data
        
        return [
            'display_name' => 'Mock User',
            'bio' => 'Mock bio for ' . $username,
            'follower_count' => rand(1000, 100000),
            'following_count' => rand(100, 5000),
            'post_count' => rand(50, 1000),
            'engagement_rate' => rand(100, 800) / 100,
            'location' => 'Los Angeles, CA',
            'category' => 'Personal',
            'email' => $username . '@example.com',
            'website' => 'https://example.com',
            'profile_image_url' => 'https://via.placeholder.com/150',
            'is_business_account' => rand(0, 1),
            'is_verified' => rand(0, 1) > 0.9,
            'language' => 'en',
            'hashtags' => ['#lifestyle', '#travel', '#photography'],
            'posts' => []
        ];
    }

    private function updateProfileHashtags($profile, $hashtags)
    {
        // Clear existing hashtags
        $profile->hashtags()->delete();

        // Add new hashtags
        foreach ($hashtags as $hashtag) {
            InstagramHashtag::create([
                'profile_id' => $profile->id,
                'hashtag' => $hashtag,
                'usage_count' => 1
            ]);
        }
    }

    private function updateProfilePosts($profile, $posts)
    {
        // Implementation for updating posts would go here
        // This would involve creating InstagramPost records
    }

    private function calculateEngagementMetrics($profile)
    {
        return [
            'average_likes' => rand(100, 1000),
            'average_comments' => rand(10, 100),
            'engagement_rate' => $profile->engagement_rate,
            'best_performing_post' => 'Mock post data',
            'engagement_trend' => 'increasing'
        ];
    }

    private function getHashtagAnalysis($profile)
    {
        return [
            'most_used_hashtags' => $profile->hashtags()->orderBy('usage_count', 'desc')->limit(10)->get(),
            'trending_hashtags' => ['#trending1', '#trending2', '#trending3'],
            'hashtag_performance' => 'Mock hashtag performance data'
        ];
    }

    private function getAudienceInsights($profile)
    {
        return [
            'age_distribution' => [
                '18-24' => 30,
                '25-34' => 40,
                '35-44' => 20,
                '45+' => 10
            ],
            'gender_distribution' => [
                'female' => 60,
                'male' => 40
            ],
            'top_locations' => ['Los Angeles', 'New York', 'Miami'],
            'interests' => ['lifestyle', 'travel', 'fashion']
        ];
    }

    private function applyFilters($query, $filters)
    {
        // Apply filters to query based on filters array
        foreach ($filters as $key => $value) {
            switch ($key) {
                case 'min_followers':
                    $query->where('follower_count', '>=', $value);
                    break;
                case 'max_followers':
                    $query->where('follower_count', '<=', $value);
                    break;
                case 'category':
                    $query->where('category', $value);
                    break;
                case 'location':
                    $query->where('location', 'like', '%' . $value . '%');
                    break;
                // Add more filter cases as needed
            }
        }

        return $query;
    }

    private function generateExportFile($data, $format, $filename)
    {
        // Implementation for generating CSV/Excel files
        // This would typically use libraries like maatwebsite/excel
        
        return [
            'url' => '/exports/' . $filename,
            'path' => storage_path('app/exports/' . $filename)
        ];
    }

    private function getFollowerDistribution($workspaceId)
    {
        return [
            '0-1K' => InstagramProfile::where('workspace_id', $workspaceId)
                                    ->where('follower_count', '<', 1000)->count(),
            '1K-10K' => InstagramProfile::where('workspace_id', $workspaceId)
                                      ->whereBetween('follower_count', [1000, 10000])->count(),
            '10K-100K' => InstagramProfile::where('workspace_id', $workspaceId)
                                        ->whereBetween('follower_count', [10000, 100000])->count(),
            '100K+' => InstagramProfile::where('workspace_id', $workspaceId)
                                     ->where('follower_count', '>', 100000)->count()
        ];
    }

    private function getRecentActivity($workspaceId)
    {
        return InstagramProfile::where('workspace_id', $workspaceId)
                              ->orderBy('last_scraped', 'desc')
                              ->limit(10)
                              ->get(['username', 'last_scraped', 'follower_count']);
    }
}