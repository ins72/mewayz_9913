<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Achievement;
use App\Models\UserProgress;
use App\Models\Leaderboard;
use App\Models\Challenge;
use App\Models\Guild;
use App\Models\Reward;
use App\Models\UnifiedAnalyticsEvent;
use Carbon\Carbon;

class GamificationController extends Controller
{
    /**
     * Get user's gamification dashboard
     */
    public function getDashboard(Request $request)
    {
        try {
            $user = $request->user();
            $userProgress = $this->getUserProgress($user);
            
            $dashboard = [
                'user_info' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'avatar' => $user->avatar,
                    'level' => $userProgress->current_level,
                    'xp' => $userProgress->current_xp,
                    'total_xp' => $userProgress->total_xp,
                    'xp_to_next_level' => $userProgress->xp_to_next_level,
                    'prestige' => $userProgress->prestige,
                    'prestige_points' => $userProgress->prestige_points
                ],
                'recent_achievements' => $this->getRecentAchievements($user, 5),
                'active_challenges' => $this->getActiveChallenges($user, 3),
                'leaderboard_positions' => $this->getLeaderboardPositions($user),
                'daily_quests' => $this->getDailyQuests($user),
                'streaks' => $this->getStreakData($user),
                'guild_info' => $this->getGuildInfo($user),
                'next_rewards' => $this->getNextRewards($user),
                'progress_stats' => $this->getProgressStats($user),
                'recommendations' => $this->getPersonalizedRecommendations($user)
            ];

            return response()->json([
                'success' => true,
                'data' => $dashboard
            ]);

        } catch (\Exception $e) {
            Log::error('Gamification dashboard failed', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load gamification dashboard'
            ], 500);
        }
    }

    /**
     * Get user achievements
     */
    public function getAchievements(Request $request)
    {
        $request->validate([
            'category' => 'nullable|string',
            'tier' => 'nullable|string',
            'status' => 'nullable|string|in:earned,available,locked'
        ]);

        try {
            $user = $request->user();
            $query = Achievement::active();
            
            if ($request->category) {
                $query->where('category', $request->category);
            }
            
            if ($request->tier) {
                $query->where('tier', $request->tier);
            }
            
            $achievements = $query->get()->map(function ($achievement) use ($user) {
                $isEarned = $achievement->isEarnedBy($user);
                $canEarn = $achievement->canBeEarnedBy($user);
                $progress = $achievement->getProgressFor($user);
                
                return [
                    'id' => $achievement->id,
                    'name' => $achievement->name,
                    'description' => $achievement->description,
                    'flavor_text' => $achievement->flavor_text,
                    'category' => $achievement->category,
                    'tier' => $achievement->tier,
                    'tier_color' => $achievement->tier_color,
                    'difficulty' => $achievement->difficulty,
                    'difficulty_color' => $achievement->difficulty_color,
                    'rarity' => $achievement->rarity,
                    'rarity_text' => $achievement->rarity_text,
                    'icon' => $achievement->icon,
                    'animated_icon' => $achievement->animated_icon,
                    'completion_rate' => $achievement->completion_rate,
                    'earned_count' => $achievement->earned_count,
                    'is_earned' => $isEarned,
                    'can_earn' => $canEarn,
                    'is_hidden' => $achievement->is_hidden && !$isEarned,
                    'is_secret' => $achievement->is_secret && !$isEarned,
                    'progress' => $progress,
                    'rewards' => $achievement->rewards,
                    'estimated_time' => $achievement->estimated_time
                ];
            });

            // Filter by status if requested
            if ($request->status) {
                $achievements = $achievements->filter(function ($achievement) use ($request) {
                    switch ($request->status) {
                        case 'earned':
                            return $achievement['is_earned'];
                        case 'available':
                            return !$achievement['is_earned'] && $achievement['can_earn'];
                        case 'locked':
                            return !$achievement['is_earned'] && !$achievement['can_earn'];
                        default:
                            return true;
                    }
                });
            }

            return response()->json([
                'success' => true,
                'data' => $achievements->values()
            ]);

        } catch (\Exception $e) {
            Log::error('Get achievements failed', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load achievements'
            ], 500);
        }
    }

    /**
     * Get leaderboards
     */
    public function getLeaderboards(Request $request)
    {
        $request->validate([
            'type' => 'nullable|string',
            'category' => 'nullable|string',
            'timeframe' => 'nullable|string',
            'limit' => 'nullable|integer|min:1|max:100'
        ]);

        try {
            $query = Leaderboard::active()->public();
            
            if ($request->type) {
                $query->where('type', $request->type);
            }
            
            if ($request->category) {
                $query->where('category', $request->category);
            }
            
            if ($request->timeframe) {
                $query->where('timeframe', $request->timeframe);
            }
            
            $leaderboards = $query->get()->map(function ($leaderboard) use ($request) {
                $limit = $request->limit ?? 100;
                return [
                    'id' => $leaderboard->id,
                    'name' => $leaderboard->name,
                    'description' => $leaderboard->description,
                    'type' => $leaderboard->type,
                    'category' => $leaderboard->category,
                    'timeframe' => $leaderboard->timeframe,
                    'rankings' => $leaderboard->getCurrentRankings($limit),
                    'user_position' => $leaderboard->getUserPosition($request->user()->id),
                    'last_reset' => $leaderboard->last_reset,
                    'next_reset' => $leaderboard->next_reset
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $leaderboards
            ]);

        } catch (\Exception $e) {
            Log::error('Get leaderboards failed', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load leaderboards'
            ], 500);
        }
    }

    /**
     * Get challenges
     */
    public function getChallenges(Request $request)
    {
        $request->validate([
            'type' => 'nullable|string',
            'category' => 'nullable|string',
            'difficulty' => 'nullable|string',
            'status' => 'nullable|string|in:available,joined,completed'
        ]);

        try {
            $user = $request->user();
            $query = Challenge::active();
            
            if ($request->type) {
                $query->where('type', $request->type);
            }
            
            if ($request->category) {
                $query->where('category', $request->category);
            }
            
            if ($request->difficulty) {
                $query->where('difficulty', $request->difficulty);
            }
            
            $challenges = $query->get()->map(function ($challenge) use ($user) {
                $isJoined = $challenge->isJoinedBy($user);
                $isCompleted = $challenge->isCompletedBy($user);
                $canJoin = $challenge->canBeJoinedBy($user);
                $progress = $challenge->getProgressFor($user);
                
                return [
                    'id' => $challenge->id,
                    'name' => $challenge->name,
                    'description' => $challenge->description,
                    'story' => $challenge->story,
                    'type' => $challenge->type,
                    'category' => $challenge->category,
                    'difficulty' => $challenge->difficulty,
                    'difficulty_color' => $challenge->difficulty_color,
                    'start_date' => $challenge->start_date,
                    'end_date' => $challenge->end_date,
                    'duration_days' => $challenge->duration_days,
                    'time_remaining' => $challenge->time_remaining,
                    'is_active_now' => $challenge->is_active_now,
                    'is_upcoming' => $challenge->is_upcoming,
                    'is_expired' => $challenge->is_expired,
                    'participant_limit' => $challenge->participant_limit,
                    'current_participants' => $challenge->current_participants,
                    'participation_percentage' => $challenge->participation_percentage,
                    'completion_rate' => $challenge->completion_rate,
                    'is_team_challenge' => $challenge->is_team_challenge,
                    'team_size' => $challenge->team_size,
                    'is_ranked' => $challenge->is_ranked,
                    'is_featured' => $challenge->is_featured,
                    'is_joined' => $isJoined,
                    'is_completed' => $isCompleted,
                    'can_join' => $canJoin,
                    'progress' => $progress,
                    'requirements' => $challenge->requirements,
                    'rewards' => $challenge->rewards,
                    'top_performers' => $challenge->getTopPerformers(5)
                ];
            });

            // Filter by status if requested
            if ($request->status) {
                $challenges = $challenges->filter(function ($challenge) use ($request) {
                    switch ($request->status) {
                        case 'available':
                            return !$challenge['is_joined'] && $challenge['can_join'];
                        case 'joined':
                            return $challenge['is_joined'] && !$challenge['is_completed'];
                        case 'completed':
                            return $challenge['is_completed'];
                        default:
                            return true;
                    }
                });
            }

            return response()->json([
                'success' => true,
                'data' => $challenges->values()
            ]);

        } catch (\Exception $e) {
            Log::error('Get challenges failed', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load challenges'
            ], 500);
        }
    }

    /**
     * Join a challenge
     */
    public function joinChallenge(Request $request, $challengeId)
    {
        $request->validate([
            'team_id' => 'nullable|integer|exists:challenge_teams,id'
        ]);

        try {
            $user = $request->user();
            $challenge = Challenge::findOrFail($challengeId);
            
            $result = $challenge->addParticipant($user, $request->team_id);
            
            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Successfully joined challenge',
                    'data' => [
                        'challenge_id' => $challenge->id,
                        'progress' => $challenge->getProgressFor($user)
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to join challenge. Check requirements and availability.'
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Join challenge failed', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id,
                'challenge_id' => $challengeId
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to join challenge'
            ], 500);
        }
    }

    /**
     * Add XP to user
     */
    public function addXP(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:1|max:10000',
            'source' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'metadata' => 'nullable|array'
        ]);

        try {
            $user = $request->user();
            $userProgress = $this->getUserProgress($user);
            
            $finalAmount = $userProgress->addXP(
                $request->amount,
                $request->source,
                $request->metadata ?? []
            );
            
            // Check for new achievements
            $newAchievements = $this->checkForNewAchievements($user);
            
            return response()->json([
                'success' => true,
                'message' => "Added {$finalAmount} XP",
                'data' => [
                    'xp_added' => $finalAmount,
                    'current_level' => $userProgress->current_level,
                    'current_xp' => $userProgress->current_xp,
                    'total_xp' => $userProgress->total_xp,
                    'xp_to_next_level' => $userProgress->xp_to_next_level,
                    'new_achievements' => $newAchievements
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Add XP failed', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to add XP'
            ], 500);
        }
    }

    /**
     * Update user streak
     */
    public function updateStreak(Request $request)
    {
        $request->validate([
            'streak_type' => 'required|string|in:daily_login,content_creation,sales_performance,learning_progress,community_engagement',
            'action' => 'required|string|in:increment,maintain,break'
        ]);

        try {
            $user = $request->user();
            $userProgress = $this->getUserProgress($user);
            
            $userProgress->updateStreak($request->streak_type, $request->action);
            
            return response()->json([
                'success' => true,
                'message' => 'Streak updated successfully',
                'data' => [
                    'streaks' => $userProgress->streaks
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Update streak failed', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update streak'
            ], 500);
        }
    }

    /**
     * Get or create user progress
     */
    private function getUserProgress(User $user)
    {
        $userProgress = $user->userProgress()->first();
        
        if (!$userProgress) {
            $userProgress = UserProgress::create([
                'user_id' => $user->id,
                'current_level' => 1,
                'current_xp' => 0,
                'total_xp' => 0,
                'lifetime_xp' => 0,
                'prestige' => 0,
                'prestige_points' => 0,
                'streaks' => [
                    'daily_login' => ['current' => 0, 'longest' => 0, 'total' => 0, 'last_updated' => now()->toDateString(), 'freeze_tokens' => 0],
                    'content_creation' => ['current' => 0, 'longest' => 0, 'total' => 0, 'last_updated' => now()->toDateString(), 'freeze_tokens' => 0],
                    'sales_performance' => ['current' => 0, 'longest' => 0, 'total' => 0, 'last_updated' => now()->toDateString(), 'freeze_tokens' => 0],
                    'learning_progress' => ['current' => 0, 'longest' => 0, 'total' => 0, 'last_updated' => now()->toDateString(), 'freeze_tokens' => 0],
                    'community_engagement' => ['current' => 0, 'longest' => 0, 'total' => 0, 'last_updated' => now()->toDateString(), 'freeze_tokens' => 0]
                ]
            ]);
        }
        
        return $userProgress;
    }

    /**
     * Get recent achievements
     */
    private function getRecentAchievements(User $user, $limit = 5)
    {
        return $user->achievements()
                   ->orderByPivot('earned_at', 'desc')
                   ->limit($limit)
                   ->get()
                   ->map(function ($achievement) {
                       return [
                           'id' => $achievement->id,
                           'name' => $achievement->name,
                           'description' => $achievement->description,
                           'tier' => $achievement->tier,
                           'icon' => $achievement->icon,
                           'earned_at' => $achievement->pivot->earned_at
                       ];
                   });
    }

    /**
     * Get active challenges for user
     */
    private function getActiveChallenges(User $user, $limit = 3)
    {
        return $user->challenges()
                   ->where('is_active', true)
                   ->whereNull('challenge_participants.completed_at')
                   ->limit($limit)
                   ->get()
                   ->map(function ($challenge) use ($user) {
                       return [
                           'id' => $challenge->id,
                           'name' => $challenge->name,
                           'description' => $challenge->description,
                           'type' => $challenge->type,
                           'progress' => $challenge->pivot->progress,
                           'end_date' => $challenge->end_date,
                           'time_remaining' => $challenge->time_remaining
                       ];
                   });
    }

    /**
     * Get user's leaderboard positions
     */
    private function getLeaderboardPositions(User $user)
    {
        $positions = [];
        $leaderboards = Leaderboard::active()->public()->get();
        
        foreach ($leaderboards as $leaderboard) {
            $position = $leaderboard->getUserPosition($user->id);
            if ($position) {
                $positions[] = [
                    'leaderboard_id' => $leaderboard->id,
                    'leaderboard_name' => $leaderboard->name,
                    'category' => $leaderboard->category,
                    'rank' => $position['rank'],
                    'score' => $position['score'],
                    'change' => $position['change']
                ];
            }
        }
        
        return $positions;
    }

    /**
     * Get daily quests for user
     */
    private function getDailyQuests(User $user)
    {
        $today = now()->toDateString();
        
        $quests = $user->dailyQuests()
                      ->where('quest_date', $today)
                      ->get();
        
        // Generate new quests if none exist for today
        if ($quests->isEmpty()) {
            $quests = $this->generateDailyQuests($user, $today);
        }
        
        return $quests->map(function ($quest) {
            return [
                'id' => $quest->id,
                'title' => $quest->title,
                'description' => $quest->description,
                'progress' => $quest->progress,
                'target' => $quest->target,
                'is_completed' => $quest->is_completed,
                'rewards' => $quest->rewards
            ];
        });
    }

    /**
     * Generate daily quests for user
     */
    private function generateDailyQuests(User $user, $date)
    {
        $questTemplates = [
            [
                'quest_type' => 'content_creation',
                'title' => 'Content Creator',
                'description' => 'Create 3 pieces of content today',
                'target' => 3,
                'rewards' => [['type' => 'xp', 'value' => 100], ['type' => 'credits', 'value' => 50]]
            ],
            [
                'quest_type' => 'social_engagement',
                'title' => 'Social Butterfly',
                'description' => 'Engage with 10 posts today',
                'target' => 10,
                'rewards' => [['type' => 'xp', 'value' => 75]]
            ],
            [
                'quest_type' => 'learning',
                'title' => 'Knowledge Seeker',
                'description' => 'Complete 1 course lesson today',
                'target' => 1,
                'rewards' => [['type' => 'xp', 'value' => 150]]
            ]
        ];
        
        $quests = collect();
        
        foreach ($questTemplates as $template) {
            $quest = $user->dailyQuests()->create([
                'quest_type' => $template['quest_type'],
                'title' => $template['title'],
                'description' => $template['description'],
                'requirements' => ['action' => $template['quest_type']],
                'rewards' => $template['rewards'],
                'target' => $template['target'],
                'quest_date' => $date
            ]);
            
            $quests->push($quest);
        }
        
        return $quests;
    }

    /**
     * Get streak data
     */
    private function getStreakData(User $user)
    {
        $userProgress = $this->getUserProgress($user);
        return $userProgress->streaks ?? [];
    }

    /**
     * Get guild info for user
     */
    private function getGuildInfo(User $user)
    {
        $guild = $user->guild()->first();
        
        if (!$guild) {
            return null;
        }
        
        return [
            'id' => $guild->id,
            'name' => $guild->name,
            'level' => $guild->level,
            'member_count' => $guild->member_count,
            'user_role' => $guild->pivot->role,
            'contribution_xp' => $guild->pivot->contribution_xp
        ];
    }

    /**
     * Get next available rewards
     */
    private function getNextRewards(User $user)
    {
        return Reward::where('cost', '>', 0)
                    ->where('stock', '>', 0)
                    ->orderBy('cost', 'asc')
                    ->limit(5)
                    ->get()
                    ->map(function ($reward) {
                        return [
                            'id' => $reward->id,
                            'name' => $reward->name,
                            'description' => $reward->description,
                            'type' => $reward->type,
                            'cost' => $reward->cost,
                            'currency' => $reward->currency
                        ];
                    });
    }

    /**
     * Get progress statistics
     */
    private function getProgressStats(User $user)
    {
        $userProgress = $this->getUserProgress($user);
        
        return [
            'achievements_earned' => $user->achievements()->count(),
            'challenges_completed' => $user->challenges()->wherePivot('completed_at', '!=', null)->count(),
            'days_active' => $user->created_at->diffInDays(now()),
            'total_contributions' => $user->analyticsEvents()->count(),
            'rank_percentile' => $this->getUserRankPercentile($user),
            'activity_score' => $this->calculateActivityScore($user)
        ];
    }

    /**
     * Get personalized recommendations
     */
    private function getPersonalizedRecommendations(User $user)
    {
        $recommendations = [];
        
        // Suggest challenges based on user interests
        $availableChallenges = Challenge::active()
                                     ->ongoing()
                                     ->whereNotIn('id', $user->challenges()->pluck('challenge_id'))
                                     ->limit(3)
                                     ->get();
        
        foreach ($availableChallenges as $challenge) {
            if ($challenge->canBeJoinedBy($user)) {
                $recommendations[] = [
                    'type' => 'challenge',
                    'title' => "Join Challenge: {$challenge->name}",
                    'description' => $challenge->description,
                    'action_url' => "/challenges/{$challenge->id}",
                    'priority' => 'medium'
                ];
            }
        }
        
        // Suggest achievements close to completion
        $achievements = Achievement::active()->get();
        foreach ($achievements as $achievement) {
            if (!$achievement->isEarnedBy($user) && $achievement->canBeEarnedBy($user)) {
                $progress = $achievement->getProgressFor($user);
                $totalProgress = 0;
                $completedRequirements = 0;
                
                foreach ($progress as $req) {
                    $totalProgress += $req['percentage'];
                    if ($req['completed']) {
                        $completedRequirements++;
                    }
                }
                
                $avgProgress = count($progress) > 0 ? $totalProgress / count($progress) : 0;
                
                if ($avgProgress > 50) {
                    $recommendations[] = [
                        'type' => 'achievement',
                        'title' => "Complete Achievement: {$achievement->name}",
                        'description' => $achievement->description,
                        'progress' => $avgProgress,
                        'action_url' => "/achievements/{$achievement->id}",
                        'priority' => $avgProgress > 80 ? 'high' : 'medium'
                    ];
                }
            }
        }
        
        // Sort by priority
        usort($recommendations, function ($a, $b) {
            $priorities = ['high' => 3, 'medium' => 2, 'low' => 1];
            return $priorities[$b['priority']] - $priorities[$a['priority']];
        });
        
        return array_slice($recommendations, 0, 5);
    }

    /**
     * Check for new achievements
     */
    private function checkForNewAchievements(User $user)
    {
        $newAchievements = [];
        $achievements = Achievement::active()->get();
        
        foreach ($achievements as $achievement) {
            if (!$achievement->isEarnedBy($user) && $achievement->canBeEarnedBy($user)) {
                $progress = $achievement->getProgressFor($user);
                $allCompleted = true;
                
                foreach ($progress as $req) {
                    if (!$req['completed']) {
                        $allCompleted = false;
                        break;
                    }
                }
                
                if ($allCompleted) {
                    if ($achievement->awardTo($user)) {
                        $newAchievements[] = [
                            'id' => $achievement->id,
                            'name' => $achievement->name,
                            'description' => $achievement->description,
                            'tier' => $achievement->tier,
                            'icon' => $achievement->icon,
                            'rewards' => $achievement->rewards
                        ];
                    }
                }
            }
        }
        
        return $newAchievements;
    }

    /**
     * Calculate user rank percentile
     */
    private function getUserRankPercentile(User $user)
    {
        $userProgress = $this->getUserProgress($user);
        $totalUsers = UserProgress::count();
        
        if ($totalUsers <= 1) {
            return 100;
        }
        
        $usersWithLowerXP = UserProgress::where('total_xp', '<', $userProgress->total_xp)->count();
        return round(($usersWithLowerXP / $totalUsers) * 100, 1);
    }

    /**
     * Calculate activity score
     */
    private function calculateActivityScore(User $user)
    {
        $recentActivity = $user->analyticsEvents()
                             ->where('created_at', '>=', now()->subDays(30))
                             ->count();
        
        $streakBonus = 0;
        $userProgress = $this->getUserProgress($user);
        if ($userProgress->streaks) {
            foreach ($userProgress->streaks as $streak) {
                $streakBonus += $streak['current'] ?? 0;
            }
        }
        
        return min(100, $recentActivity + $streakBonus);
    }
}