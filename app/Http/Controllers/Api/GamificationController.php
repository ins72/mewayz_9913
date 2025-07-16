<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GamificationController extends Controller
{
    /**
     * Get user achievements
     */
    public function getAchievements()
    {
        try {
            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }

            $achievements = [
                [
                    'id' => 1,
                    'name' => 'First Post',
                    'description' => 'Create your first Instagram post',
                    'category' => 'instagram',
                    'icon' => 'camera',
                    'points' => 100,
                    'rarity' => 'common',
                    'unlocked' => true,
                    'unlocked_at' => now()->subDays(25),
                    'progress' => 100,
                ],
                [
                    'id' => 2,
                    'name' => 'Content Creator',
                    'description' => 'Create 10 Instagram posts',
                    'category' => 'instagram',
                    'icon' => 'edit',
                    'points' => 500,
                    'rarity' => 'uncommon',
                    'unlocked' => true,
                    'unlocked_at' => now()->subDays(15),
                    'progress' => 100,
                ],
                [
                    'id' => 3,
                    'name' => 'Hashtag Master',
                    'description' => 'Research 100 hashtags',
                    'category' => 'instagram',
                    'icon' => 'hash',
                    'points' => 750,
                    'rarity' => 'rare',
                    'unlocked' => true,
                    'unlocked_at' => now()->subDays(8),
                    'progress' => 100,
                ],
                [
                    'id' => 4,
                    'name' => 'Email Marketer',
                    'description' => 'Send your first email campaign',
                    'category' => 'email',
                    'icon' => 'mail',
                    'points' => 200,
                    'rarity' => 'common',
                    'unlocked' => true,
                    'unlocked_at' => now()->subDays(20),
                    'progress' => 100,
                ],
                [
                    'id' => 5,
                    'name' => 'Campaign Expert',
                    'description' => 'Send 50 email campaigns',
                    'category' => 'email',
                    'icon' => 'send',
                    'points' => 1000,
                    'rarity' => 'rare',
                    'unlocked' => false,
                    'unlocked_at' => null,
                    'progress' => 68,
                ],
                [
                    'id' => 6,
                    'name' => 'Team Builder',
                    'description' => 'Invite 5 team members',
                    'category' => 'team',
                    'icon' => 'users',
                    'points' => 600,
                    'rarity' => 'uncommon',
                    'unlocked' => false,
                    'unlocked_at' => null,
                    'progress' => 40,
                ],
                [
                    'id' => 7,
                    'name' => 'Analytics Ninja',
                    'description' => 'View analytics 100 times',
                    'category' => 'analytics',
                    'icon' => 'bar-chart',
                    'points' => 800,
                    'rarity' => 'rare',
                    'unlocked' => false,
                    'unlocked_at' => null,
                    'progress' => 23,
                ],
                [
                    'id' => 8,
                    'name' => 'Revenue Generator',
                    'description' => 'Generate $1000 in revenue',
                    'category' => 'business',
                    'icon' => 'dollar-sign',
                    'points' => 2000,
                    'rarity' => 'legendary',
                    'unlocked' => false,
                    'unlocked_at' => null,
                    'progress' => 45,
                ],
                [
                    'id' => 9,
                    'name' => 'Course Creator',
                    'description' => 'Create your first course',
                    'category' => 'courses',
                    'icon' => 'book',
                    'points' => 1500,
                    'rarity' => 'epic',
                    'unlocked' => false,
                    'unlocked_at' => null,
                    'progress' => 0,
                ],
                [
                    'id' => 10,
                    'name' => 'Social Media Master',
                    'description' => 'Complete all social media achievements',
                    'category' => 'meta',
                    'icon' => 'trophy',
                    'points' => 5000,
                    'rarity' => 'legendary',
                    'unlocked' => false,
                    'unlocked_at' => null,
                    'progress' => 75,
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $achievements,
                'summary' => [
                    'total_achievements' => count($achievements),
                    'unlocked_achievements' => count(array_filter($achievements, function($a) { return $a['unlocked']; })),
                    'total_points' => array_sum(array_column(array_filter($achievements, function($a) { return $a['unlocked']; }), 'points')),
                    'completion_rate' => round(count(array_filter($achievements, function($a) { return $a['unlocked']; })) / count($achievements) * 100, 1),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting achievements: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get achievements'], 500);
        }
    }

    /**
     * Get user progress
     */
    public function getProgress()
    {
        try {
            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }

            $progress = [
                'level' => 8,
                'current_xp' => 3450,
                'next_level_xp' => 4000,
                'total_xp' => 3450,
                'xp_to_next_level' => 550,
                'progress_percentage' => 86.25,
                'daily_streak' => 7,
                'longest_streak' => 15,
                'categories' => [
                    [
                        'category' => 'instagram',
                        'name' => 'Instagram Management',
                        'level' => 12,
                        'xp' => 1200,
                        'progress' => 75,
                        'icon' => 'camera',
                        'color' => '#E91E63',
                    ],
                    [
                        'category' => 'email',
                        'name' => 'Email Marketing',
                        'level' => 8,
                        'xp' => 800,
                        'progress' => 60,
                        'icon' => 'mail',
                        'color' => '#2196F3',
                    ],
                    [
                        'category' => 'analytics',
                        'name' => 'Analytics',
                        'level' => 5,
                        'xp' => 500,
                        'progress' => 40,
                        'icon' => 'bar-chart',
                        'color' => '#4CAF50',
                    ],
                    [
                        'category' => 'team',
                        'name' => 'Team Management',
                        'level' => 3,
                        'xp' => 300,
                        'progress' => 25,
                        'icon' => 'users',
                        'color' => '#FF9800',
                    ],
                    [
                        'category' => 'courses',
                        'name' => 'Course Creation',
                        'level' => 1,
                        'xp' => 100,
                        'progress' => 10,
                        'icon' => 'book',
                        'color' => '#9C27B0',
                    ],
                    [
                        'category' => 'business',
                        'name' => 'Business Growth',
                        'level' => 6,
                        'xp' => 550,
                        'progress' => 55,
                        'icon' => 'trending-up',
                        'color' => '#F44336',
                    ],
                ],
                'recent_activities' => [
                    [
                        'activity' => 'Created Instagram post',
                        'xp_gained' => 50,
                        'timestamp' => now()->subHours(2),
                        'category' => 'instagram',
                    ],
                    [
                        'activity' => 'Sent email campaign',
                        'xp_gained' => 100,
                        'timestamp' => now()->subHours(5),
                        'category' => 'email',
                    ],
                    [
                        'activity' => 'Viewed analytics dashboard',
                        'xp_gained' => 25,
                        'timestamp' => now()->subHours(8),
                        'category' => 'analytics',
                    ],
                    [
                        'activity' => 'Invited team member',
                        'xp_gained' => 75,
                        'timestamp' => now()->subDays(1),
                        'category' => 'team',
                    ],
                    [
                        'activity' => 'Completed workspace setup',
                        'xp_gained' => 200,
                        'timestamp' => now()->subDays(2),
                        'category' => 'business',
                    ],
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $progress,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting progress: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get progress'], 500);
        }
    }

    /**
     * Get leaderboard
     */
    public function getLeaderboard(Request $request)
    {
        try {
            $request->validate([
                'period' => 'nullable|in:weekly,monthly,all_time',
                'category' => 'nullable|in:all,instagram,email,analytics,team,courses,business',
                'limit' => 'nullable|integer|min:1|max:100',
            ]);

            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }

            $period = $request->period ?? 'weekly';
            $category = $request->category ?? 'all';
            $limit = $request->limit ?? 50;

            $leaderboard = [
                [
                    'rank' => 1,
                    'user_id' => 101,
                    'name' => 'Sarah Johnson',
                    'avatar' => '/images/avatars/sarah.jpg',
                    'level' => 15,
                    'xp' => 12450,
                    'achievements' => 28,
                    'badge' => 'Social Media Master',
                    'is_current_user' => false,
                ],
                [
                    'rank' => 2,
                    'user_id' => 102,
                    'name' => 'Michael Chen',
                    'avatar' => '/images/avatars/michael.jpg',
                    'level' => 14,
                    'xp' => 11890,
                    'achievements' => 25,
                    'badge' => 'Email Expert',
                    'is_current_user' => false,
                ],
                [
                    'rank' => 3,
                    'user_id' => 103,
                    'name' => 'Emma Rodriguez',
                    'avatar' => '/images/avatars/emma.jpg',
                    'level' => 13,
                    'xp' => 10567,
                    'achievements' => 22,
                    'badge' => 'Analytics Ninja',
                    'is_current_user' => false,
                ],
                [
                    'rank' => 4,
                    'user_id' => 104,
                    'name' => 'David Kim',
                    'avatar' => '/images/avatars/david.jpg',
                    'level' => 12,
                    'xp' => 9234,
                    'achievements' => 20,
                    'badge' => 'Team Builder',
                    'is_current_user' => false,
                ],
                [
                    'rank' => 5,
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'avatar' => $user->avatar,
                    'level' => 8,
                    'xp' => 3450,
                    'achievements' => 12,
                    'badge' => 'Content Creator',
                    'is_current_user' => true,
                ],
                [
                    'rank' => 6,
                    'user_id' => 105,
                    'name' => 'Lisa Thompson',
                    'avatar' => '/images/avatars/lisa.jpg',
                    'level' => 7,
                    'xp' => 2890,
                    'achievements' => 10,
                    'badge' => 'Beginner',
                    'is_current_user' => false,
                ],
                [
                    'rank' => 7,
                    'user_id' => 106,
                    'name' => 'James Wilson',
                    'avatar' => '/images/avatars/james.jpg',
                    'level' => 6,
                    'xp' => 2345,
                    'achievements' => 8,
                    'badge' => 'Newcomer',
                    'is_current_user' => false,
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => array_slice($leaderboard, 0, $limit),
                'current_user_rank' => 5,
                'current_user_stats' => [
                    'level' => 8,
                    'xp' => 3450,
                    'achievements' => 12,
                    'badge' => 'Content Creator',
                ],
                'filters' => [
                    'period' => $period,
                    'category' => $category,
                    'limit' => $limit,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting leaderboard: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get leaderboard'], 500);
        }
    }

    /**
     * Get badges
     */
    public function getBadges()
    {
        try {
            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }

            $badges = [
                [
                    'id' => 1,
                    'name' => 'First Steps',
                    'description' => 'Complete your first action',
                    'icon' => 'footprints',
                    'rarity' => 'common',
                    'color' => '#4CAF50',
                    'earned' => true,
                    'earned_at' => now()->subDays(30),
                ],
                [
                    'id' => 2,
                    'name' => 'Content Creator',
                    'description' => 'Create 10 pieces of content',
                    'icon' => 'edit',
                    'rarity' => 'uncommon',
                    'color' => '#2196F3',
                    'earned' => true,
                    'earned_at' => now()->subDays(15),
                ],
                [
                    'id' => 3,
                    'name' => 'Social Media Master',
                    'description' => 'Complete all social media challenges',
                    'icon' => 'share',
                    'rarity' => 'rare',
                    'color' => '#FF9800',
                    'earned' => true,
                    'earned_at' => now()->subDays(8),
                ],
                [
                    'id' => 4,
                    'name' => 'Email Expert',
                    'description' => 'Send 100 email campaigns',
                    'icon' => 'mail',
                    'rarity' => 'epic',
                    'color' => '#9C27B0',
                    'earned' => false,
                    'earned_at' => null,
                ],
                [
                    'id' => 5,
                    'name' => 'Analytics Ninja',
                    'description' => 'Master analytics and reporting',
                    'icon' => 'bar-chart',
                    'rarity' => 'legendary',
                    'color' => '#F44336',
                    'earned' => false,
                    'earned_at' => null,
                ],
                [
                    'id' => 6,
                    'name' => 'Team Builder',
                    'description' => 'Build a team of 10 members',
                    'icon' => 'users',
                    'rarity' => 'rare',
                    'color' => '#FF5722',
                    'earned' => false,
                    'earned_at' => null,
                ],
                [
                    'id' => 7,
                    'name' => 'Revenue Generator',
                    'description' => 'Generate $10,000 in revenue',
                    'icon' => 'dollar-sign',
                    'rarity' => 'legendary',
                    'color' => '#795548',
                    'earned' => false,
                    'earned_at' => null,
                ],
                [
                    'id' => 8,
                    'name' => 'Course Creator',
                    'description' => 'Create and publish 5 courses',
                    'icon' => 'book',
                    'rarity' => 'epic',
                    'color' => '#607D8B',
                    'earned' => false,
                    'earned_at' => null,
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $badges,
                'summary' => [
                    'total_badges' => count($badges),
                    'earned_badges' => count(array_filter($badges, function($b) { return $b['earned']; })),
                    'rarity_breakdown' => [
                        'common' => count(array_filter($badges, function($b) { return $b['rarity'] === 'common'; })),
                        'uncommon' => count(array_filter($badges, function($b) { return $b['rarity'] === 'uncommon'; })),
                        'rare' => count(array_filter($badges, function($b) { return $b['rarity'] === 'rare'; })),
                        'epic' => count(array_filter($badges, function($b) { return $b['rarity'] === 'epic'; })),
                        'legendary' => count(array_filter($badges, function($b) { return $b['rarity'] === 'legendary'; })),
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting badges: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get badges'], 500);
        }
    }

    /**
     * Get challenges
     */
    public function getChallenges()
    {
        try {
            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }

            $challenges = [
                [
                    'id' => 1,
                    'name' => 'Weekly Content Challenge',
                    'description' => 'Create 7 posts this week',
                    'category' => 'instagram',
                    'type' => 'weekly',
                    'difficulty' => 'easy',
                    'xp_reward' => 500,
                    'badge_reward' => 'Weekly Warrior',
                    'start_date' => now()->startOfWeek(),
                    'end_date' => now()->endOfWeek(),
                    'progress' => 4,
                    'target' => 7,
                    'completed' => false,
                    'status' => 'active',
                ],
                [
                    'id' => 2,
                    'name' => 'Email Marketing Sprint',
                    'description' => 'Send 5 email campaigns this month',
                    'category' => 'email',
                    'type' => 'monthly',
                    'difficulty' => 'medium',
                    'xp_reward' => 1000,
                    'badge_reward' => 'Email Champion',
                    'start_date' => now()->startOfMonth(),
                    'end_date' => now()->endOfMonth(),
                    'progress' => 3,
                    'target' => 5,
                    'completed' => false,
                    'status' => 'active',
                ],
                [
                    'id' => 3,
                    'name' => 'Analytics Deep Dive',
                    'description' => 'Check analytics 30 times this month',
                    'category' => 'analytics',
                    'type' => 'monthly',
                    'difficulty' => 'easy',
                    'xp_reward' => 300,
                    'badge_reward' => 'Data Detective',
                    'start_date' => now()->startOfMonth(),
                    'end_date' => now()->endOfMonth(),
                    'progress' => 18,
                    'target' => 30,
                    'completed' => false,
                    'status' => 'active',
                ],
                [
                    'id' => 4,
                    'name' => 'Team Building Challenge',
                    'description' => 'Invite 3 new team members',
                    'category' => 'team',
                    'type' => 'special',
                    'difficulty' => 'hard',
                    'xp_reward' => 1500,
                    'badge_reward' => 'Team Builder Supreme',
                    'start_date' => now()->subDays(10),
                    'end_date' => now()->addDays(20),
                    'progress' => 1,
                    'target' => 3,
                    'completed' => false,
                    'status' => 'active',
                ],
                [
                    'id' => 5,
                    'name' => 'Revenue Milestone',
                    'description' => 'Generate $1000 in revenue',
                    'category' => 'business',
                    'type' => 'milestone',
                    'difficulty' => 'hard',
                    'xp_reward' => 2000,
                    'badge_reward' => 'Revenue Rockstar',
                    'start_date' => now()->subDays(30),
                    'end_date' => now()->addDays(60),
                    'progress' => 450,
                    'target' => 1000,
                    'completed' => false,
                    'status' => 'active',
                ],
                [
                    'id' => 6,
                    'name' => 'Content Consistency',
                    'description' => 'Post for 30 consecutive days',
                    'category' => 'instagram',
                    'type' => 'streak',
                    'difficulty' => 'medium',
                    'xp_reward' => 1200,
                    'badge_reward' => 'Consistency King',
                    'start_date' => now()->subDays(7),
                    'end_date' => now()->addDays(23),
                    'progress' => 7,
                    'target' => 30,
                    'completed' => false,
                    'status' => 'active',
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $challenges,
                'summary' => [
                    'total_challenges' => count($challenges),
                    'active_challenges' => count(array_filter($challenges, function($c) { return $c['status'] === 'active'; })),
                    'completed_challenges' => count(array_filter($challenges, function($c) { return $c['completed']; })),
                    'total_xp_available' => array_sum(array_column($challenges, 'xp_reward')),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting challenges: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get challenges'], 500);
        }
    }

    /**
     * Get rewards
     */
    public function getRewards()
    {
        try {
            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }

            $rewards = [
                [
                    'id' => 1,
                    'name' => 'Premium Template',
                    'description' => 'Unlock a premium template of your choice',
                    'type' => 'template',
                    'cost' => 1000,
                    'currency' => 'xp',
                    'value' => '$29.99',
                    'available' => true,
                    'redeemed' => false,
                    'icon' => 'gift',
                    'category' => 'premium',
                ],
                [
                    'id' => 2,
                    'name' => 'Extra Analytics',
                    'description' => '30 days of advanced analytics',
                    'type' => 'feature',
                    'cost' => 1500,
                    'currency' => 'xp',
                    'value' => '$19.99',
                    'available' => true,
                    'redeemed' => false,
                    'icon' => 'bar-chart',
                    'category' => 'premium',
                ],
                [
                    'id' => 3,
                    'name' => 'AI Content Credits',
                    'description' => '100 AI content generation credits',
                    'type' => 'credits',
                    'cost' => 800,
                    'currency' => 'xp',
                    'value' => '$14.99',
                    'available' => true,
                    'redeemed' => false,
                    'icon' => 'zap',
                    'category' => 'premium',
                ],
                [
                    'id' => 4,
                    'name' => 'Custom Badge',
                    'description' => 'Create your own custom badge',
                    'type' => 'customization',
                    'cost' => 2000,
                    'currency' => 'xp',
                    'value' => 'Exclusive',
                    'available' => true,
                    'redeemed' => false,
                    'icon' => 'award',
                    'category' => 'exclusive',
                ],
                [
                    'id' => 5,
                    'name' => 'Priority Support',
                    'description' => '7 days of priority customer support',
                    'type' => 'support',
                    'cost' => 1200,
                    'currency' => 'xp',
                    'value' => '$49.99',
                    'available' => true,
                    'redeemed' => false,
                    'icon' => 'headphones',
                    'category' => 'premium',
                ],
                [
                    'id' => 6,
                    'name' => 'Team Boost',
                    'description' => '2x XP for your entire team for 7 days',
                    'type' => 'boost',
                    'cost' => 2500,
                    'currency' => 'xp',
                    'value' => 'Team Bonus',
                    'available' => true,
                    'redeemed' => false,
                    'icon' => 'users',
                    'category' => 'team',
                ],
                [
                    'id' => 7,
                    'name' => 'Mewayz Merchandise',
                    'description' => 'Exclusive Mewayz branded items',
                    'type' => 'physical',
                    'cost' => 3000,
                    'currency' => 'xp',
                    'value' => '$39.99',
                    'available' => true,
                    'redeemed' => false,
                    'icon' => 'package',
                    'category' => 'exclusive',
                ],
                [
                    'id' => 8,
                    'name' => 'Feature Request',
                    'description' => 'Request a new feature to be prioritized',
                    'type' => 'feature_request',
                    'cost' => 5000,
                    'currency' => 'xp',
                    'value' => 'Priceless',
                    'available' => true,
                    'redeemed' => false,
                    'icon' => 'lightbulb',
                    'category' => 'exclusive',
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $rewards,
                'user_balance' => [
                    'xp' => 3450,
                    'points' => 1200,
                    'tokens' => 45,
                ],
                'categories' => [
                    'premium' => count(array_filter($rewards, function($r) { return $r['category'] === 'premium'; })),
                    'exclusive' => count(array_filter($rewards, function($r) { return $r['category'] === 'exclusive'; })),
                    'team' => count(array_filter($rewards, function($r) { return $r['category'] === 'team'; })),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting rewards: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get rewards'], 500);
        }
    }

    /**
     * Redeem reward
     */
    public function redeemReward(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $workspace = $user->workspaces()->where('is_primary', true)->first();
            
            if (!$workspace) {
                return response()->json(['error' => 'Workspace not found'], 404);
            }

            // Simulate reward redemption
            $redemption = [
                'id' => rand(10000, 99999),
                'reward_id' => $id,
                'user_id' => $user->id,
                'workspace_id' => $workspace->id,
                'cost' => 1000,
                'currency' => 'xp',
                'status' => 'redeemed',
                'redeemed_at' => now(),
                'expires_at' => now()->addDays(30),
            ];

            Log::info('Reward redeemed', [
                'user_id' => $user->id,
                'reward_id' => $id,
                'cost' => $redemption['cost'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reward redeemed successfully',
                'data' => $redemption,
            ]);
        } catch (\Exception $e) {
            Log::error('Error redeeming reward: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to redeem reward'], 500);
        }
    }
}