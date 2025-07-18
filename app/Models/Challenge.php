<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Challenge extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'story',
        'type',
        'difficulty',
        'category',
        'requirements',
        'rewards',
        'penalties',
        'start_date',
        'end_date',
        'participant_limit',
        'current_participants',
        'completion_rate',
        'average_time',
        'is_team_challenge',
        'team_size',
        'is_ranked',
        'prerequisites',
        'exclusions',
        'dynamic_difficulty',
        'ai_adaptation',
        'community_voting',
        'mentorship_required',
        'cross_platform_integration',
        'is_active',
        'is_featured',
        'creator_id',
        'settings',
        'metadata'
    ];

    protected $casts = [
        'requirements' => 'array',
        'rewards' => 'array',
        'penalties' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'prerequisites' => 'array',
        'exclusions' => 'array',
        'settings' => 'array',
        'metadata' => 'array',
        'is_team_challenge' => 'boolean',
        'is_ranked' => 'boolean',
        'dynamic_difficulty' => 'boolean',
        'ai_adaptation' => 'boolean',
        'community_voting' => 'boolean',
        'mentorship_required' => 'boolean',
        'cross_platform_integration' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'participant_limit' => 'integer',
        'current_participants' => 'integer',
        'completion_rate' => 'decimal:2',
        'average_time' => 'decimal:2',
        'team_size' => 'integer'
    ];

    // Challenge types
    const TYPES = [
        'daily' => 'Daily',
        'weekly' => 'Weekly',
        'monthly' => 'Monthly',
        'seasonal' => 'Seasonal',
        'special' => 'Special',
        'community' => 'Community',
        'personal' => 'Personal',
        'competitive' => 'Competitive'
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

    // Categories
    const CATEGORIES = [
        'content_creation' => 'Content Creation',
        'social_media' => 'Social Media',
        'ecommerce' => 'E-commerce',
        'learning' => 'Learning',
        'community' => 'Community',
        'collaboration' => 'Collaboration',
        'innovation' => 'Innovation',
        'wellness' => 'Wellness',
        'productivity' => 'Productivity',
        'creativity' => 'Creativity'
    ];

    /**
     * Get the creator of this challenge
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Get challenge participants
     */
    public function participants()
    {
        return $this->belongsToMany(User::class, 'challenge_participants')
                    ->withPivot('joined_at', 'completed_at', 'progress', 'score', 'rank', 'metadata')
                    ->withTimestamps();
    }

    /**
     * Get challenge teams
     */
    public function teams()
    {
        return $this->hasMany(ChallengeTeam::class);
    }

    /**
     * Get challenge leaderboard
     */
    public function leaderboard()
    {
        return $this->participants()
                    ->wherePivot('completed_at', '!=', null)
                    ->orderByPivot('score', 'desc')
                    ->orderByPivot('completed_at', 'asc');
    }

    /**
     * Check if challenge is active
     */
    public function getIsActiveNowAttribute()
    {
        $now = Carbon::now();
        return $this->is_active && 
               $now->gte($this->start_date) && 
               $now->lte($this->end_date);
    }

    /**
     * Check if challenge is upcoming
     */
    public function getIsUpcomingAttribute()
    {
        return $this->is_active && Carbon::now()->lt($this->start_date);
    }

    /**
     * Check if challenge is expired
     */
    public function getIsExpiredAttribute()
    {
        return Carbon::now()->gt($this->end_date);
    }

    /**
     * Get challenge duration in days
     */
    public function getDurationDaysAttribute()
    {
        return $this->start_date->diffInDays($this->end_date);
    }

    /**
     * Get time remaining
     */
    public function getTimeRemainingAttribute()
    {
        if ($this->is_expired) {
            return null;
        }
        
        $now = Carbon::now();
        
        if ($this->is_upcoming) {
            return $now->diffForHumans($this->start_date, true);
        }
        
        return $now->diffForHumans($this->end_date, true);
    }

    /**
     * Check if user can join challenge
     */
    public function canBeJoinedBy(User $user)
    {
        // Check if challenge is active
        if (!$this->is_active_now) {
            return false;
        }
        
        // Check if user already joined
        if ($this->isJoinedBy($user)) {
            return false;
        }
        
        // Check participant limit
        if ($this->participant_limit && $this->current_participants >= $this->participant_limit) {
            return false;
        }
        
        // Check prerequisites
        if (!empty($this->prerequisites)) {
            foreach ($this->prerequisites as $prerequisite) {
                if (!$this->checkPrerequisite($user, $prerequisite)) {
                    return false;
                }
            }
        }
        
        // Check exclusions
        if (!empty($this->exclusions)) {
            foreach ($this->exclusions as $exclusion) {
                if ($this->checkExclusion($user, $exclusion)) {
                    return false;
                }
            }
        }
        
        return true;
    }

    /**
     * Check if user has joined challenge
     */
    public function isJoinedBy(User $user)
    {
        return $this->participants()->where('user_id', $user->id)->exists();
    }

    /**
     * Check if user has completed challenge
     */
    public function isCompletedBy(User $user)
    {
        return $this->participants()
                    ->where('user_id', $user->id)
                    ->wherePivot('completed_at', '!=', null)
                    ->exists();
    }

    /**
     * Add user to challenge
     */
    public function addParticipant(User $user, $teamId = null)
    {
        if (!$this->canBeJoinedBy($user)) {
            return false;
        }
        
        $metadata = [];
        
        if ($this->is_team_challenge && $teamId) {
            $metadata['team_id'] = $teamId;
        }
        
        $this->participants()->attach($user->id, [
            'joined_at' => now(),
            'progress' => 0,
            'score' => 0,
            'metadata' => $metadata
        ]);
        
        $this->increment('current_participants');
        
        // Fire event
        event(new \App\Events\ChallengeJoined($user, $this));
        
        return true;
    }

    /**
     * Remove user from challenge
     */
    public function removeParticipant(User $user)
    {
        $removed = $this->participants()->detach($user->id);
        
        if ($removed) {
            $this->decrement('current_participants');
            
            // Fire event
            event(new \App\Events\ChallengeLeft($user, $this));
        }
        
        return $removed > 0;
    }

    /**
     * Update user progress
     */
    public function updateProgress(User $user, $progress, $score = null, $metadata = [])
    {
        if (!$this->isJoinedBy($user)) {
            return false;
        }
        
        $updateData = [
            'progress' => $progress,
            'metadata' => $metadata
        ];
        
        if ($score !== null) {
            $updateData['score'] = $score;
        }
        
        // Check if challenge is completed
        if ($progress >= 100) {
            $updateData['completed_at'] = now();
            
            // Award completion rewards
            $this->awardRewards($user);
            
            // Update challenge stats
            $this->updateCompletionStats();
            
            // Fire completion event
            event(new \App\Events\ChallengeCompleted($user, $this));
        }
        
        $this->participants()->updateExistingPivot($user->id, $updateData);
        
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
                    $user->addXP($reward['value'], 'challenge_completed');
                    break;
                case 'credits':
                    $user->addCredits($reward['value'], 'challenge_reward');
                    break;
                case 'badge':
                    $user->awardBadge($reward['value']);
                    break;
                case 'achievement':
                    $achievement = Achievement::where('slug', $reward['value'])->first();
                    if ($achievement) {
                        $achievement->awardTo($user);
                    }
                    break;
                case 'premium_time':
                    $user->extendPremium($reward['value']);
                    break;
            }
        }
    }

    /**
     * Update completion statistics
     */
    private function updateCompletionStats()
    {
        $totalParticipants = $this->participants()->count();
        $completedParticipants = $this->participants()
                                      ->wherePivot('completed_at', '!=', null)
                                      ->count();
        
        $completionRate = $totalParticipants > 0 ? ($completedParticipants / $totalParticipants) * 100 : 0;
        
        $this->update(['completion_rate' => $completionRate]);
    }

    /**
     * Get user's progress in challenge
     */
    public function getProgressFor(User $user)
    {
        $participant = $this->participants()
                           ->where('user_id', $user->id)
                           ->first();
        
        if (!$participant) {
            return null;
        }
        
        return [
            'progress' => $participant->pivot->progress,
            'score' => $participant->pivot->score,
            'rank' => $this->getUserRank($user),
            'joined_at' => $participant->pivot->joined_at,
            'completed_at' => $participant->pivot->completed_at,
            'metadata' => $participant->pivot->metadata
        ];
    }

    /**
     * Get user's rank in challenge
     */
    public function getUserRank(User $user)
    {
        $userScore = $this->participants()
                          ->where('user_id', $user->id)
                          ->first();
        
        if (!$userScore) {
            return null;
        }
        
        $rank = $this->participants()
                     ->where('score', '>', $userScore->pivot->score)
                     ->count() + 1;
        
        return $rank;
    }

    /**
     * Get top performers
     */
    public function getTopPerformers($limit = 10)
    {
        return $this->participants()
                    ->orderByPivot('score', 'desc')
                    ->orderByPivot('completed_at', 'asc')
                    ->limit($limit)
                    ->get()
                    ->map(function ($user) {
                        return [
                            'user' => $user,
                            'score' => $user->pivot->score,
                            'progress' => $user->pivot->progress,
                            'completed_at' => $user->pivot->completed_at,
                            'rank' => $this->getUserRank($user)
                        ];
                    });
    }

    /**
     * Check prerequisite
     */
    private function checkPrerequisite(User $user, $prerequisite)
    {
        switch ($prerequisite['type']) {
            case 'level':
                return $user->userProgress && $user->userProgress->current_level >= $prerequisite['value'];
            case 'achievement':
                return $user->achievements()->where('slug', $prerequisite['value'])->exists();
            case 'challenge':
                return $user->challenges()
                           ->where('slug', $prerequisite['value'])
                           ->wherePivot('completed_at', '!=', null)
                           ->exists();
            case 'xp':
                return $user->userProgress && $user->userProgress->total_xp >= $prerequisite['value'];
            default:
                return true;
        }
    }

    /**
     * Check exclusion
     */
    private function checkExclusion(User $user, $exclusion)
    {
        switch ($exclusion['type']) {
            case 'challenge':
                return $user->challenges()
                           ->where('slug', $exclusion['value'])
                           ->wherePivot('completed_at', '!=', null)
                           ->exists();
            case 'achievement':
                return $user->achievements()->where('slug', $exclusion['value'])->exists();
            default:
                return false;
        }
    }

    /**
     * Get difficulty color
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
     * Get participation percentage
     */
    public function getParticipationPercentageAttribute()
    {
        if (!$this->participant_limit) {
            return 0;
        }
        
        return ($this->current_participants / $this->participant_limit) * 100;
    }

    /**
     * Scope for active challenges
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ongoing challenges
     */
    public function scopeOngoing($query)
    {
        $now = Carbon::now();
        return $query->where('is_active', true)
                    ->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now);
    }

    /**
     * Scope for upcoming challenges
     */
    public function scopeUpcoming($query)
    {
        return $query->where('is_active', true)
                    ->where('start_date', '>', Carbon::now());
    }

    /**
     * Scope for category
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for type
     */
    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for difficulty
     */
    public function scopeDifficulty($query, $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }

    /**
     * Scope for featured challenges
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}