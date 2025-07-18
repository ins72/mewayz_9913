<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeaderboardEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'leaderboard_id',
        'user_id',
        'rank',
        'previous_rank',
        'score',
        'previous_score',
        'achievements',
        'badges',
        'specializations',
        'streak_summary',
        'social_metrics',
        'last_active',
        'metadata'
    ];

    protected $casts = [
        'achievements' => 'array',
        'badges' => 'array',
        'specializations' => 'array',
        'streak_summary' => 'array',
        'social_metrics' => 'array',
        'metadata' => 'array',
        'last_active' => 'datetime',
        'rank' => 'integer',
        'previous_rank' => 'integer',
        'score' => 'decimal:2',
        'previous_score' => 'decimal:2'
    ];

    /**
     * Get the leaderboard this entry belongs to
     */
    public function leaderboard()
    {
        return $this->belongsTo(Leaderboard::class);
    }

    /**
     * Get the user this entry belongs to
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get rank change
     */
    public function getRankChangeAttribute()
    {
        return $this->previous_rank - $this->rank;
    }

    /**
     * Get score change
     */
    public function getScoreChangeAttribute()
    {
        return $this->score - $this->previous_score;
    }

    /**
     * Get score change percentage
     */
    public function getScoreChangePercentageAttribute()
    {
        if ($this->previous_score == 0) {
            return 0;
        }
        
        return (($this->score - $this->previous_score) / $this->previous_score) * 100;
    }

    /**
     * Get rank change direction
     */
    public function getRankChangeDirectionAttribute()
    {
        $change = $this->rank_change;
        
        if ($change > 0) {
            return 'up';
        } elseif ($change < 0) {
            return 'down';
        } else {
            return 'same';
        }
    }

    /**
     * Get display rank with suffix
     */
    public function getDisplayRankAttribute()
    {
        $rank = $this->rank;
        
        if ($rank >= 11 && $rank <= 13) {
            return $rank . 'th';
        }
        
        switch ($rank % 10) {
            case 1:
                return $rank . 'st';
            case 2:
                return $rank . 'nd';
            case 3:
                return $rank . 'rd';
            default:
                return $rank . 'th';
        }
    }

    /**
     * Get medal type for top 3
     */
    public function getMedalTypeAttribute()
    {
        switch ($this->rank) {
            case 1:
                return 'gold';
            case 2:
                return 'silver';
            case 3:
                return 'bronze';
            default:
                return null;
        }
    }

    /**
     * Check if this is a top 10 position
     */
    public function getIsTop10Attribute()
    {
        return $this->rank <= 10;
    }

    /**
     * Check if this is a top 100 position
     */
    public function getIsTop100Attribute()
    {
        return $this->rank <= 100;
    }

    /**
     * Get formatted score
     */
    public function getFormattedScoreAttribute()
    {
        if ($this->score >= 1000000) {
            return number_format($this->score / 1000000, 1) . 'M';
        } elseif ($this->score >= 1000) {
            return number_format($this->score / 1000, 1) . 'K';
        } else {
            return number_format($this->score);
        }
    }

    /**
     * Scope for top entries
     */
    public function scopeTop($query, $limit = 10)
    {
        return $query->orderBy('rank', 'asc')->limit($limit);
    }

    /**
     * Scope for user's entries
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for rank range
     */
    public function scopeRankRange($query, $start, $end)
    {
        return $query->whereBetween('rank', [$start, $end]);
    }

    /**
     * Scope for recent entries
     */
    public function scopeRecent($query)
    {
        return $query->where('created_at', '>=', now()->subHours(24));
    }
}