<?php

namespace App\Models\Gamification;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use Carbon\Carbon;

class Streak extends Model
{
    protected $table = 'gamification_streaks';
    
    protected $fillable = [
        'user_id', 'streak_type', 'current_streak', 'longest_streak', 'total_completions',
        'last_activity_date', 'streak_start_date', 'is_active', 'streak_data',
        'streak_multiplier', 'milestones'
    ];

    protected $casts = [
        'current_streak' => 'integer',
        'longest_streak' => 'integer',
        'total_completions' => 'integer',
        'last_activity_date' => 'date',
        'streak_start_date' => 'date',
        'is_active' => 'boolean',
        'streak_data' => 'array',
        'streak_multiplier' => 'integer',
        'milestones' => 'array'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('streak_type', $type);
    }

    public function updateStreak($activityDate = null)
    {
        $activityDate = $activityDate ? Carbon::parse($activityDate)->toDateString() : now()->toDateString();
        $lastActivityDate = $this->last_activity_date ? Carbon::parse($this->last_activity_date) : null;
        
        // If first activity or continuing streak
        if (!$lastActivityDate || $this->canContinueStreak($activityDate, $lastActivityDate)) {
            $this->incrementStreak($activityDate);
        } else {
            // Streak broken, reset
            $this->resetStreak($activityDate);
        }
        
        $this->save();
        
        // Check for milestone achievements
        $this->checkMilestones();
        
        return $this;
    }

    public function canContinueStreak($activityDate, $lastActivityDate)
    {
        $activityCarbon = Carbon::parse($activityDate);
        $lastActivityCarbon = Carbon::parse($lastActivityDate);
        
        // Check if activity is consecutive based on streak type
        switch ($this->streak_type) {
            case 'daily_login':
                return $activityCarbon->diffInDays($lastActivityCarbon) <= 1;
            case 'weekly_post':
                return $activityCarbon->diffInWeeks($lastActivityCarbon) <= 1;
            case 'monthly_course':
                return $activityCarbon->diffInMonths($lastActivityCarbon) <= 1;
            default:
                return $activityCarbon->diffInDays($lastActivityCarbon) <= 1;
        }
    }

    public function incrementStreak($activityDate)
    {
        $this->current_streak++;
        $this->total_completions++;
        $this->last_activity_date = $activityDate;
        
        if (!$this->streak_start_date) {
            $this->streak_start_date = $activityDate;
        }
        
        if ($this->current_streak > $this->longest_streak) {
            $this->longest_streak = $this->current_streak;
        }
        
        // Award XP for streak continuation
        $baseXp = $this->getBaseXp();
        $streakBonus = min(50, $this->current_streak * 2); // Cap bonus at 50
        $totalXp = $baseXp + $streakBonus;
        
        $this->user->addXp($totalXp, 'streak_activity', [
            'streak_type' => $this->streak_type,
            'current_streak' => $this->current_streak,
            'bonus_xp' => $streakBonus,
            'category' => 'engagement'
        ]);
        
        return $this;
    }

    public function resetStreak($activityDate)
    {
        $this->current_streak = 1;
        $this->total_completions++;
        $this->last_activity_date = $activityDate;
        $this->streak_start_date = $activityDate;
        
        // Award base XP for activity (but no streak bonus)
        $baseXp = $this->getBaseXp();
        $this->user->addXp($baseXp, 'streak_activity', [
            'streak_type' => $this->streak_type,
            'current_streak' => $this->current_streak,
            'streak_reset' => true,
            'category' => 'engagement'
        ]);
        
        return $this;
    }

    public function breakStreak()
    {
        $this->current_streak = 0;
        $this->is_active = false;
        $this->save();
        
        return $this;
    }

    public function checkMilestones()
    {
        $milestones = $this->getMilestones();
        
        foreach ($milestones as $milestone) {
            if ($this->current_streak >= $milestone['streak'] && !$this->hasMilestone($milestone['streak'])) {
                $this->awardMilestone($milestone);
            }
        }
        
        return $this;
    }

    public function hasMilestone($streakValue)
    {
        $milestones = $this->milestones ?? [];
        return in_array($streakValue, $milestones);
    }

    public function awardMilestone($milestone)
    {
        $milestones = $this->milestones ?? [];
        $milestones[] = $milestone['streak'];
        $this->milestones = $milestones;
        $this->save();
        
        // Award milestone XP
        $this->user->addXp($milestone['xp'], 'streak_milestone', [
            'streak_type' => $this->streak_type,
            'milestone_streak' => $milestone['streak'],
            'milestone_name' => $milestone['name'],
            'category' => 'milestone'
        ]);
        
        return $this;
    }

    public function getBaseXp()
    {
        $xpMapping = [
            'daily_login' => 5,
            'weekly_post' => 25,
            'monthly_course' => 100,
            'email_campaign' => 50,
            'social_engagement' => 15,
            'bio_site_update' => 20
        ];
        
        return $xpMapping[$this->streak_type] ?? 10;
    }

    public function getMilestones()
    {
        return [
            ['streak' => 3, 'name' => 'Getting Started', 'xp' => 50],
            ['streak' => 7, 'name' => 'Week Warrior', 'xp' => 100],
            ['streak' => 14, 'name' => 'Two Week Champion', 'xp' => 200],
            ['streak' => 30, 'name' => 'Monthly Master', 'xp' => 500],
            ['streak' => 60, 'name' => 'Dedication Expert', 'xp' => 1000],
            ['streak' => 100, 'name' => 'Century Achiever', 'xp' => 2000],
            ['streak' => 365, 'name' => 'Year Long Legend', 'xp' => 5000]
        ];
    }

    public function getStreakTypes()
    {
        return [
            'daily_login' => 'Daily Login',
            'weekly_post' => 'Weekly Post',
            'monthly_course' => 'Monthly Course',
            'email_campaign' => 'Email Campaign',
            'social_engagement' => 'Social Engagement',
            'bio_site_update' => 'Bio Site Update'
        ];
    }

    public function getStreakTypeLabel()
    {
        $types = $this->getStreakTypes();
        return $types[$this->streak_type] ?? ucfirst(str_replace('_', ' ', $this->streak_type));
    }

    public function getStreakStatus()
    {
        if (!$this->is_active) {
            return 'inactive';
        }
        
        if (!$this->last_activity_date) {
            return 'new';
        }
        
        $daysSinceActivity = Carbon::parse($this->last_activity_date)->diffInDays(now());
        
        if ($daysSinceActivity > 1) {
            return 'broken';
        }
        
        if ($daysSinceActivity === 1) {
            return 'at_risk';
        }
        
        return 'active';
    }

    public function getNextMilestone()
    {
        $milestones = $this->getMilestones();
        
        foreach ($milestones as $milestone) {
            if ($this->current_streak < $milestone['streak']) {
                return $milestone;
            }
        }
        
        return null;
    }
}