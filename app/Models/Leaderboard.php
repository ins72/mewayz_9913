<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Leaderboard extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'category',
        'timeframe',
        'metric',
        'calculation_method',
        'min_entries',
        'max_entries',
        'is_active',
        'is_public',
        'reset_frequency',
        'last_reset',
        'next_reset',
        'settings',
        'metadata'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_public' => 'boolean',
        'last_reset' => 'datetime',
        'next_reset' => 'datetime',
        'settings' => 'array',
        'metadata' => 'array',
        'min_entries' => 'integer',
        'max_entries' => 'integer'
    ];

    // Leaderboard types
    const TYPES = [
        'global' => 'Global',
        'regional' => 'Regional',
        'workspace' => 'Workspace',
        'category' => 'Category',
        'seasonal' => 'Seasonal',
        'realtime' => 'Real-time',
        'historical' => 'Historical',
        'predictive' => 'Predictive'
    ];

    // Timeframes
    const TIMEFRAMES = [
        'daily' => 'Daily',
        'weekly' => 'Weekly',
        'monthly' => 'Monthly',
        'yearly' => 'Yearly',
        'alltime' => 'All Time',
        'custom' => 'Custom'
    ];

    // Categories
    const CATEGORIES = [
        'xp' => 'Experience Points',
        'level' => 'User Level',
        'achievements' => 'Achievements',
        'social_influence' => 'Social Media Influence',
        'content_creation' => 'Content Creation',
        'ecommerce_success' => 'E-commerce Success',
        'community_building' => 'Community Building',
        'learning_progress' => 'Learning Progress',
        'collaboration' => 'Collaboration',
        'innovation' => 'Innovation',
        'mentorship' => 'Mentorship',
        'customer_satisfaction' => 'Customer Satisfaction',
        'revenue_generation' => 'Revenue Generation',
        'cross_platform_growth' => 'Cross-platform Growth',
        'analytics_insights' => 'Analytics Insights'
    ];

    /**
     * Get leaderboard entries
     */
    public function entries()
    {
        return $this->hasMany(LeaderboardEntry::class);
    }

    /**
     * Get current leaderboard rankings
     */
    public function getCurrentRankings($limit = 100)
    {
        return $this->entries()
            ->with('user')
            ->orderBy('rank', 'asc')
            ->limit($limit)
            ->get()
            ->map(function ($entry) {
                return [
                    'rank' => $entry->rank,
                    'previous_rank' => $entry->previous_rank,
                    'user' => [
                        'id' => $entry->user->id,
                        'name' => $entry->user->name,
                        'username' => $entry->user->username ?? $entry->user->email,
                        'avatar' => $entry->user->avatar,
                        'is_verified' => $entry->user->is_verified ?? false,
                        'is_premium' => $entry->user->is_premium ?? false,
                        'custom_title' => $entry->user->custom_title,
                        'region' => $entry->user->region,
                        'joined_at' => $entry->user->created_at
                    ],
                    'score' => $entry->score,
                    'previous_score' => $entry->previous_score,
                    'change' => $entry->score - $entry->previous_score,
                    'change_percentage' => $entry->previous_score > 0 
                        ? (($entry->score - $entry->previous_score) / $entry->previous_score) * 100 
                        : 0,
                    'achievements' => $entry->achievements ?? [],
                    'badges' => $entry->badges ?? [],
                    'specializations' => $entry->specializations ?? [],
                    'streak_summary' => $entry->streak_summary ?? [],
                    'social_metrics' => $entry->social_metrics ?? [],
                    'last_active' => $entry->last_active
                ];
            });
    }

    /**
     * Update leaderboard rankings
     */
    public function updateRankings()
    {
        $users = $this->calculateScores();
        
        // Clear existing entries
        $this->entries()->delete();
        
        // Create new entries
        foreach ($users as $index => $userData) {
            LeaderboardEntry::create([
                'leaderboard_id' => $this->id,
                'user_id' => $userData['user_id'],
                'rank' => $index + 1,
                'previous_rank' => $userData['previous_rank'] ?? $index + 1,
                'score' => $userData['score'],
                'previous_score' => $userData['previous_score'] ?? 0,
                'achievements' => $userData['achievements'] ?? [],
                'badges' => $userData['badges'] ?? [],
                'specializations' => $userData['specializations'] ?? [],
                'streak_summary' => $userData['streak_summary'] ?? [],
                'social_metrics' => $userData['social_metrics'] ?? [],
                'last_active' => $userData['last_active'] ?? now(),
                'metadata' => $userData['metadata'] ?? []
            ]);
        }
        
        $this->update(['last_reset' => now()]);
    }

    /**
     * Calculate scores for all users
     */
    private function calculateScores()
    {
        $users = User::with(['userProgress', 'achievements'])->get();
        $scores = [];
        
        foreach ($users as $user) {
            $score = $this->calculateUserScore($user);
            
            if ($score > 0) {
                $scores[] = [
                    'user_id' => $user->id,
                    'score' => $score,
                    'previous_score' => $this->getUserPreviousScore($user),
                    'previous_rank' => $this->getUserPreviousRank($user),
                    'achievements' => $user->achievements->pluck('slug')->toArray(),
                    'badges' => $user->badges ?? [],
                    'specializations' => $user->userProgress->specializations ?? [],
                    'streak_summary' => $this->getUserStreakSummary($user),
                    'social_metrics' => $this->getUserSocialMetrics($user),
                    'last_active' => $user->last_login_at ?? $user->updated_at,
                    'metadata' => $this->getUserMetadata($user)
                ];
            }
        }
        
        // Sort by score descending
        usort($scores, function ($a, $b) {
            return $b['score'] - $a['score'];
        });
        
        return $scores;
    }

    /**
     * Calculate score for a specific user
     */
    private function calculateUserScore($user)
    {
        $score = 0;
        $userProgress = $user->userProgress;
        
        if (!$userProgress) {
            return 0;
        }
        
        switch ($this->category) {
            case 'xp':
                $score = $this->calculateXPScore($userProgress);
                break;
            case 'level':
                $score = $this->calculateLevelScore($userProgress);
                break;
            case 'achievements':
                $score = $this->calculateAchievementScore($user);
                break;
            case 'social_influence':
                $score = $this->calculateSocialInfluenceScore($user);
                break;
            case 'content_creation':
                $score = $this->calculateContentCreationScore($user);
                break;
            case 'ecommerce_success':
                $score = $this->calculateEcommerceScore($user);
                break;
            case 'community_building':
                $score = $this->calculateCommunityScore($user);
                break;
            case 'learning_progress':
                $score = $this->calculateLearningScore($user);
                break;
            case 'collaboration':
                $score = $this->calculateCollaborationScore($user);
                break;
            case 'innovation':
                $score = $this->calculateInnovationScore($user);
                break;
            default:
                $score = $userProgress->total_xp;
        }
        
        return $score;
    }

    /**
     * Calculate XP-based score
     */
    private function calculateXPScore($userProgress)
    {
        switch ($this->timeframe) {
            case 'daily':
                return $userProgress->daily_xp;
            case 'weekly':
                return $userProgress->weekly_xp;
            case 'monthly':
                return $userProgress->monthly_xp;
            case 'yearly':
                return $userProgress->yearly_xp;
            default:
                return $userProgress->total_xp;
        }
    }

    /**
     * Calculate level-based score
     */
    private function calculateLevelScore($userProgress)
    {
        return $userProgress->current_level * 1000 + $userProgress->current_xp;
    }

    /**
     * Calculate achievement-based score
     */
    private function calculateAchievementScore($user)
    {
        $score = 0;
        $achievements = $user->achievements;
        
        foreach ($achievements as $achievement) {
            $tierMultiplier = [
                'starter' => 1,
                'bronze' => 2,
                'silver' => 3,
                'gold' => 5,
                'platinum' => 8,
                'diamond' => 13,
                'legendary' => 21,
                'mythical' => 34
            ];
            
            $multiplier = $tierMultiplier[$achievement->tier] ?? 1;
            $score += 100 * $multiplier;
        }
        
        return $score;
    }

    /**
     * Calculate social influence score
     */
    private function calculateSocialInfluenceScore($user)
    {
        $score = 0;
        
        // Get social media metrics
        $socialAccounts = $user->socialMediaAccounts;
        foreach ($socialAccounts as $account) {
            $score += $account->followers_count * 0.1;
            $score += $account->engagement_rate * 100;
        }
        
        // Add bio site metrics
        $bioSites = $user->bioSites;
        foreach ($bioSites as $site) {
            $score += $site->view_count * 0.01;
            $score += $site->click_count * 0.1;
        }
        
        return $score;
    }

    /**
     * Calculate content creation score
     */
    private function calculateContentCreationScore($user)
    {
        $score = 0;
        
        // Count various content types
        $posts = $user->posts()->count();
        $courses = $user->courses()->count();
        $articles = $user->articles()->count();
        
        $score += $posts * 10;
        $score += $courses * 100;
        $score += $articles * 50;
        
        return $score;
    }

    /**
     * Calculate e-commerce score
     */
    private function calculateEcommerceScore($user)
    {
        $score = 0;
        
        // Get sales data
        $salesEvents = $user->analyticsEvents()
            ->where('event_type', 'sale_completed')
            ->where('created_at', '>=', $this->getTimeframeStart())
            ->get();
        
        foreach ($salesEvents as $event) {
            $score += $event->revenue ?? 0;
        }
        
        return $score;
    }

    /**
     * Calculate community score
     */
    private function calculateCommunityScore($user)
    {
        $score = 0;
        
        // Count community activities
        $forumPosts = $user->forumPosts()->count();
        $helpfulAnswers = $user->helpfulAnswers()->count();
        $mentorships = $user->mentorships()->count();
        
        $score += $forumPosts * 5;
        $score += $helpfulAnswers * 20;
        $score += $mentorships * 100;
        
        return $score;
    }

    /**
     * Calculate learning score
     */
    private function calculateLearningScore($user)
    {
        $score = 0;
        
        // Count learning activities
        $coursesCompleted = $user->coursesCompleted()->count();
        $certificationsEarned = $user->certificationsEarned()->count();
        $skillsLearned = $user->skillsLearned()->count();
        
        $score += $coursesCompleted * 100;
        $score += $certificationsEarned * 500;
        $score += $skillsLearned * 50;
        
        return $score;
    }

    /**
     * Calculate collaboration score
     */
    private function calculateCollaborationScore($user)
    {
        $score = 0;
        
        // Count collaboration activities
        $collaborations = $user->collaborations()->where('status', 'completed')->count();
        $referrals = $user->referrals()->count();
        $teamProjects = $user->teamProjects()->count();
        
        $score += $collaborations * 150;
        $score += $referrals * 50;
        $score += $teamProjects * 200;
        
        return $score;
    }

    /**
     * Calculate innovation score
     */
    private function calculateInnovationScore($user)
    {
        $score = 0;
        
        // Count innovation activities
        $betaFeatures = $user->betaFeaturesUsed()->count();
        $suggestions = $user->featureSuggestions()->count();
        $experiments = $user->experiments()->count();
        
        $score += $betaFeatures * 25;
        $score += $suggestions * 100;
        $score += $experiments * 200;
        
        return $score;
    }

    /**
     * Get previous score for user
     */
    private function getUserPreviousScore($user)
    {
        $previousEntry = $this->entries()
            ->where('user_id', $user->id)
            ->first();
        
        return $previousEntry ? $previousEntry->score : 0;
    }

    /**
     * Get previous rank for user
     */
    private function getUserPreviousRank($user)
    {
        $previousEntry = $this->entries()
            ->where('user_id', $user->id)
            ->first();
        
        return $previousEntry ? $previousEntry->rank : 999999;
    }

    /**
     * Get user streak summary
     */
    private function getUserStreakSummary($user)
    {
        $userProgress = $user->userProgress;
        
        if (!$userProgress || !$userProgress->streaks) {
            return [];
        }
        
        $summary = [];
        foreach ($userProgress->streaks as $type => $streak) {
            $summary[$type] = [
                'current' => $streak['current'],
                'longest' => $streak['longest']
            ];
        }
        
        return $summary;
    }

    /**
     * Get user social metrics
     */
    private function getUserSocialMetrics($user)
    {
        return [
            'followers_count' => $user->followers()->count(),
            'following_count' => $user->following()->count(),
            'posts_count' => $user->posts()->count(),
            'engagement_rate' => $user->calculateEngagementRate(),
            'influence_score' => $user->calculateInfluenceScore()
        ];
    }

    /**
     * Get user metadata
     */
    private function getUserMetadata($user)
    {
        return [
            'country' => $user->country,
            'timezone' => $user->timezone,
            'account_age_days' => $user->created_at->diffInDays(now()),
            'last_login_days_ago' => $user->last_login_at ? $user->last_login_at->diffInDays(now()) : 999,
            'premium_member' => $user->is_premium ?? false,
            'verified_account' => $user->is_verified ?? false
        ];
    }

    /**
     * Get timeframe start date
     */
    private function getTimeframeStart()
    {
        switch ($this->timeframe) {
            case 'daily':
                return Carbon::now()->startOfDay();
            case 'weekly':
                return Carbon::now()->startOfWeek();
            case 'monthly':
                return Carbon::now()->startOfMonth();
            case 'yearly':
                return Carbon::now()->startOfYear();
            default:
                return Carbon::now()->subYear();
        }
    }

    /**
     * Check if leaderboard should be reset
     */
    public function shouldReset()
    {
        if (!$this->reset_frequency || $this->reset_frequency === 'never') {
            return false;
        }
        
        return $this->next_reset && Carbon::now()->gte($this->next_reset);
    }

    /**
     * Set next reset date
     */
    public function setNextReset()
    {
        $nextReset = null;
        
        switch ($this->reset_frequency) {
            case 'daily':
                $nextReset = Carbon::now()->addDay()->startOfDay();
                break;
            case 'weekly':
                $nextReset = Carbon::now()->addWeek()->startOfWeek();
                break;
            case 'monthly':
                $nextReset = Carbon::now()->addMonth()->startOfMonth();
                break;
            case 'yearly':
                $nextReset = Carbon::now()->addYear()->startOfYear();
                break;
        }
        
        if ($nextReset) {
            $this->update(['next_reset' => $nextReset]);
        }
    }

    /**
     * Get user's rank in this leaderboard
     */
    public function getUserRank($userId)
    {
        $entry = $this->entries()
            ->where('user_id', $userId)
            ->first();
        
        return $entry ? $entry->rank : null;
    }

    /**
     * Get user's position data
     */
    public function getUserPosition($userId)
    {
        $entry = $this->entries()
            ->where('user_id', $userId)
            ->first();
        
        if (!$entry) {
            return null;
        }
        
        return [
            'rank' => $entry->rank,
            'previous_rank' => $entry->previous_rank,
            'score' => $entry->score,
            'previous_score' => $entry->previous_score,
            'change' => $entry->score - $entry->previous_score,
            'change_percentage' => $entry->previous_score > 0 
                ? (($entry->score - $entry->previous_score) / $entry->previous_score) * 100 
                : 0
        ];
    }

    /**
     * Scope for active leaderboards
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for public leaderboards
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope for category
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for timeframe
     */
    public function scopeTimeframe($query, $timeframe)
    {
        return $query->where('timeframe', $timeframe);
    }
}