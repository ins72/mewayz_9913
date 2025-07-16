<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class InstagramAnalytics extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'workspace_id',
        'instagram_account_id',
        'date',
        'metric_type',
        'metric_name',
        'metric_value',
        'metric_percentage',
        'followers_count',
        'following_count',
        'posts_count',
        'stories_count',
        'total_reach',
        'total_impressions',
        'total_likes',
        'total_comments',
        'total_shares',
        'total_saves',
        'engagement_rate',
        'profile_visits',
        'website_clicks',
        'email_contacts',
        'phone_calls',
        'direction_clicks',
        'audience_demographics',
        'top_posts',
        'top_stories',
        'hashtag_performance',
        'optimal_times',
        'metadata',
    ];

    protected $casts = [
        'date' => 'date',
        'metric_percentage' => 'decimal:2',
        'engagement_rate' => 'decimal:2',
        'audience_demographics' => 'array',
        'top_posts' => 'array',
        'top_stories' => 'array',
        'hashtag_performance' => 'array',
        'optimal_times' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Get the user that owns this analytics record
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the workspace that owns this analytics record
     */
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    /**
     * Get the Instagram account that owns this analytics record
     */
    public function instagramAccount(): BelongsTo
    {
        return $this->belongsTo(InstagramAccount::class);
    }

    /**
     * Get total engagement
     */
    public function getTotalEngagement(): int
    {
        return $this->total_likes + $this->total_comments + $this->total_shares + $this->total_saves;
    }

    /**
     * Get formatted reach
     */
    public function getFormattedReach(): string
    {
        if ($this->total_reach >= 1000000) {
            return round($this->total_reach / 1000000, 1) . 'M';
        } elseif ($this->total_reach >= 1000) {
            return round($this->total_reach / 1000, 1) . 'K';
        }
        return (string) $this->total_reach;
    }

    /**
     * Get formatted impressions
     */
    public function getFormattedImpressions(): string
    {
        if ($this->total_impressions >= 1000000) {
            return round($this->total_impressions / 1000000, 1) . 'M';
        } elseif ($this->total_impressions >= 1000) {
            return round($this->total_impressions / 1000, 1) . 'K';
        }
        return (string) $this->total_impressions;
    }

    /**
     * Get formatted engagement
     */
    public function getFormattedEngagement(): string
    {
        $engagement = $this->getTotalEngagement();
        
        if ($engagement >= 1000000) {
            return round($engagement / 1000000, 1) . 'M';
        } elseif ($engagement >= 1000) {
            return round($engagement / 1000, 1) . 'K';
        }
        return (string) $engagement;
    }

    /**
     * Get reach rate (reach / impressions)
     */
    public function getReachRate(): float
    {
        if ($this->total_impressions == 0) {
            return 0.0;
        }
        
        return ($this->total_reach / $this->total_impressions) * 100;
    }

    /**
     * Get profile visit rate
     */
    public function getProfileVisitRate(): float
    {
        if ($this->total_reach == 0) {
            return 0.0;
        }
        
        return ($this->profile_visits / $this->total_reach) * 100;
    }

    /**
     * Get website click rate
     */
    public function getWebsiteClickRate(): float
    {
        if ($this->profile_visits == 0) {
            return 0.0;
        }
        
        return ($this->website_clicks / $this->profile_visits) * 100;
    }

    /**
     * Get primary audience demographic
     */
    public function getPrimaryAudience(): array
    {
        $demographics = $this->audience_demographics ?? [];
        
        return [
            'age_range' => $demographics['top_age_range'] ?? 'Unknown',
            'gender' => $demographics['top_gender'] ?? 'Unknown',
            'location' => $demographics['top_location'] ?? 'Unknown',
        ];
    }

    /**
     * Get top performing post
     */
    public function getTopPost(): ?array
    {
        $topPosts = $this->top_posts ?? [];
        return $topPosts[0] ?? null;
    }

    /**
     * Get top performing story
     */
    public function getTopStory(): ?array
    {
        $topStories = $this->top_stories ?? [];
        return $topStories[0] ?? null;
    }

    /**
     * Get best performing hashtag
     */
    public function getBestHashtag(): ?array
    {
        $hashtags = $this->hashtag_performance ?? [];
        
        if (empty($hashtags)) {
            return null;
        }
        
        // Sort by engagement rate
        usort($hashtags, function($a, $b) {
            return ($b['engagement_rate'] ?? 0) <=> ($a['engagement_rate'] ?? 0);
        });
        
        return $hashtags[0] ?? null;
    }

    /**
     * Get optimal posting time
     */
    public function getOptimalPostingTime(): ?string
    {
        $times = $this->optimal_times ?? [];
        
        if (empty($times['best_time'])) {
            return null;
        }
        
        return $times['best_time'];
    }

    /**
     * Scope to get analytics by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope to get analytics by metric type
     */
    public function scopeByMetricType($query, $type)
    {
        return $query->where('metric_type', $type);
    }

    /**
     * Scope to get analytics by account
     */
    public function scopeByAccount($query, $accountId)
    {
        return $query->where('instagram_account_id', $accountId);
    }

    /**
     * Scope to get recent analytics
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('date', '>=', Carbon::now()->subDays($days));
    }

    /**
     * Scope to get this week's analytics
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('date', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ]);
    }

    /**
     * Scope to get this month's analytics
     */
    public function scopeThisMonth($query)
    {
        return $query->whereBetween('date', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ]);
    }

    /**
     * Get growth rate compared to previous period
     */
    public function getGrowthRate(string $metric, int $days = 7): float
    {
        $currentValue = $this->$metric ?? 0;
        
        $previousRecord = static::where('instagram_account_id', $this->instagram_account_id)
            ->where('date', Carbon::parse($this->date)->subDays($days))
            ->first();
            
        if (!$previousRecord) {
            return 0.0;
        }
        
        $previousValue = $previousRecord->$metric ?? 0;
        
        if ($previousValue == 0) {
            return $currentValue > 0 ? 100.0 : 0.0;
        }
        
        return (($currentValue - $previousValue) / $previousValue) * 100;
    }

    /**
     * Get performance trend
     */
    public function getPerformanceTrend(string $metric, int $days = 7): string
    {
        $growth = $this->getGrowthRate($metric, $days);
        
        if ($growth > 5) {
            return 'trending_up';
        } elseif ($growth < -5) {
            return 'trending_down';
        } else {
            return 'stable';
        }
    }

    /**
     * Get performance insights
     */
    public function getPerformanceInsights(): array
    {
        $insights = [];
        
        // Engagement rate insight
        if ($this->engagement_rate >= 5) {
            $insights[] = [
                'type' => 'positive',
                'message' => 'Excellent engagement rate of ' . $this->engagement_rate . '%',
                'metric' => 'engagement_rate'
            ];
        } elseif ($this->engagement_rate < 2) {
            $insights[] = [
                'type' => 'negative',
                'message' => 'Low engagement rate. Consider posting more engaging content.',
                'metric' => 'engagement_rate'
            ];
        }
        
        // Reach vs impressions insight
        $reachRate = $this->getReachRate();
        if ($reachRate < 30) {
            $insights[] = [
                'type' => 'warning',
                'message' => 'Low reach rate. Your content may not be reaching new audiences.',
                'metric' => 'reach_rate'
            ];
        }
        
        // Profile visit insight
        $profileVisitRate = $this->getProfileVisitRate();
        if ($profileVisitRate > 10) {
            $insights[] = [
                'type' => 'positive',
                'message' => 'High profile visit rate. Your content is driving traffic to your profile.',
                'metric' => 'profile_visits'
            ];
        }
        
        return $insights;
    }
}