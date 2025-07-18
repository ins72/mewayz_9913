<?php

namespace App\Models\Gamification;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class UserAchievement extends Model
{
    protected $table = 'gamification_user_achievements';
    
    protected $fillable = [
        'user_id', 'achievement_id', 'progress', 'target', 'completed', 'completed_at',
        'completion_count', 'progress_data'
    ];

    protected $casts = [
        'progress' => 'integer',
        'target' => 'integer',
        'completed' => 'boolean',
        'completed_at' => 'datetime',
        'completion_count' => 'integer',
        'progress_data' => 'array'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function achievement(): BelongsTo
    {
        return $this->belongsTo(Achievement::class, 'achievement_id');
    }

    public function getProgressPercentage()
    {
        if ($this->target <= 0) {
            return 0;
        }

        return min(100, round(($this->progress / $this->target) * 100, 2));
    }

    public function updateProgress($amount, $eventData = [])
    {
        $this->progress += $amount;
        
        // Update progress data
        $progressData = $this->progress_data ?? [];
        $progressData['last_update'] = now()->toISOString();
        $progressData['event_data'] = $eventData;
        $this->progress_data = $progressData;

        // Check if achievement is completed
        if ($this->progress >= $this->target && !$this->completed) {
            $this->completed = true;
            $this->completed_at = now();
            $this->completion_count++;
            
            // Award XP and rewards
            $this->awardRewards();
        }

        $this->save();
        
        return $this;
    }

    public function setProgress($amount, $eventData = [])
    {
        $this->progress = $amount;
        
        // Update progress data
        $progressData = $this->progress_data ?? [];
        $progressData['last_update'] = now()->toISOString();
        $progressData['event_data'] = $eventData;
        $this->progress_data = $progressData;

        // Check if achievement is completed
        if ($this->progress >= $this->target && !$this->completed) {
            $this->completed = true;
            $this->completed_at = now();
            $this->completion_count++;
            
            // Award XP and rewards
            $this->awardRewards();
        }

        $this->save();
        
        return $this;
    }

    public function resetProgress()
    {
        $this->progress = 0;
        $this->completed = false;
        $this->completed_at = null;
        $this->progress_data = [];
        $this->save();
        
        return $this;
    }

    public function awardRewards()
    {
        $achievement = $this->achievement;
        $user = $this->user;
        
        // Award XP
        if ($achievement->points > 0) {
            $user->addXp($achievement->points, 'achievement_completed', [
                'achievement_id' => $achievement->id,
                'achievement_name' => $achievement->name,
                'achievement_type' => $achievement->type
            ]);
        }

        // Award additional rewards
        if ($achievement->rewards) {
            foreach ($achievement->rewards as $reward) {
                $this->processReward($reward);
            }
        }
    }

    protected function processReward($reward)
    {
        $user = $this->user;
        
        switch ($reward['type']) {
            case 'xp':
                $user->addXp($reward['amount'], 'achievement_bonus', [
                    'achievement_id' => $this->achievement_id,
                    'reward_type' => 'bonus_xp'
                ]);
                break;
                
            case 'badge':
                // Award badge/unlock
                $progressData = $this->progress_data ?? [];
                $progressData['badges'][] = $reward['badge'];
                $this->progress_data = $progressData;
                $this->save();
                break;
                
            case 'unlock':
                // Unlock feature or content
                $progressData = $this->progress_data ?? [];
                $progressData['unlocks'][] = $reward['unlock'];
                $this->progress_data = $progressData;
                $this->save();
                break;
        }
    }
}