<?php

namespace App\Models\Gamification;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\User;

class Achievement extends Model
{
    protected $table = 'gamification_achievements';
    
    protected $fillable = [
        'name', 'slug', 'description', 'icon', 'badge_color', 'type', 'category',
        'difficulty', 'points', 'requirements', 'rewards', 'is_active', 'is_repeatable',
        'max_completions', 'unlock_condition', 'sort_order'
    ];

    protected $casts = [
        'requirements' => 'array',
        'rewards' => 'array',
        'is_active' => 'boolean',
        'is_repeatable' => 'boolean',
        'max_completions' => 'integer',
        'points' => 'integer',
        'sort_order' => 'integer'
    ];

    public function userAchievements(): HasMany
    {
        return $this->hasMany(UserAchievement::class, 'achievement_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'gamification_user_achievements', 'achievement_id', 'user_id')
                    ->withPivot(['progress', 'target', 'completed', 'completed_at', 'completion_count', 'progress_data'])
                    ->withTimestamps();
    }

    public function completedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'gamification_user_achievements', 'achievement_id', 'user_id')
                    ->wherePivot('completed', true)
                    ->withPivot(['progress', 'target', 'completed', 'completed_at', 'completion_count', 'progress_data'])
                    ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByDifficulty($query, $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }

    public function getProgressForUser(User $user)
    {
        $userAchievement = $this->userAchievements()->where('user_id', $user->id)->first();
        
        if (!$userAchievement) {
            return [
                'progress' => 0,
                'target' => $this->getTargetValue(),
                'completed' => false,
                'percentage' => 0
            ];
        }

        $percentage = $userAchievement->target > 0 ? ($userAchievement->progress / $userAchievement->target) * 100 : 0;

        return [
            'progress' => $userAchievement->progress,
            'target' => $userAchievement->target,
            'completed' => $userAchievement->completed,
            'percentage' => min(100, round($percentage, 2)),
            'completed_at' => $userAchievement->completed_at,
            'completion_count' => $userAchievement->completion_count
        ];
    }

    public function getTargetValue()
    {
        $requirements = $this->requirements;
        return $requirements['target'] ?? 1;
    }

    public function checkRequirements(User $user, $eventData = [])
    {
        $requirements = $this->requirements;
        
        if (!$requirements) {
            return true;
        }

        // Check unlock conditions
        if ($this->unlock_condition) {
            $unlockAchievement = static::where('slug', $this->unlock_condition)->first();
            if ($unlockAchievement && !$user->hasCompletedAchievement($unlockAchievement)) {
                return false;
            }
        }

        // Check level requirements
        if (isset($requirements['min_level'])) {
            $userLevel = $user->gamificationLevel();
            if (!$userLevel || $userLevel->level < $requirements['min_level']) {
                return false;
            }
        }

        return true;
    }

    public function canBeAwarded(User $user)
    {
        if (!$this->is_active) {
            return false;
        }

        if (!$this->checkRequirements($user)) {
            return false;
        }

        if (!$this->is_repeatable) {
            return !$user->hasCompletedAchievement($this);
        }

        if ($this->max_completions) {
            $completionCount = $user->getAchievementCompletionCount($this);
            return $completionCount < $this->max_completions;
        }

        return true;
    }

    public function getDifficultyColor()
    {
        return match($this->difficulty) {
            'easy' => '#10B981',
            'medium' => '#F59E0B',
            'hard' => '#EF4444',
            'legendary' => '#8B5CF6',
            default => '#6B7280'
        };
    }

    public function getCompletionStats()
    {
        $total = $this->userAchievements()->count();
        $completed = $this->userAchievements()->where('completed', true)->count();
        $completionRate = $total > 0 ? ($completed / $total) * 100 : 0;

        return [
            'total_attempts' => $total,
            'completed_count' => $completed,
            'completion_rate' => round($completionRate, 2),
            'average_progress' => $this->userAchievements()->avg('progress') ?? 0
        ];
    }
}