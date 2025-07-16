<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class InstagramHashtag extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'workspace_id',
        'hashtag',
        'category',
        'posts_count',
        'engagement_rate',
        'difficulty_score',
        'trend_status',
        'last_used_at',
        'use_count',
        'is_favorite',
        'is_banned',
        'related_hashtags',
        'performance_data',
        'metadata',
    ];

    protected $casts = [
        'engagement_rate' => 'decimal:2',
        'is_favorite' => 'boolean',
        'is_banned' => 'boolean',
        'related_hashtags' => 'array',
        'performance_data' => 'array',
        'metadata' => 'array',
        'last_used_at' => 'datetime',
    ];

    /**
     * Get the user that owns this hashtag
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the workspace that owns this hashtag
     */
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    /**
     * Get hashtag with # symbol
     */
    public function getHashtagWithSymbol(): string
    {
        return '#' . $this->hashtag;
    }

    /**
     * Get difficulty level
     */
    public function getDifficultyLevel(): string
    {
        if ($this->difficulty_score <= 30) {
            return 'Easy';
        } elseif ($this->difficulty_score <= 60) {
            return 'Medium';
        } else {
            return 'Hard';
        }
    }

    /**
     * Get difficulty color
     */
    public function getDifficultyColor(): string
    {
        if ($this->difficulty_score <= 30) {
            return 'text-green-500';
        } elseif ($this->difficulty_score <= 60) {
            return 'text-yellow-500';
        } else {
            return 'text-red-500';
        }
    }

    /**
     * Get trend status color
     */
    public function getTrendStatusColor(): string
    {
        $colors = [
            'rising' => 'text-green-500',
            'stable' => 'text-blue-500',
            'declining' => 'text-red-500',
        ];

        return $colors[$this->trend_status] ?? 'text-gray-500';
    }

    /**
     * Get formatted posts count
     */
    public function getFormattedPostsCount(): string
    {
        if ($this->posts_count >= 1000000) {
            return round($this->posts_count / 1000000, 1) . 'M';
        } elseif ($this->posts_count >= 1000) {
            return round($this->posts_count / 1000, 1) . 'K';
        }
        return (string) $this->posts_count;
    }

    /**
     * Get performance score
     */
    public function getPerformanceScore(): float
    {
        // Calculate performance based on engagement rate and difficulty
        $engagementScore = min($this->engagement_rate * 10, 50); // Max 50 points
        $difficultyScore = (100 - $this->difficulty_score) * 0.3; // Max 30 points
        $trendScore = $this->getTrendScore(); // Max 20 points
        
        return $engagementScore + $difficultyScore + $trendScore;
    }

    /**
     * Get trend score
     */
    private function getTrendScore(): float
    {
        $scores = [
            'rising' => 20,
            'stable' => 15,
            'declining' => 5,
        ];

        return $scores[$this->trend_status] ?? 0;
    }

    /**
     * Get recommendation level
     */
    public function getRecommendationLevel(): string
    {
        $score = $this->getPerformanceScore();
        
        if ($score >= 80) {
            return 'Highly Recommended';
        } elseif ($score >= 60) {
            return 'Recommended';
        } elseif ($score >= 40) {
            return 'Moderate';
        } else {
            return 'Not Recommended';
        }
    }

    /**
     * Get recommendation color
     */
    public function getRecommendationColor(): string
    {
        $score = $this->getPerformanceScore();
        
        if ($score >= 80) {
            return 'text-green-600';
        } elseif ($score >= 60) {
            return 'text-green-500';
        } elseif ($score >= 40) {
            return 'text-yellow-500';
        } else {
            return 'text-red-500';
        }
    }

    /**
     * Scope to get favorite hashtags
     */
    public function scopeFavorite($query)
    {
        return $query->where('is_favorite', true);
    }

    /**
     * Scope to get non-banned hashtags
     */
    public function scopeNotBanned($query)
    {
        return $query->where('is_banned', false);
    }

    /**
     * Scope to get hashtags by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to get trending hashtags
     */
    public function scopeTrending($query)
    {
        return $query->where('trend_status', 'rising');
    }

    /**
     * Scope to get hashtags by difficulty
     */
    public function scopeByDifficulty($query, $level)
    {
        switch ($level) {
            case 'easy':
                return $query->where('difficulty_score', '<=', 30);
            case 'medium':
                return $query->whereBetween('difficulty_score', [31, 60]);
            case 'hard':
                return $query->where('difficulty_score', '>', 60);
            default:
                return $query;
        }
    }

    /**
     * Scope to get recently used hashtags
     */
    public function scopeRecentlyUsed($query, $days = 30)
    {
        return $query->where('last_used_at', '>=', Carbon::now()->subDays($days));
    }

    /**
     * Scope to get popular hashtags
     */
    public function scopePopular($query)
    {
        return $query->orderBy('posts_count', 'desc')
                     ->orderBy('engagement_rate', 'desc');
    }

    /**
     * Scope to get high performing hashtags
     */
    public function scopeHighPerforming($query)
    {
        return $query->where('engagement_rate', '>=', 3.0)
                     ->where('difficulty_score', '<=', 70);
    }

    /**
     * Scope to search hashtags
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('hashtag', 'like', "%{$search}%")
                     ->orWhere('category', 'like', "%{$search}%");
    }

    /**
     * Mark hashtag as used
     */
    public function markAsUsed()
    {
        $this->increment('use_count');
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Toggle favorite status
     */
    public function toggleFavorite()
    {
        $this->update(['is_favorite' => !$this->is_favorite]);
    }

    /**
     * Mark as banned
     */
    public function markAsBanned()
    {
        $this->update(['is_banned' => true]);
    }

    /**
     * Update performance data
     */
    public function updatePerformance(array $data)
    {
        $performance = $this->performance_data ?? [];
        $performance[] = array_merge($data, ['updated_at' => now()]);
        
        // Keep only last 10 performance records
        if (count($performance) > 10) {
            $performance = array_slice($performance, -10);
        }
        
        $this->update(['performance_data' => $performance]);
    }

    /**
     * Get related hashtags for suggestions
     */
    public function getSuggestedHashtags($limit = 10)
    {
        $related = $this->related_hashtags ?? [];
        
        return static::whereIn('hashtag', $related)
                     ->where('id', '!=', $this->id)
                     ->notBanned()
                     ->orderBy('engagement_rate', 'desc')
                     ->limit($limit)
                     ->get();
    }
}