<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'flavor_text',
        'category',
        'tier',
        'requirements',
        'rewards',
        'icon',
        'animated_icon',
        'rarity',
        'is_hidden',
        'is_secret',
        'prerequisite_achievements',
        'mutually_exclusive',
        'seasonal_availability',
        'completion_rate',
        'first_earner',
        'earned_count',
        'tags',
        'difficulty',
        'estimated_time',
        'ai_difficulty',
        'community_rating',
        'related_achievements',
        'is_active',
        'metadata'
    ];

    protected $casts = [
        'requirements' => 'array',
        'rewards' => 'array',
        'prerequisite_achievements' => 'array',
        'mutually_exclusive' => 'array',
        'seasonal_availability' => 'array',
        'tags' => 'array',
        'related_achievements' => 'array',
        'is_hidden' => 'boolean',
        'is_secret' => 'boolean',
        'is_active' => 'boolean',
        'completion_rate' => 'decimal:2',
        'estimated_time' => 'integer',
        'ai_difficulty' => 'decimal:2',
        'community_rating' => 'decimal:2',
        'earned_count' => 'integer',
        'rarity' => 'integer',
        'metadata' => 'array'
    ];

    // Achievement categories
    const CATEGORIES = [
        'social' => 'Social Media Mastery',
        'content' => 'Content Creation Excellence',
        'commerce' => 'E-commerce Tycoon',
        'learning' => 'Learning & Development',
        'community' => 'Community & Collaboration',
        'platform' => 'Platform Pioneer',
        'collaboration' => 'Collaboration Master',
        'innovation' => 'Innovation & Creativity'
    ];

    // Achievement tiers
    const TIERS = [
        'starter' => 'Starter',
        'bronze' => 'Bronze',
        'silver' => 'Silver',
        'gold' => 'Gold',
        'platinum' => 'Platinum',
        'diamond' => 'Diamond',
        'legendary' => 'Legendary',
        'mythical' => 'Mythical'
    ];

    // Difficulty levels
    const DIFFICULTIES = [
        'trivial' => 'Trivial',
        'easy' => 'Easy',
        'medium' => 'Medium',
        'hard' => 'Hard',
        'expert' => 'Expert',
        'legendary' => 'Legendary',
        'impossible' => 'Impossible'
    ];

    /**
     * Get users who have earned this achievement
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_achievements')
                    ->withPivot('earned_at', 'progress', 'metadata')
                    ->withTimestamps();
    }

    /**
     * Get achievement requirements
     */
    public function getRequirementsAttribute($value)
    {
        $requirements = json_decode($value, true) ?? [];
        return collect($requirements)->map(function ($requirement) {
            return [
                'type' => $requirement['type'] ?? 'count',
                'metric' => $requirement['metric'] ?? '',
                'value' => $requirement['value'] ?? 0,
                'timeframe' => $requirement['timeframe'] ?? 'alltime',
                'conditions' => $requirement['conditions'] ?? [],
                'social_requirements' => $requirement['social_requirements'] ?? [],
                'quality_threshold' => $requirement['quality_threshold'] ?? []
            ];
        })->toArray();
    }

    /**
     * Get achievement rewards
     */
    public function getRewardsAttribute($value)
    {
        $rewards = json_decode($value, true) ?? [];
        return collect($rewards)->map(function ($reward) {
            return [
                'type' => $reward['type'] ?? 'xp',
                'value' => $reward['value'] ?? 0,
                'description' => $reward['description'] ?? '',
                'rarity' => $reward['rarity'] ?? 'common',
                'is_one_time' => $reward['is_one_time'] ?? true,
                'conditions' => $reward['conditions'] ?? []
            ];
        })->toArray();
    }

    /**
     * Check if user can earn this achievement
     */
    public function canBeEarnedBy(User $user)
    {
        // Check if already earned
        if ($this->isEarnedBy($user)) {
            return false;
        }

        // Check prerequisites
        if (!empty($this->prerequisite_achievements)) {
            $earnedPrerequisites = $user->achievements()
                ->whereIn('slug', $this->prerequisite_achievements)
                ->count();
            
            if ($earnedPrerequisites < count($this->prerequisite_achievements)) {
                return false;
            }
        }

        // Check mutually exclusive
        if (!empty($this->mutually_exclusive)) {
            $hasExclusive = $user->achievements()
                ->whereIn('slug', $this->mutually_exclusive)
                ->exists();
            
            if ($hasExclusive) {
                return false;
            }
        }

        // Check seasonal availability
        if (!empty($this->seasonal_availability)) {
            $now = Carbon::now();
            $startDate = Carbon::parse($this->seasonal_availability['start_date']);
            $endDate = Carbon::parse($this->seasonal_availability['end_date']);
            
            if ($now->lt($startDate) || $now->gt($endDate)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if user has earned this achievement
     */
    public function isEarnedBy(User $user)
    {
        return $user->achievements()->where('achievement_id', $this->id)->exists();
    }

    /**
     * Check user progress towards this achievement
     */
    public function getProgressFor(User $user)
    {
        $progress = [];
        
        foreach ($this->requirements as $requirement) {
            $currentValue = $this->calculateRequirementProgress($user, $requirement);
            $targetValue = $requirement['value'];
            
            $progress[] = [
                'requirement' => $requirement,
                'current' => $currentValue,
                'target' => $targetValue,
                'percentage' => $targetValue > 0 ? min(100, ($currentValue / $targetValue) * 100) : 0,
                'completed' => $currentValue >= $targetValue
            ];
        }
        
        return $progress;
    }

    /**
     * Calculate progress for a specific requirement
     */
    private function calculateRequirementProgress(User $user, $requirement)
    {
        $type = $requirement['type'];
        $metric = $requirement['metric'];
        $timeframe = $requirement['timeframe'] ?? 'alltime';
        
        switch ($type) {
            case 'count':
                return $this->calculateCountProgress($user, $metric, $timeframe);
            case 'streak':
                return $this->calculateStreakProgress($user, $metric);
            case 'quality':
                return $this->calculateQualityProgress($user, $metric, $timeframe);
            case 'social':
                return $this->calculateSocialProgress($user, $metric);
            case 'financial':
                return $this->calculateFinancialProgress($user, $metric, $timeframe);
            default:
                return 0;
        }
    }

    /**
     * Calculate count-based progress
     */
    private function calculateCountProgress(User $user, $metric, $timeframe)
    {
        $query = $user->analyticsEvents();
        
        // Apply timeframe filter
        if ($timeframe !== 'alltime') {
            $query->where('created_at', '>=', $this->getTimeframeStart($timeframe));
        }
        
        switch ($metric) {
            case 'posts_created':
                return $query->where('event_type', 'post_created')->count();
            case 'courses_completed':
                return $query->where('event_type', 'course_completed')->count();
            case 'sales_made':
                return $query->where('event_type', 'sale_completed')->count();
            case 'followers_gained':
                return $query->where('event_type', 'follower_gained')->count();
            default:
                return 0;
        }
    }

    /**
     * Calculate streak-based progress
     */
    private function calculateStreakProgress(User $user, $metric)
    {
        $userProgress = $user->userProgress()->first();
        
        if (!$userProgress) {
            return 0;
        }
        
        $streaks = $userProgress->streaks ?? [];
        
        switch ($metric) {
            case 'daily_login':
                return $streaks['daily_login']['current'] ?? 0;
            case 'content_creation':
                return $streaks['content_creation']['current'] ?? 0;
            case 'sales_performance':
                return $streaks['sales_performance']['current'] ?? 0;
            default:
                return 0;
        }
    }

    /**
     * Calculate quality-based progress
     */
    private function calculateQualityProgress(User $user, $metric, $timeframe)
    {
        switch ($metric) {
            case 'average_rating':
                return $user->reviews()->avg('rating') ?? 0;
            case 'satisfaction_score':
                return $user->customerSatisfactionScore() ?? 0;
            case 'engagement_rate':
                return $user->calculateEngagementRate($timeframe) ?? 0;
            default:
                return 0;
        }
    }

    /**
     * Calculate social-based progress
     */
    private function calculateSocialProgress(User $user, $metric)
    {
        switch ($metric) {
            case 'followers_count':
                return $user->followers()->count();
            case 'referrals_made':
                return $user->referrals()->count();
            case 'collaborations_completed':
                return $user->collaborations()->where('status', 'completed')->count();
            default:
                return 0;
        }
    }

    /**
     * Calculate financial-based progress
     */
    private function calculateFinancialProgress(User $user, $metric, $timeframe)
    {
        $query = $user->analyticsEvents()->where('event_category', 'financial');
        
        if ($timeframe !== 'alltime') {
            $query->where('created_at', '>=', $this->getTimeframeStart($timeframe));
        }
        
        switch ($metric) {
            case 'revenue_generated':
                return $query->sum('revenue') ?? 0;
            case 'products_sold':
                return $query->where('event_type', 'product_sold')->count();
            case 'subscription_revenue':
                return $query->where('event_type', 'subscription_payment')->sum('revenue') ?? 0;
            default:
                return 0;
        }
    }

    /**
     * Get timeframe start date
     */
    private function getTimeframeStart($timeframe)
    {
        switch ($timeframe) {
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
     * Award achievement to user
     */
    public function awardTo(User $user, $metadata = [])
    {
        if (!$this->canBeEarnedBy($user)) {
            return false;
        }

        // Create achievement record
        $user->achievements()->attach($this->id, [
            'earned_at' => now(),
            'progress' => 100,
            'metadata' => $metadata
        ]);

        // Update achievement stats
        $this->increment('earned_count');
        
        if ($this->earned_count === 1) {
            $this->update(['first_earner' => $user->id]);
        }

        // Award rewards
        $this->awardRewards($user);

        // Fire achievement earned event
        event(new \App\Events\AchievementEarned($user, $this));

        return true;
    }

    /**
     * Award rewards to user
     */
    private function awardRewards(User $user)
    {
        foreach ($this->rewards as $reward) {
            switch ($reward['type']) {
                case 'xp':
                    $user->addXP($reward['value'], 'achievement_earned');
                    break;
                case 'credits':
                    $user->addCredits($reward['value'], 'achievement_reward');
                    break;
                case 'badge':
                    $user->awardBadge($reward['value']);
                    break;
                case 'premium_time':
                    $user->extendPremium($reward['value']);
                    break;
            }
        }
    }

    /**
     * Get achievement rarity text
     */
    public function getRarityTextAttribute()
    {
        if ($this->rarity <= 10) return 'Legendary';
        if ($this->rarity <= 50) return 'Epic';
        if ($this->rarity <= 100) return 'Rare';
        if ($this->rarity <= 250) return 'Uncommon';
        return 'Common';
    }

    /**
     * Get achievement difficulty color
     */
    public function getDifficultyColorAttribute()
    {
        $colors = [
            'trivial' => '#10B981',
            'easy' => '#3B82F6',
            'medium' => '#F59E0B',
            'hard' => '#EF4444',
            'expert' => '#8B5CF6',
            'legendary' => '#F59E0B',
            'impossible' => '#1F2937'
        ];
        
        return $colors[$this->difficulty] ?? '#6B7280';
    }

    /**
     * Get tier color
     */
    public function getTierColorAttribute()
    {
        $colors = [
            'starter' => '#10B981',
            'bronze' => '#CD7F32',
            'silver' => '#C0C0C0',
            'gold' => '#FFD700',
            'platinum' => '#E5E4E2',
            'diamond' => '#B9F2FF',
            'legendary' => '#FF6B35',
            'mythical' => '#9B59B6'
        ];
        
        return $colors[$this->tier] ?? '#6B7280';
    }

    /**
     * Scope for active achievements
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for visible achievements
     */
    public function scopeVisible($query)
    {
        return $query->where('is_hidden', false);
    }

    /**
     * Scope for category
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for tier
     */
    public function scopeTier($query, $tier)
    {
        return $query->where('tier', $tier);
    }

    /**
     * Scope for difficulty
     */
    public function scopeDifficulty($query, $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }
}