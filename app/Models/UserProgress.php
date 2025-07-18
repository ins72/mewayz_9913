<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class UserProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'current_level',
        'current_xp',
        'total_xp',
        'lifetime_xp',
        'prestige',
        'prestige_points',
        'specializations',
        'mastery',
        'level_history',
        'xp_multipliers',
        'streaks',
        'seasonal_bonus',
        'mentorship_bonus',
        'daily_xp',
        'weekly_xp',
        'monthly_xp',
        'yearly_xp',
        'metadata'
    ];

    protected $casts = [
        'specializations' => 'array',
        'mastery' => 'array',
        'level_history' => 'array',
        'xp_multipliers' => 'array',
        'streaks' => 'array',
        'seasonal_bonus' => 'array',
        'mentorship_bonus' => 'array',
        'metadata' => 'array',
        'current_level' => 'integer',
        'current_xp' => 'integer',
        'total_xp' => 'integer',
        'lifetime_xp' => 'integer',
        'prestige' => 'integer',
        'prestige_points' => 'integer',
        'daily_xp' => 'integer',
        'weekly_xp' => 'integer',
        'monthly_xp' => 'integer',
        'yearly_xp' => 'integer'
    ];

    /**
     * Get the user that owns this progress
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate XP needed for next level
     */
    public function getXpToNextLevelAttribute()
    {
        return $this->calculateXPForLevel($this->current_level + 1) - $this->current_xp;
    }

    /**
     * Calculate XP needed for a specific level
     */
    public function calculateXPForLevel($level)
    {
        // Exponential growth formula: XP = 100 * (level^2.5)
        return (int) (100 * pow($level, 2.5));
    }

    /**
     * Add XP to user
     */
    public function addXP($amount, $source = 'unknown', $metadata = [])
    {
        $multiplier = $this->calculateXPMultiplier($source, $metadata);
        $finalAmount = (int) ($amount * $multiplier);
        
        // Update XP values
        $this->current_xp += $finalAmount;
        $this->total_xp += $finalAmount;
        $this->lifetime_xp += $finalAmount;
        $this->daily_xp += $finalAmount;
        $this->weekly_xp += $finalAmount;
        $this->monthly_xp += $finalAmount;
        $this->yearly_xp += $finalAmount;
        
        // Check for level up
        $this->checkLevelUp();
        
        // Record XP gain
        $this->recordXPGain($finalAmount, $source, $metadata);
        
        $this->save();
        
        return $finalAmount;
    }

    /**
     * Calculate XP multiplier based on various factors
     */
    private function calculateXPMultiplier($source, $metadata)
    {
        $multiplier = 1.0;
        
        // Base multipliers
        $baseMultipliers = $this->xp_multipliers['base'] ?? [];
        foreach ($baseMultipliers as $mult) {
            if ($mult['active'] && Carbon::parse($mult['expires_at'])->isFuture()) {
                $multiplier *= $mult['value'];
            }
        }
        
        // Prestige multiplier
        $multiplier *= (1 + ($this->prestige * 0.1));
        
        // Streak multipliers
        $streaks = $this->streaks ?? [];
        foreach ($streaks as $streakType => $streak) {
            if ($streak['current'] > 0) {
                $streakMultiplier = 1 + min($streak['current'] * 0.01, 0.5); // Max 50% bonus
                $multiplier *= $streakMultiplier;
            }
        }
        
        // Seasonal bonus
        if (!empty($this->seasonal_bonus) && $this->seasonal_bonus['active']) {
            $multiplier *= $this->seasonal_bonus['multiplier'];
        }
        
        // Mentorship bonus
        if (!empty($this->mentorship_bonus) && $this->mentorship_bonus['active']) {
            $multiplier *= $this->mentorship_bonus['multiplier'];
        }
        
        // Quality multiplier
        if (isset($metadata['quality_score'])) {
            $qualityMultiplier = 1 + ($metadata['quality_score'] / 100);
            $multiplier *= $qualityMultiplier;
        }
        
        return $multiplier;
    }

    /**
     * Check if user should level up
     */
    private function checkLevelUp()
    {
        $newLevel = $this->calculateLevelFromXP($this->current_xp);
        
        if ($newLevel > $this->current_level) {
            $oldLevel = $this->current_level;
            $this->current_level = $newLevel;
            
            // Record level up in history
            $levelHistory = $this->level_history ?? [];
            $levelHistory[] = [
                'from_level' => $oldLevel,
                'to_level' => $newLevel,
                'xp_at_levelup' => $this->current_xp,
                'date' => now()->toISOString()
            ];
            $this->level_history = $levelHistory;
            
            // Check for prestige
            if ($newLevel >= 100) {
                $this->handlePrestige();
            }
            
            // Award level up rewards
            $this->awardLevelUpRewards($newLevel);
            
            // Fire level up event
            event(new \App\Events\UserLevelUp($this->user, $oldLevel, $newLevel));
        }
    }

    /**
     * Calculate level from XP
     */
    private function calculateLevelFromXP($xp)
    {
        $level = 1;
        while ($this->calculateXPForLevel($level + 1) <= $xp) {
            $level++;
        }
        return $level;
    }

    /**
     * Handle prestige system
     */
    private function handlePrestige()
    {
        $this->prestige++;
        $this->prestige_points += $this->current_level;
        $this->current_level = 1;
        $this->current_xp = 0;
        
        // Award prestige rewards
        $this->awardPrestigeRewards();
        
        // Fire prestige event
        event(new \App\Events\UserPrestige($this->user, $this->prestige));
    }

    /**
     * Award level up rewards
     */
    private function awardLevelUpRewards($level)
    {
        $rewards = $this->getLevelUpRewards($level);
        
        foreach ($rewards as $reward) {
            switch ($reward['type']) {
                case 'credits':
                    $this->user->addCredits($reward['value'], 'level_up_reward');
                    break;
                case 'badge':
                    $this->user->awardBadge($reward['value']);
                    break;
                case 'premium_time':
                    $this->user->extendPremium($reward['value']);
                    break;
                case 'specialization_unlock':
                    $this->unlockSpecialization($reward['value']);
                    break;
            }
        }
    }

    /**
     * Get level up rewards for a specific level
     */
    private function getLevelUpRewards($level)
    {
        $rewards = [];
        
        // Every 5 levels
        if ($level % 5 === 0) {
            $rewards[] = ['type' => 'credits', 'value' => $level * 10];
        }
        
        // Every 10 levels
        if ($level % 10 === 0) {
            $rewards[] = ['type' => 'badge', 'value' => "level_{$level}"];
        }
        
        // Every 25 levels
        if ($level % 25 === 0) {
            $rewards[] = ['type' => 'premium_time', 'value' => 7]; // 7 days
        }
        
        // Special levels
        $specialRewards = [
            20 => ['type' => 'specialization_unlock', 'value' => 'social_media'],
            30 => ['type' => 'specialization_unlock', 'value' => 'content_creation'],
            40 => ['type' => 'specialization_unlock', 'value' => 'ecommerce'],
            50 => ['type' => 'specialization_unlock', 'value' => 'community_management'],
            60 => ['type' => 'specialization_unlock', 'value' => 'analytics'],
            70 => ['type' => 'specialization_unlock', 'value' => 'technical_innovation']
        ];
        
        if (isset($specialRewards[$level])) {
            $rewards[] = $specialRewards[$level];
        }
        
        return $rewards;
    }

    /**
     * Award prestige rewards
     */
    private function awardPrestigeRewards()
    {
        $rewards = [
            ['type' => 'credits', 'value' => 10000],
            ['type' => 'badge', 'value' => "prestige_{$this->prestige}"],
            ['type' => 'premium_time', 'value' => 30],
            ['type' => 'xp_multiplier', 'value' => 1.1, 'duration' => 365]
        ];
        
        foreach ($rewards as $reward) {
            switch ($reward['type']) {
                case 'credits':
                    $this->user->addCredits($reward['value'], 'prestige_reward');
                    break;
                case 'badge':
                    $this->user->awardBadge($reward['value']);
                    break;
                case 'premium_time':
                    $this->user->extendPremium($reward['value']);
                    break;
                case 'xp_multiplier':
                    $this->addXPMultiplier($reward['value'], $reward['duration']);
                    break;
            }
        }
    }

    /**
     * Add XP multiplier
     */
    public function addXPMultiplier($multiplier, $durationDays)
    {
        $multipliers = $this->xp_multipliers ?? [];
        $multipliers['base'] = $multipliers['base'] ?? [];
        
        $multipliers['base'][] = [
            'value' => $multiplier,
            'active' => true,
            'expires_at' => now()->addDays($durationDays)->toISOString(),
            'source' => 'prestige_reward'
        ];
        
        $this->xp_multipliers = $multipliers;
        $this->save();
    }

    /**
     * Record XP gain
     */
    private function recordXPGain($amount, $source, $metadata)
    {
        // Create analytics event
        UnifiedAnalyticsEvent::track([
            'user_id' => $this->user_id,
            'event_type' => 'xp_gained',
            'event_category' => 'gamification',
            'platform' => 'platform',
            'properties' => [
                'amount' => $amount,
                'source' => $source,
                'metadata' => $metadata,
                'level' => $this->current_level,
                'total_xp' => $this->total_xp
            ]
        ]);
    }

    /**
     * Update streak
     */
    public function updateStreak($streakType, $action = 'maintain')
    {
        $streaks = $this->streaks ?? [];
        
        if (!isset($streaks[$streakType])) {
            $streaks[$streakType] = [
                'current' => 0,
                'longest' => 0,
                'total' => 0,
                'last_updated' => now()->toDateString(),
                'freeze_tokens' => 0
            ];
        }
        
        $streak = &$streaks[$streakType];
        $today = now()->toDateString();
        $lastUpdate = $streak['last_updated'];
        
        switch ($action) {
            case 'increment':
                if ($lastUpdate === $today) {
                    // Already updated today
                    break;
                }
                
                $streak['current']++;
                $streak['total']++;
                $streak['longest'] = max($streak['longest'], $streak['current']);
                $streak['last_updated'] = $today;
                break;
                
            case 'break':
                if ($streak['freeze_tokens'] > 0) {
                    $streak['freeze_tokens']--;
                } else {
                    $streak['current'] = 0;
                }
                $streak['last_updated'] = $today;
                break;
                
            case 'maintain':
                // Check if streak should be broken
                $daysSinceUpdate = Carbon::parse($lastUpdate)->diffInDays(now());
                if ($daysSinceUpdate > 1) {
                    $this->updateStreak($streakType, 'break');
                }
                break;
        }
        
        $this->streaks = $streaks;
        $this->save();
        
        // Fire streak event
        event(new \App\Events\StreakUpdated($this->user, $streakType, $streak));
    }

    /**
     * Add freeze token
     */
    public function addFreezeToken($streakType)
    {
        $streaks = $this->streaks ?? [];
        
        if (isset($streaks[$streakType])) {
            $streaks[$streakType]['freeze_tokens']++;
            $this->streaks = $streaks;
            $this->save();
        }
    }

    /**
     * Unlock specialization
     */
    public function unlockSpecialization($specializationId)
    {
        $specializations = $this->specializations ?? [];
        
        if (!isset($specializations[$specializationId])) {
            $specializations[$specializationId] = [
                'id' => $specializationId,
                'level' => 1,
                'xp' => 0,
                'unlocked_at' => now()->toISOString(),
                'is_active' => false
            ];
            
            $this->specializations = $specializations;
            $this->save();
            
            // Fire specialization unlock event
            event(new \App\Events\SpecializationUnlocked($this->user, $specializationId));
        }
    }

    /**
     * Add specialization XP
     */
    public function addSpecializationXP($specializationId, $amount)
    {
        $specializations = $this->specializations ?? [];
        
        if (isset($specializations[$specializationId])) {
            $specializations[$specializationId]['xp'] += $amount;
            
            // Check for specialization level up
            $newLevel = $this->calculateSpecializationLevel($specializations[$specializationId]['xp']);
            if ($newLevel > $specializations[$specializationId]['level']) {
                $specializations[$specializationId]['level'] = $newLevel;
                
                // Fire specialization level up event
                event(new \App\Events\SpecializationLevelUp($this->user, $specializationId, $newLevel));
            }
            
            $this->specializations = $specializations;
            $this->save();
        }
    }

    /**
     * Calculate specialization level from XP
     */
    private function calculateSpecializationLevel($xp)
    {
        // Specialization levels are 1-10
        $level = 1;
        $xpPerLevel = 1000;
        
        while ($level < 10 && $xp >= ($level * $xpPerLevel)) {
            $level++;
        }
        
        return $level;
    }

    /**
     * Reset daily XP
     */
    public function resetDailyXP()
    {
        $this->daily_xp = 0;
        $this->save();
    }

    /**
     * Reset weekly XP
     */
    public function resetWeeklyXP()
    {
        $this->weekly_xp = 0;
        $this->save();
    }

    /**
     * Reset monthly XP
     */
    public function resetMonthlyXP()
    {
        $this->monthly_xp = 0;
        $this->save();
    }

    /**
     * Reset yearly XP
     */
    public function resetYearlyXP()
    {
        $this->yearly_xp = 0;
        $this->save();
    }
}