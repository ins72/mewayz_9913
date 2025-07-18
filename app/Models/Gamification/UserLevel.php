<?php

namespace App\Models\Gamification;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class UserLevel extends Model
{
    protected $table = 'gamification_user_levels';
    
    protected $fillable = [
        'user_id', 'level', 'total_xp', 'current_level_xp', 'next_level_xp', 'xp_to_next_level',
        'level_name', 'level_tier', 'level_benefits', 'last_level_up'
    ];

    protected $casts = [
        'level' => 'integer',
        'total_xp' => 'integer',
        'current_level_xp' => 'integer',
        'next_level_xp' => 'integer',
        'xp_to_next_level' => 'integer',
        'level_benefits' => 'array',
        'last_level_up' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function addXp($amount, $eventType = null, $eventData = [])
    {
        $this->total_xp += $amount;
        $this->current_level_xp += $amount;
        
        // Check for level up
        while ($this->current_level_xp >= $this->next_level_xp) {
            $this->levelUp();
        }
        
        $this->updateXpToNextLevel();
        $this->save();
        
        // Record XP event
        XpEvent::create([
            'user_id' => $this->user_id,
            'event_type' => $eventType ?? 'manual',
            'event_category' => $eventData['category'] ?? 'general',
            'xp_amount' => $amount,
            'multiplier' => 1,
            'final_xp' => $amount,
            'source_type' => $eventData['source_type'] ?? null,
            'source_id' => $eventData['source_id'] ?? null,
            'description' => $eventData['description'] ?? null,
            'metadata' => $eventData,
            'is_bonus' => $eventData['is_bonus'] ?? false,
            'bonus_reason' => $eventData['bonus_reason'] ?? null
        ]);
        
        return $this;
    }

    public function levelUp()
    {
        $oldLevel = $this->level;
        $this->level++;
        $this->current_level_xp = $this->current_level_xp - $this->next_level_xp;
        $this->next_level_xp = $this->calculateNextLevelXp($this->level);
        $this->level_name = $this->calculateLevelName($this->level);
        $this->level_tier = $this->calculateLevelTier($this->level);
        $this->level_benefits = $this->calculateLevelBenefits($this->level);
        $this->last_level_up = now();
        
        // Award level up bonus XP
        if ($this->level > 1) {
            $bonusXp = $this->level * 10;
            XpEvent::create([
                'user_id' => $this->user_id,
                'event_type' => 'level_up',
                'event_category' => 'milestone',
                'xp_amount' => $bonusXp,
                'multiplier' => 1,
                'final_xp' => $bonusXp,
                'description' => "Level up bonus: Level {$oldLevel} → Level {$this->level}",
                'metadata' => [
                    'old_level' => $oldLevel,
                    'new_level' => $this->level
                ],
                'is_bonus' => true,
                'bonus_reason' => 'level_up'
            ]);
        }
        
        return $this;
    }

    public function updateXpToNextLevel()
    {
        $this->xp_to_next_level = $this->next_level_xp - $this->current_level_xp;
        return $this;
    }

    public function calculateNextLevelXp($level)
    {
        // Progressive XP requirement: base * (level ^ 1.5)
        $baseXp = 100;
        return ceil($baseXp * pow($level, 1.5));
    }

    public function calculateLevelName($level)
    {
        $levelNames = [
            1 => 'Newcomer',
            5 => 'Explorer',
            10 => 'Creator',
            15 => 'Innovator',
            20 => 'Expert',
            25 => 'Master',
            30 => 'Guru',
            35 => 'Legend',
            40 => 'Champion',
            45 => 'Elite',
            50 => 'Grand Master'
        ];

        $levelName = 'Newcomer';
        foreach ($levelNames as $levelThreshold => $name) {
            if ($level >= $levelThreshold) {
                $levelName = $name;
            }
        }

        return $levelName;
    }

    public function calculateLevelTier($level)
    {
        if ($level >= 50) return 'Diamond';
        if ($level >= 40) return 'Platinum';
        if ($level >= 30) return 'Gold';
        if ($level >= 20) return 'Silver';
        return 'Bronze';
    }

    public function calculateLevelBenefits($level)
    {
        $benefits = [];
        
        if ($level >= 5) {
            $benefits[] = 'Custom bio site themes';
        }
        if ($level >= 10) {
            $benefits[] = 'Advanced analytics';
        }
        if ($level >= 15) {
            $benefits[] = 'Priority support';
        }
        if ($level >= 20) {
            $benefits[] = 'Custom branding';
        }
        if ($level >= 25) {
            $benefits[] = 'API access';
        }
        if ($level >= 30) {
            $benefits[] = 'White-label features';
        }
        if ($level >= 40) {
            $benefits[] = 'Beta feature access';
        }
        if ($level >= 50) {
            $benefits[] = 'VIP status';
        }

        return $benefits;
    }

    public function getProgressPercentage()
    {
        if ($this->next_level_xp <= 0) {
            return 100;
        }

        return round(($this->current_level_xp / $this->next_level_xp) * 100, 2);
    }

    public function getTierColor()
    {
        return match($this->level_tier) {
            'Bronze' => '#CD7F32',
            'Silver' => '#C0C0C0',
            'Gold' => '#FFD700',
            'Platinum' => '#E5E4E2',
            'Diamond' => '#B9F2FF',
            default => '#6B7280'
        };
    }

    public function getTierIcon()
    {
        return match($this->level_tier) {
            'Bronze' => '🥉',
            'Silver' => '🥈',
            'Gold' => '🥇',
            'Platinum' => '💎',
            'Diamond' => '💠',
            default => '🏆'
        };
    }
}