<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Gamification\Achievement;
use App\Models\Gamification\UserAchievement;
use App\Models\Gamification\XpEvent;
use App\Models\Gamification\UserLevel;
use App\Models\Gamification\Streak;
use Carbon\Carbon;

class GamificationController extends Controller
{
    /**
     * Get user's complete gamification profile
     */
    public function getProfile(Request $request)
    {
        try {
            $user = $request->user();
            
            // Get or create user level
            $userLevel = $user->gamificationLevel ?? $this->createUserLevel($user);
            
            // Get achievements
            $achievements = $this->getUserAchievements($user);
            
            // Get streaks
            $streaks = $this->getUserStreaks($user);
            
            // Get recent XP events
            $recentXpEvents = $this->getRecentXpEvents($user);
            
            // Get leaderboard position
            $leaderboardPosition = $this->getUserLeaderboardPosition($user);

            $profile = [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'joined_at' => $user->created_at
                ],
                'level' => [
                    'level' => $userLevel->level,
                    'level_name' => $userLevel->level_name,
                    'level_tier' => $userLevel->level_tier,
                    'tier_color' => $userLevel->getTierColor(),
                    'tier_icon' => $userLevel->getTierIcon(),
                    'total_xp' => $userLevel->total_xp,
                    'current_level_xp' => $userLevel->current_level_xp,
                    'next_level_xp' => $userLevel->next_level_xp,
                    'xp_to_next_level' => $userLevel->xp_to_next_level,
                    'progress_percentage' => $userLevel->getProgressPercentage(),
                    'level_benefits' => $userLevel->level_benefits,
                    'last_level_up' => $userLevel->last_level_up
                ],
                'achievements' => $achievements,
                'streaks' => $streaks,
                'recent_activity' => $recentXpEvents,
                'leaderboard' => $leaderboardPosition,
                'statistics' => [
                    'total_achievements' => $achievements['completed_count'],
                    'total_xp_earned' => $userLevel->total_xp,
                    'active_streaks' => collect($streaks)->where('is_active', true)->count(),
                    'longest_streak' => collect($streaks)->max('longest_streak') ?? 0,
                    'days_active' => $this->getDaysActive($user),
                    'rank' => $leaderboardPosition['global_rank'] ?? 'Unranked'
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $profile
            ]);

        } catch (\Exception $e) {
            Log::error('Gamification profile failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve gamification profile'
            ], 500);
        }
    }

    /**
     * Get all available achievements
     */
    public function getAchievements(Request $request)
    {
        $request->validate([
            'category' => 'nullable|string',
            'type' => 'nullable|string',
            'difficulty' => 'nullable|string|in:easy,medium,hard,legendary',
            'completed' => 'nullable|boolean',
            'per_page' => 'nullable|integer|min:1|max:100'
        ]);

        try {
            $user = $request->user();
            $perPage = $request->per_page ?? 20;
            
            $query = Achievement::with('userAchievements')->active();
            
            if ($request->category) {
                $query->byCategory($request->category);
            }
            
            if ($request->type) {
                $query->byType($request->type);
            }
            
            if ($request->difficulty) {
                $query->byDifficulty($request->difficulty);
            }
            
            $achievements = $query->orderBy('difficulty')
                                 ->orderBy('sort_order')
                                 ->paginate($perPage);

            $processedAchievements = $achievements->map(function ($achievement) use ($user) {
                $progress = $achievement->getProgressForUser($user);
                
                return [
                    'id' => $achievement->id,
                    'name' => $achievement->name,
                    'slug' => $achievement->slug,
                    'description' => $achievement->description,
                    'icon' => $achievement->icon,
                    'badge_color' => $achievement->badge_color,
                    'type' => $achievement->type,
                    'category' => $achievement->category,
                    'difficulty' => $achievement->difficulty,
                    'difficulty_color' => $achievement->getDifficultyColor(),
                    'points' => $achievement->points,
                    'requirements' => $achievement->requirements,
                    'rewards' => $achievement->rewards,
                    'is_repeatable' => $achievement->is_repeatable,
                    'max_completions' => $achievement->max_completions,
                    'unlock_condition' => $achievement->unlock_condition,
                    'progress' => $progress,
                    'can_be_awarded' => $achievement->canBeAwarded($user),
                    'completion_stats' => $achievement->getCompletionStats()
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'achievements' => $processedAchievements,
                    'pagination' => [
                        'current_page' => $achievements->currentPage(),
                        'total_pages' => $achievements->lastPage(),
                        'per_page' => $achievements->perPage(),
                        'total' => $achievements->total(),
                        'has_more' => $achievements->hasMorePages()
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Get achievements failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve achievements'
            ], 500);
        }
    }

    /**
     * Award XP to user
     */
    public function awardXp(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|integer|min:1|max:10000',
            'event_type' => 'required|string|max:255',
            'event_category' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'source_type' => 'nullable|string|max:255',
            'source_id' => 'nullable|integer',
            'metadata' => 'nullable|array'
        ]);

        try {
            $user = User::findOrFail($request->user_id);
            
            // Get or create user level
            $userLevel = $user->gamificationLevel() ?? $this->createUserLevel($user);
            
            // Award XP
            $userLevel->addXp($request->amount, $request->event_type, [
                'category' => $request->event_category ?? 'general',
                'description' => $request->description,
                'source_type' => $request->source_type,
                'source_id' => $request->source_id,
                'metadata' => $request->metadata ?? []
            ]);
            
            // Check for achievements
            $this->checkAchievements($user, $request->event_type, $request->metadata ?? []);

            return response()->json([
                'success' => true,
                'message' => "Awarded {$request->amount} XP to user",
                'data' => [
                    'user_id' => $user->id,
                    'xp_awarded' => $request->amount,
                    'new_total_xp' => $userLevel->fresh()->total_xp,
                    'new_level' => $userLevel->fresh()->level,
                    'level_name' => $userLevel->fresh()->level_name
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Award XP failed', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to award XP'
            ], 500);
        }
    }

    /**
     * Update user streak
     */
    public function updateStreak(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'streak_type' => 'required|string|max:255',
            'activity_date' => 'nullable|date'
        ]);

        try {
            $user = User::findOrFail($request->user_id);
            
            // Get or create streak
            $streak = Streak::firstOrCreate([
                'user_id' => $user->id,
                'streak_type' => $request->streak_type
            ], [
                'current_streak' => 0,
                'longest_streak' => 0,
                'total_completions' => 0,
                'is_active' => true,
                'streak_multiplier' => 1,
                'milestones' => []
            ]);
            
            // Update streak
            $streak->updateStreak($request->activity_date);

            return response()->json([
                'success' => true,
                'message' => 'Streak updated successfully',
                'data' => [
                    'streak_type' => $streak->streak_type,
                    'current_streak' => $streak->current_streak,
                    'longest_streak' => $streak->longest_streak,
                    'total_completions' => $streak->total_completions,
                    'status' => $streak->getStreakStatus(),
                    'next_milestone' => $streak->getNextMilestone()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Update streak failed', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update streak'
            ], 500);
        }
    }

    /**
     * Get leaderboard
     */
    public function getLeaderboard(Request $request)
    {
        $request->validate([
            'type' => 'nullable|string|in:xp,level,achievements,streaks',
            'period' => 'nullable|string|in:daily,weekly,monthly,yearly,all_time',
            'limit' => 'nullable|integer|min:1|max:100'
        ]);

        try {
            $type = $request->type ?? 'xp';
            $period = $request->period ?? 'all_time';
            $limit = $request->limit ?? 50;
            
            $leaderboard = $this->generateLeaderboard($type, $period, $limit);

            return response()->json([
                'success' => true,
                'data' => [
                    'leaderboard' => $leaderboard,
                    'type' => $type,
                    'period' => $period,
                    'generated_at' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Get leaderboard failed', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve leaderboard'
            ], 500);
        }
    }

    /**
     * Get gamification statistics
     */
    public function getStatistics(Request $request)
    {
        $request->validate([
            'period' => 'nullable|string|in:7d,30d,90d,1y,all_time'
        ]);

        try {
            $period = $request->period ?? '30d';
            $dateRange = $this->parseDateRange($period);
            
            $statistics = [
                'overview' => [
                    'total_users' => User::count(),
                    'active_users' => UserLevel::whereNotNull('last_level_up')
                                             ->where('last_level_up', '>=', $dateRange[0])
                                             ->count(),
                    'total_xp_awarded' => XpEvent::whereBetween('created_at', $dateRange)->sum('final_xp'),
                    'total_achievements_completed' => UserAchievement::whereBetween('completed_at', $dateRange)
                                                                   ->where('completed', true)
                                                                   ->count(),
                    'active_streaks' => Streak::where('is_active', true)->count(),
                    'total_level_ups' => UserLevel::whereBetween('last_level_up', $dateRange)
                                                  ->whereNotNull('last_level_up')
                                                  ->count()
                ],
                'level_distribution' => UserLevel::select('level_tier', DB::raw('count(*) as count'))
                                                 ->groupBy('level_tier')
                                                 ->get()
                                                 ->mapWithKeys(function ($item) {
                                                     return [$item->level_tier => $item->count];
                                                 }),
                'achievement_stats' => [
                    'total_achievements' => Achievement::active()->count(),
                    'completion_rate' => $this->getAchievementCompletionRate(),
                    'popular_achievements' => $this->getPopularAchievements(),
                    'difficulty_distribution' => Achievement::active()
                                                           ->select('difficulty', DB::raw('count(*) as count'))
                                                           ->groupBy('difficulty')
                                                           ->get()
                                                           ->mapWithKeys(function ($item) {
                                                               return [$item->difficulty => $item->count];
                                                           })
                ],
                'xp_activity' => [
                    'daily_xp' => $this->getDailyXpActivity($dateRange),
                    'top_xp_sources' => $this->getTopXpSources($dateRange),
                    'average_xp_per_user' => XpEvent::whereBetween('created_at', $dateRange)
                                                   ->avg('final_xp')
                ],
                'streak_stats' => [
                    'active_streaks' => Streak::where('is_active', true)->count(),
                    'longest_active_streak' => Streak::where('is_active', true)->max('current_streak'),
                    'total_streak_days' => Streak::sum('total_completions'),
                    'streak_types' => Streak::select('streak_type', DB::raw('count(*) as count'))
                                           ->groupBy('streak_type')
                                           ->get()
                                           ->mapWithKeys(function ($item) {
                                               return [$item->streak_type => $item->count];
                                           })
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $statistics
            ]);

        } catch (\Exception $e) {
            Log::error('Get gamification statistics failed', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics'
            ], 500);
        }
    }

    // Helper methods

    protected function createUserLevel(User $user)
    {
        return UserLevel::create([
            'user_id' => $user->id,
            'level' => 1,
            'total_xp' => 0,
            'current_level_xp' => 0,
            'next_level_xp' => 100,
            'xp_to_next_level' => 100,
            'level_name' => 'Newcomer',
            'level_tier' => 'Bronze',
            'level_benefits' => []
        ]);
    }

    protected function getUserAchievements(User $user)
    {
        $userAchievements = UserAchievement::where('user_id', $user->id)
                                          ->with('achievement')
                                          ->get();

        $completed = $userAchievements->where('completed', true);
        $inProgress = $userAchievements->where('completed', false)->where('progress', '>', 0);

        return [
            'completed' => $completed->map(function ($ua) {
                return [
                    'id' => $ua->achievement->id,
                    'name' => $ua->achievement->name,
                    'description' => $ua->achievement->description,
                    'icon' => $ua->achievement->icon,
                    'badge_color' => $ua->achievement->badge_color,
                    'type' => $ua->achievement->type,
                    'category' => $ua->achievement->category,
                    'difficulty' => $ua->achievement->difficulty,
                    'points' => $ua->achievement->points,
                    'completed_at' => $ua->completed_at,
                    'completion_count' => $ua->completion_count
                ];
            }),
            'in_progress' => $inProgress->map(function ($ua) {
                return [
                    'id' => $ua->achievement->id,
                    'name' => $ua->achievement->name,
                    'description' => $ua->achievement->description,
                    'icon' => $ua->achievement->icon,
                    'type' => $ua->achievement->type,
                    'category' => $ua->achievement->category,
                    'difficulty' => $ua->achievement->difficulty,
                    'progress' => $ua->progress,
                    'target' => $ua->target,
                    'percentage' => $ua->getProgressPercentage()
                ];
            }),
            'completed_count' => $completed->count(),
            'in_progress_count' => $inProgress->count(),
            'total_points' => $completed->sum(function ($ua) {
                return $ua->achievement->points;
            })
        ];
    }

    protected function getUserStreaks(User $user)
    {
        return Streak::where('user_id', $user->id)
                     ->get()
                     ->map(function ($streak) {
                         return [
                             'id' => $streak->id,
                             'streak_type' => $streak->streak_type,
                             'streak_type_label' => $streak->getStreakTypeLabel(),
                             'current_streak' => $streak->current_streak,
                             'longest_streak' => $streak->longest_streak,
                             'total_completions' => $streak->total_completions,
                             'last_activity_date' => $streak->last_activity_date,
                             'streak_start_date' => $streak->streak_start_date,
                             'is_active' => $streak->is_active,
                             'status' => $streak->getStreakStatus(),
                             'next_milestone' => $streak->getNextMilestone(),
                             'milestones' => $streak->milestones ?? []
                         ];
                     });
    }

    protected function getRecentXpEvents(User $user, $limit = 10)
    {
        return XpEvent::where('user_id', $user->id)
                      ->orderBy('created_at', 'desc')
                      ->limit($limit)
                      ->get()
                      ->map(function ($event) {
                          return [
                              'id' => $event->id,
                              'event_type' => $event->event_type,
                              'event_type_label' => $event->getEventTypeLabel(),
                              'event_category' => $event->event_category,
                              'category_label' => $event->getCategoryLabel(),
                              'xp_amount' => $event->xp_amount,
                              'final_xp' => $event->final_xp,
                              'description' => $event->description,
                              'is_bonus' => $event->is_bonus,
                              'bonus_reason' => $event->bonus_reason,
                              'created_at' => $event->created_at
                          ];
                      });
    }

    protected function getUserLeaderboardPosition(User $user)
    {
        $userLevel = $user->gamificationLevel();
        if (!$userLevel) {
            return ['global_rank' => null, 'total_xp' => 0];
        }

        $globalRank = UserLevel::where('total_xp', '>', $userLevel->total_xp)->count() + 1;
        
        return [
            'global_rank' => $globalRank,
            'total_xp' => $userLevel->total_xp,
            'level' => $userLevel->level,
            'level_name' => $userLevel->level_name,
            'level_tier' => $userLevel->level_tier
        ];
    }

    protected function generateLeaderboard($type, $period, $limit)
    {
        switch ($type) {
            case 'xp':
                return $this->getXpLeaderboard($period, $limit);
            case 'level':
                return $this->getLevelLeaderboard($period, $limit);
            case 'achievements':
                return $this->getAchievementLeaderboard($period, $limit);
            case 'streaks':
                return $this->getStreakLeaderboard($period, $limit);
            default:
                return $this->getXpLeaderboard($period, $limit);
        }
    }

    protected function getXpLeaderboard($period, $limit)
    {
        $dateRange = $this->parseDateRange($period);
        
        if ($period === 'all_time') {
            $query = UserLevel::with('user')
                             ->orderBy('total_xp', 'desc')
                             ->limit($limit);
        } else {
            $query = DB::table('gamification_user_levels')
                      ->join('users', 'gamification_user_levels.user_id', '=', 'users.id')
                      ->join('gamification_xp_events', 'gamification_user_levels.user_id', '=', 'gamification_xp_events.user_id')
                      ->select('users.id', 'users.name', 'users.email', 
                              DB::raw('SUM(gamification_xp_events.final_xp) as period_xp'),
                              'gamification_user_levels.total_xp', 'gamification_user_levels.level',
                              'gamification_user_levels.level_name', 'gamification_user_levels.level_tier')
                      ->whereBetween('gamification_xp_events.created_at', $dateRange)
                      ->groupBy('users.id', 'users.name', 'users.email', 'gamification_user_levels.total_xp', 
                               'gamification_user_levels.level', 'gamification_user_levels.level_name', 
                               'gamification_user_levels.level_tier')
                      ->orderBy('period_xp', 'desc')
                      ->limit($limit);
        }

        return $query->get()->map(function ($entry, $index) {
            return [
                'rank' => $index + 1,
                'user' => [
                    'id' => $entry->id,
                    'name' => $entry->name,
                    'email' => $entry->email
                ],
                'xp' => $entry->period_xp ?? $entry->total_xp,
                'total_xp' => $entry->total_xp,
                'level' => $entry->level,
                'level_name' => $entry->level_name,
                'level_tier' => $entry->level_tier
            ];
        });
    }

    protected function getLevelLeaderboard($period, $limit)
    {
        return UserLevel::with('user')
                        ->orderBy('level', 'desc')
                        ->orderBy('total_xp', 'desc')
                        ->limit($limit)
                        ->get()
                        ->map(function ($entry, $index) {
                            return [
                                'rank' => $index + 1,
                                'user' => [
                                    'id' => $entry->user->id,
                                    'name' => $entry->user->name,
                                    'email' => $entry->user->email
                                ],
                                'level' => $entry->level,
                                'level_name' => $entry->level_name,
                                'level_tier' => $entry->level_tier,
                                'total_xp' => $entry->total_xp,
                                'last_level_up' => $entry->last_level_up
                            ];
                        });
    }

    protected function getAchievementLeaderboard($period, $limit)
    {
        $dateRange = $this->parseDateRange($period);
        
        $query = DB::table('gamification_user_achievements')
                  ->join('users', 'gamification_user_achievements.user_id', '=', 'users.id')
                  ->join('gamification_achievements', 'gamification_user_achievements.achievement_id', '=', 'gamification_achievements.id')
                  ->select('users.id', 'users.name', 'users.email',
                          DB::raw('COUNT(gamification_user_achievements.id) as achievement_count'),
                          DB::raw('SUM(gamification_achievements.points) as total_points'))
                  ->where('gamification_user_achievements.completed', true);

        if ($period !== 'all_time') {
            $query->whereBetween('gamification_user_achievements.completed_at', $dateRange);
        }

        return $query->groupBy('users.id', 'users.name', 'users.email')
                     ->orderBy('achievement_count', 'desc')
                     ->orderBy('total_points', 'desc')
                     ->limit($limit)
                     ->get()
                     ->map(function ($entry, $index) {
                         return [
                             'rank' => $index + 1,
                             'user' => [
                                 'id' => $entry->id,
                                 'name' => $entry->name,
                                 'email' => $entry->email
                             ],
                             'achievement_count' => $entry->achievement_count,
                             'total_points' => $entry->total_points
                         ];
                     });
    }

    protected function getStreakLeaderboard($period, $limit)
    {
        return Streak::with('user')
                     ->where('is_active', true)
                     ->orderBy('current_streak', 'desc')
                     ->orderBy('longest_streak', 'desc')
                     ->limit($limit)
                     ->get()
                     ->groupBy('user_id')
                     ->map(function ($userStreaks, $userId) {
                         $user = $userStreaks->first()->user;
                         $maxStreak = $userStreaks->max('current_streak');
                         $longestStreak = $userStreaks->max('longest_streak');
                         
                         return [
                             'user' => [
                                 'id' => $user->id,
                                 'name' => $user->name,
                                 'email' => $user->email
                             ],
                             'current_streak' => $maxStreak,
                             'longest_streak' => $longestStreak,
                             'active_streaks' => $userStreaks->where('is_active', true)->count(),
                             'total_completions' => $userStreaks->sum('total_completions')
                         ];
                     })
                     ->sortByDesc('current_streak')
                     ->values()
                     ->take($limit)
                     ->map(function ($entry, $index) {
                         return array_merge($entry, ['rank' => $index + 1]);
                     });
    }

    protected function checkAchievements(User $user, $eventType, $eventData = [])
    {
        $achievements = Achievement::active()->get();
        
        foreach ($achievements as $achievement) {
            if ($achievement->canBeAwarded($user)) {
                $this->checkAchievementProgress($user, $achievement, $eventType, $eventData);
            }
        }
    }

    protected function checkAchievementProgress(User $user, Achievement $achievement, $eventType, $eventData)
    {
        $userAchievement = UserAchievement::firstOrCreate([
            'user_id' => $user->id,
            'achievement_id' => $achievement->id
        ], [
            'progress' => 0,
            'target' => $achievement->getTargetValue(),
            'completed' => false,
            'completion_count' => 0,
            'progress_data' => []
        ]);

        // Simple achievement progress logic - can be extended
        if ($achievement->type === 'milestone' && $eventType === 'login') {
            $userAchievement->updateProgress(1, $eventData);
        } elseif ($achievement->type === 'engagement' && in_array($eventType, ['post_created', 'bio_site_updated'])) {
            $userAchievement->updateProgress(1, $eventData);
        } elseif ($achievement->type === 'revenue' && isset($eventData['revenue'])) {
            $userAchievement->updateProgress($eventData['revenue'], $eventData);
        }
    }

    protected function getDaysActive(User $user)
    {
        return XpEvent::where('user_id', $user->id)
                      ->select(DB::raw('DATE(created_at) as date'))
                      ->distinct()
                      ->count();
    }

    protected function getAchievementCompletionRate()
    {
        $totalAchievements = Achievement::active()->count();
        $totalCompletions = UserAchievement::where('completed', true)->count();
        
        return $totalAchievements > 0 ? ($totalCompletions / $totalAchievements) * 100 : 0;
    }

    protected function getPopularAchievements()
    {
        return Achievement::withCount(['userAchievements as completion_count' => function ($query) {
                               $query->where('completed', true);
                           }])
                           ->orderBy('completion_count', 'desc')
                           ->limit(5)
                           ->get(['id', 'name', 'description', 'completion_count']);
    }

    protected function getDailyXpActivity($dateRange)
    {
        return XpEvent::whereBetween('created_at', $dateRange)
                      ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(final_xp) as total_xp'))
                      ->groupBy('date')
                      ->orderBy('date')
                      ->get()
                      ->mapWithKeys(function ($item) {
                          return [$item->date => $item->total_xp];
                      });
    }

    protected function getTopXpSources($dateRange)
    {
        return XpEvent::whereBetween('created_at', $dateRange)
                      ->select('event_type', DB::raw('SUM(final_xp) as total_xp'), DB::raw('COUNT(*) as count'))
                      ->groupBy('event_type')
                      ->orderBy('total_xp', 'desc')
                      ->limit(10)
                      ->get()
                      ->map(function ($item) {
                          return [
                              'event_type' => $item->event_type,
                              'event_type_label' => XpEvent::getEventTypes()[$item->event_type] ?? $item->event_type,
                              'total_xp' => $item->total_xp,
                              'count' => $item->count,
                              'average_xp' => $item->count > 0 ? $item->total_xp / $item->count : 0
                          ];
                      });
    }

    protected function parseDateRange($period)
    {
        switch ($period) {
            case '7d':
                return [now()->subDays(7), now()];
            case '30d':
                return [now()->subDays(30), now()];
            case '90d':
                return [now()->subDays(90), now()];
            case '1y':
                return [now()->subYear(), now()];
            case 'all_time':
                return [now()->subYears(10), now()];
            default:
                return [now()->subDays(30), now()];
        }
    }
}
