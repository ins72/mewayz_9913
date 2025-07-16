<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InstagramAccount;
use App\Models\InstagramPost;
use App\Models\InstagramHashtag;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class InstagramManagementController extends Controller
{
    /**
     * Get Instagram accounts for workspace
     */
    public function getAccounts(Request $request)
    {
        try {
            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }
            
            $accounts = InstagramAccount::where('workspace_id', $workspace->id)
                ->orderBy('is_active', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'accounts' => $accounts->map(function($account) {
                    return [
                        'id' => $account->id,
                        'username' => $account->username,
                        'display_name' => $account->display_name,
                        'profile_picture_url' => $account->profile_picture_url,
                        'bio' => $account->bio,
                        'followers_count' => $account->followers_count,
                        'following_count' => $account->following_count,
                        'media_count' => $account->media_count,
                        'is_active' => $account->is_active,
                        'is_verified' => $account->is_verified,
                        'account_type' => $account->account_type,
                        'engagement_rate' => $account->getEngagementRate(),
                        'formatted_followers' => $account->getFormattedFollowersCount(),
                        'token_expired' => $account->isTokenExpired()
                    ];
                })
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching Instagram accounts', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch Instagram accounts'
            ], 500);
        }
    }
    
    /**
     * Add Instagram account
     */
    public function addAccount(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required|string|max:100',
                'display_name' => 'required|string|max:255',
                'profile_picture_url' => 'nullable|url',
                'bio' => 'nullable|string|max:500',
                'is_active' => 'boolean'
            ]);
            
            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }
            
            // Check if account already exists
            $existingAccount = InstagramAccount::where('workspace_id', $workspace->id)
                ->where('username', $request->username)
                ->first();
            
            if ($existingAccount) {
                return response()->json([
                    'error' => 'Instagram account already exists'
                ], 400);
            }
            
            $account = InstagramAccount::create([
                'workspace_id' => $workspace->id,
                'user_id' => $user->id,
                'instagram_id' => 'demo_' . $request->username, // Demo ID
                'username' => $request->username,
                'display_name' => $request->display_name,
                'profile_picture_url' => $request->profile_picture_url,
                'bio' => $request->bio,
                'is_active' => $request->is_active ?? true,
                'account_type' => 'personal', // Default to personal
                'followers_count' => 0,
                'following_count' => 0,
                'media_count' => 0
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Instagram account added successfully',
                'account' => [
                    'id' => $account->id,
                    'username' => $account->username,
                    'display_name' => $account->display_name,
                    'profile_picture_url' => $account->profile_picture_url,
                    'bio' => $account->bio,
                    'is_active' => $account->is_active,
                    'account_type' => $account->account_type
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error adding Instagram account', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to add Instagram account'
            ], 500);
        }
    }
    
    /**
     * Get Instagram posts
     */
    public function getPosts(Request $request)
    {
        try {
            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }
            
            $query = InstagramPost::where('workspace_id', $workspace->id);
            
            // Filter by status
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }
            
            // Filter by post type
            if ($request->has('post_type') && $request->post_type) {
                $query->where('post_type', $request->post_type);
            }
            
            // Filter by date range
            if ($request->has('from_date') && $request->from_date) {
                $query->whereDate('created_at', '>=', $request->from_date);
            }
            
            if ($request->has('to_date') && $request->to_date) {
                $query->whereDate('created_at', '<=', $request->to_date);
            }
            
            $posts = $query->orderBy('created_at', 'desc')
                ->paginate(20);
            
            return response()->json([
                'success' => true,
                'posts' => $posts->items(),
                'pagination' => [
                    'current_page' => $posts->currentPage(),
                    'total_pages' => $posts->lastPage(),
                    'per_page' => $posts->perPage(),
                    'total' => $posts->total()
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching Instagram posts', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch Instagram posts'
            ], 500);
        }
    }
    
    /**
     * Create Instagram post
     */
    public function createPost(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'caption' => 'required|string|max:2200',
                'media_urls' => 'required|array|min:1|max:10',
                'media_urls.*' => 'url',
                'hashtags' => 'nullable|array|max:30',
                'hashtags.*' => 'string|max:100',
                'post_type' => 'required|string|in:feed,story,reel',
                'scheduled_at' => 'nullable|date|after:now'
            ]);
            
            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }
            
            // Determine post status
            $status = 'draft';
            if ($request->scheduled_at) {
                $status = 'scheduled';
            }
            
            // Process hashtags
            $hashtags = $request->hashtags ?? [];
            $processedHashtags = array_map(function($tag) {
                return str_starts_with($tag, '#') ? $tag : '#' . $tag;
            }, $hashtags);
            
            // Get the first Instagram account for this workspace (or create a default one)
            $instagramAccount = InstagramAccount::where('workspace_id', $workspace->id)->first();
            if (!$instagramAccount) {
                $instagramAccount = InstagramAccount::create([
                    'workspace_id' => $workspace->id,
                    'user_id' => $user->id,
                    'instagram_id' => 'default_account',
                    'username' => 'default_account',
                    'display_name' => 'Default Account',
                    'is_active' => true,
                    'account_type' => 'personal',
                    'followers_count' => 0,
                    'following_count' => 0,
                    'media_count' => 0
                ]);
            }

            $post = InstagramPost::create([
                'workspace_id' => $workspace->id,
                'user_id' => $user->id,
                'instagram_account_id' => $instagramAccount->id,
                'caption' => $request->caption,
                'media_urls' => $request->media_urls,
                'hashtags' => $processedHashtags,
                'post_type' => $request->post_type,
                'status' => $status,
                'scheduled_at' => $request->scheduled_at ? Carbon::parse($request->scheduled_at) : null
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Instagram post created successfully',
                'post' => [
                    'id' => $post->id,
                    'title' => $post->title,
                    'caption' => $post->caption,
                    'media_urls' => $post->media_urls,
                    'hashtags' => $post->hashtags,
                    'post_type' => $post->post_type,
                    'status' => $post->status,
                    'scheduled_at' => $post->scheduled_at,
                    'created_at' => $post->created_at
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error creating Instagram post', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to create Instagram post'
            ], 500);
        }
    }
    
    /**
     * Update Instagram post
     */
    public function updatePost(Request $request, $postId)
    {
        try {
            $request->validate([
                'title' => 'string|max:255',
                'caption' => 'string|max:2200',
                'media_urls' => 'array|min:1|max:10',
                'media_urls.*' => 'url',
                'hashtags' => 'nullable|array|max:30',
                'hashtags.*' => 'string|max:100',
                'post_type' => 'string|in:feed,story,reel',
                'scheduled_at' => 'nullable|date|after:now'
            ]);
            
            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }
            
            $post = InstagramPost::where('workspace_id', $workspace->id)
                ->where('id', $postId)
                ->first();
            
            if (!$post) {
                return response()->json(['error' => 'Post not found'], 404);
            }
            
            // Don't allow editing published posts
            if ($post->status === 'published') {
                return response()->json([
                    'error' => 'Cannot edit published posts'
                ], 400);
            }
            
            // Update only provided fields
            $updateData = [];
            if ($request->has('title')) $updateData['title'] = $request->title;
            if ($request->has('caption')) $updateData['caption'] = $request->caption;
            if ($request->has('media_urls')) $updateData['media_urls'] = $request->media_urls;
            if ($request->has('post_type')) $updateData['post_type'] = $request->post_type;
            
            if ($request->has('hashtags')) {
                $hashtags = $request->hashtags ?? [];
                $updateData['hashtags'] = array_map(function($tag) {
                    return str_starts_with($tag, '#') ? $tag : '#' . $tag;
                }, $hashtags);
            }
            
            if ($request->has('scheduled_at')) {
                $updateData['scheduled_at'] = $request->scheduled_at ? Carbon::parse($request->scheduled_at) : null;
                $updateData['status'] = $request->scheduled_at ? 'scheduled' : 'draft';
            }
            
            $post->update($updateData);
            
            return response()->json([
                'success' => true,
                'message' => 'Instagram post updated successfully',
                'post' => $post->fresh()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error updating Instagram post', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'post_id' => $postId
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to update Instagram post'
            ], 500);
        }
    }
    
    /**
     * Delete Instagram post
     */
    public function deletePost($postId)
    {
        try {
            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }
            
            $post = InstagramPost::where('workspace_id', $workspace->id)
                ->where('id', $postId)
                ->first();
            
            if (!$post) {
                return response()->json(['error' => 'Post not found'], 404);
            }
            
            $post->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Instagram post deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error deleting Instagram post', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'post_id' => $postId
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to delete Instagram post'
            ], 500);
        }
    }
    
    /**
     * Get hashtag research
     */
    public function getHashtagResearch(Request $request)
    {
        try {
            $request->validate([
                'keyword' => 'required|string|max:100',
                'limit' => 'integer|min:1|max:100'
            ]);
            
            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }
            
            $keyword = $request->keyword;
            $limit = $request->limit ?? 20;
            
            // Get hashtags from database
            $hashtags = InstagramHashtag::where('workspace_id', $workspace->id)
                ->where('hashtag', 'like', '%' . $keyword . '%')
                ->orderBy('posts_count', 'desc')
                ->limit($limit)
                ->get();
            
            // If no hashtags found, generate sample hashtags
            if ($hashtags->isEmpty()) {
                $sampleHashtags = $this->generateSampleHashtags($keyword, $workspace->id);
                $hashtags = collect($sampleHashtags);
            }
            
            return response()->json([
                'success' => true,
                'hashtags' => $hashtags->map(function($hashtag) {
                    // Handle both model instances and sample objects
                    $formattedCount = method_exists($hashtag, 'getFormattedPostCount') 
                        ? $hashtag->getFormattedPostCount() 
                        : $this->formatPostCount($hashtag->post_count);
                    
                    $difficultyColor = method_exists($hashtag, 'getDifficultyColor') 
                        ? $hashtag->getDifficultyColor() 
                        : $this->getDifficultyColor($hashtag->difficulty);
                    
                    return [
                        'id' => $hashtag->id ?? null,
                        'hashtag' => $hashtag->hashtag,
                        'post_count' => $hashtag->post_count,
                        'formatted_count' => $formattedCount,
                        'engagement_rate' => $hashtag->engagement_rate,
                        'difficulty' => $hashtag->difficulty,
                        'difficulty_color' => $difficultyColor,
                        'is_trending' => $hashtag->is_trending,
                        'related_hashtags' => $hashtag->related_hashtags ?? []
                    ];
                })
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching hashtag research', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch hashtag research'
            ], 500);
        }
    }
    
    /**
     * Get Instagram analytics
     */
    public function getAnalytics(Request $request)
    {
        try {
            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }
            
            $dateRange = $request->date_range ?? '30'; // days
            $startDate = Carbon::now()->subDays($dateRange);
            
            // Get posts in date range
            $posts = InstagramPost::where('workspace_id', $workspace->id)
                ->where('status', 'published')
                ->where('published_at', '>=', $startDate)
                ->get();
            
            // Get accounts
            $accounts = InstagramAccount::where('workspace_id', $workspace->id)->get();
            
            // Calculate metrics
            $totalPosts = $posts->count();
            $totalEngagement = $posts->sum(function($post) {
                $analytics = $post->analytics ?? [];
                return ($analytics['likes_count'] ?? 0) + ($analytics['comments_count'] ?? 0);
            });
            
            $averageEngagement = $totalPosts > 0 ? $totalEngagement / $totalPosts : 0;
            
            $followersCount = $accounts->sum('followers_count');
            $engagementRate = $followersCount > 0 ? ($totalEngagement / $followersCount) * 100 : 0;
            
            // Top performing posts
            $topPosts = $posts->sortByDesc(function($post) {
                return $post->getEngagementRate();
            })->take(5)->values();
            
            // Top hashtags
            $hashtagPerformance = [];
            foreach ($posts as $post) {
                foreach ($post->hashtags ?? [] as $hashtag) {
                    if (!isset($hashtagPerformance[$hashtag])) {
                        $hashtagPerformance[$hashtag] = ['count' => 0, 'engagement' => 0];
                    }
                    $hashtagPerformance[$hashtag]['count']++;
                    $hashtagPerformance[$hashtag]['engagement'] += $post->getEngagementRate();
                }
            }
            
            $topHashtags = collect($hashtagPerformance)
                ->map(function($data, $hashtag) {
                    return [
                        'hashtag' => $hashtag,
                        'usage_count' => $data['count'],
                        'average_engagement' => $data['count'] > 0 ? $data['engagement'] / $data['count'] : 0
                    ];
                })
                ->sortByDesc('average_engagement')
                ->take(10)
                ->values();
            
            return response()->json([
                'success' => true,
                'analytics' => [
                    'overview' => [
                        'total_posts' => $totalPosts,
                        'total_followers' => $followersCount,
                        'total_engagement' => $totalEngagement,
                        'average_engagement' => round($averageEngagement, 2),
                        'engagement_rate' => round($engagementRate, 2),
                        'accounts_count' => $accounts->count()
                    ],
                    'top_posts' => $topPosts->map(function($post) {
                        return [
                            'id' => $post->id,
                            'title' => $post->title,
                            'engagement_rate' => $post->getEngagementRate(),
                            'published_at' => $post->published_at,
                            'post_type' => $post->post_type
                        ];
                    }),
                    'top_hashtags' => $topHashtags,
                    'date_range' => $dateRange,
                    'period' => [
                        'start_date' => $startDate->format('Y-m-d'),
                        'end_date' => Carbon::now()->format('Y-m-d')
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching Instagram analytics', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch Instagram analytics'
            ], 500);
        }
    }
    
    /**
     * Generate sample hashtags for demo
     */
    private function generateSampleHashtags($keyword, $workspaceId)
    {
        $sampleHashtags = [
            ['hashtag' => "#{$keyword}", 'post_count' => rand(100000, 5000000), 'engagement_rate' => rand(50, 300) / 100, 'difficulty' => 'medium', 'is_trending' => rand(0, 1)],
            ['hashtag' => "#{$keyword}life", 'post_count' => rand(50000, 1000000), 'engagement_rate' => rand(100, 400) / 100, 'difficulty' => 'easy', 'is_trending' => rand(0, 1)],
            ['hashtag' => "#{$keyword}love", 'post_count' => rand(200000, 2000000), 'engagement_rate' => rand(80, 250) / 100, 'difficulty' => 'medium', 'is_trending' => rand(0, 1)],
            ['hashtag' => "#{$keyword}daily", 'post_count' => rand(30000, 500000), 'engagement_rate' => rand(150, 350) / 100, 'difficulty' => 'easy', 'is_trending' => rand(0, 1)],
            ['hashtag' => "#{$keyword}inspiration", 'post_count' => rand(100000, 800000), 'engagement_rate' => rand(120, 280) / 100, 'difficulty' => 'medium', 'is_trending' => rand(0, 1)],
            ['hashtag' => "#{$keyword}tips", 'post_count' => rand(80000, 600000), 'engagement_rate' => rand(100, 300) / 100, 'difficulty' => 'easy', 'is_trending' => rand(0, 1)],
            ['hashtag' => "#{$keyword}community", 'post_count' => rand(150000, 1200000), 'engagement_rate' => rand(90, 220) / 100, 'difficulty' => 'medium', 'is_trending' => rand(0, 1)],
            ['hashtag' => "#{$keyword}goals", 'post_count' => rand(40000, 400000), 'engagement_rate' => rand(140, 320) / 100, 'difficulty' => 'easy', 'is_trending' => rand(0, 1)],
        ];
        
        return array_map(function($hashtag) use ($workspaceId) {
            return (object) array_merge($hashtag, [
                'id' => null,
                'workspace_id' => $workspaceId,
                'related_hashtags' => []
            ]);
        }, $sampleHashtags);
    }
    
    /**
     * Format post count for display
     */
    private function formatPostCount($count)
    {
        if ($count >= 1000000) {
            return round($count / 1000000, 1) . 'M';
        } elseif ($count >= 1000) {
            return round($count / 1000, 1) . 'K';
        }
        return $count;
    }
    
    /**
     * Get difficulty color
     */
    private function getDifficultyColor($difficulty)
    {
        return match($difficulty) {
            'easy' => '#28a745',
            'medium' => '#ffc107',
            'hard' => '#dc3545',
            default => '#6c757d'
        };
    }
}
