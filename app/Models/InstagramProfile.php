<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class InstagramProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'username',
        'display_name',
        'bio',
        'follower_count',
        'following_count',
        'post_count',
        'engagement_rate',
        'location',
        'category',
        'email',
        'phone',
        'website',
        'profile_image_url',
        'is_business_account',
        'is_verified',
        'language',
        'last_scraped',
        'workspace_id'
    ];

    protected $casts = [
        'is_business_account' => 'boolean',
        'is_verified' => 'boolean',
        'engagement_rate' => 'decimal:2',
        'last_scraped' => 'datetime'
    ];

    /**
     * Get the workspace that owns the profile
     */
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'workspace_id');
    }

    /**
     * Get the posts for the profile
     */
    public function posts(): HasMany
    {
        return $this->hasMany(InstagramPost::class, 'profile_id');
    }

    /**
     * Get the hashtags for the profile
     */
    public function hashtags(): HasMany
    {
        return $this->hasMany(InstagramHashtag::class, 'profile_id');
    }

    /**
     * Get the analytics for the profile
     */
    public function analytics(): HasMany
    {
        return $this->hasMany(InstagramAnalytics::class, 'profile_id');
    }

    /**
     * Scope for verified profiles
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope for business accounts
     */
    public function scopeBusinessAccounts($query)
    {
        return $query->where('is_business_account', true);
    }

    /**
     * Scope for profiles with minimum followers
     */
    public function scopeMinFollowers($query, $count)
    {
        return $query->where('follower_count', '>=', $count);
    }

    /**
     * Scope for profiles with maximum followers
     */
    public function scopeMaxFollowers($query, $count)
    {
        return $query->where('follower_count', '<=', $count);
    }

    /**
     * Scope for profiles by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for profiles by location
     */
    public function scopeByLocation($query, $location)
    {
        return $query->where('location', 'like', '%' . $location . '%');
    }

    /**
     * Scope for recent profiles
     */
    public function scopeRecentlyScraped($query, $hours = 24)
    {
        return $query->where('last_scraped', '>=', now()->subHours($hours));
    }

    /**
     * Calculate engagement rate
     */
    public function calculateEngagementRate()
    {
        $recentPosts = $this->posts()->orderBy('created_at', 'desc')->limit(12)->get();
        
        if ($recentPosts->count() === 0 || $this->follower_count === 0) {
            return 0;
        }

        $totalEngagement = $recentPosts->sum(function ($post) {
            return $post->likes_count + $post->comments_count;
        });

        $averageEngagement = $totalEngagement / $recentPosts->count();
        
        return round(($averageEngagement / $this->follower_count) * 100, 2);
    }

    /**
     * Get profile URL
     */
    public function getProfileUrlAttribute()
    {
        return 'https://instagram.com/' . $this->username;
    }

    /**
     * Get follower count formatted
     */
    public function getFollowerCountFormattedAttribute()
    {
        return $this->formatNumber($this->follower_count);
    }

    /**
     * Get following count formatted
     */
    public function getFollowingCountFormattedAttribute()
    {
        return $this->formatNumber($this->following_count);
    }

    /**
     * Format number for display
     */
    private function formatNumber($number)
    {
        if ($number >= 1000000) {
            return round($number / 1000000, 1) . 'M';
        } elseif ($number >= 1000) {
            return round($number / 1000, 1) . 'K';
        } else {
            return $number;
        }
    }

    /**
     * Get top hashtags
     */
    public function getTopHashtags($limit = 10)
    {
        return $this->hashtags()
                   ->orderBy('usage_count', 'desc')
                   ->limit($limit)
                   ->pluck('hashtag', 'usage_count');
    }

    /**
     * Check if profile needs updating
     */
    public function needsUpdate($hours = 24)
    {
        return !$this->last_scraped || $this->last_scraped->diffInHours(now()) >= $hours;
    }

    /**
     * Get similar profiles
     */
    public function getSimilarProfiles($limit = 5)
    {
        return self::where('id', '!=', $this->id)
                   ->where('category', $this->category)
                   ->whereBetween('follower_count', [
                       $this->follower_count * 0.5,
                       $this->follower_count * 2
                   ])
                   ->orderBy('engagement_rate', 'desc')
                   ->limit($limit)
                   ->get();
    }

    /**
     * Get profile statistics
     */
    public function getStatistics()
    {
        return [
            'total_posts' => $this->posts()->count(),
            'average_likes' => $this->posts()->avg('likes_count'),
            'average_comments' => $this->posts()->avg('comments_count'),
            'engagement_rate' => $this->engagement_rate,
            'posting_frequency' => $this->getPostingFrequency(),
            'best_performing_post' => $this->posts()->orderBy('likes_count', 'desc')->first(),
            'most_used_hashtags' => $this->getTopHashtags(5)
        ];
    }

    /**
     * Get posting frequency
     */
    private function getPostingFrequency()
    {
        $posts = $this->posts()->orderBy('created_at', 'desc')->limit(30)->get();
        
        if ($posts->count() < 2) {
            return 'Insufficient data';
        }

        $firstPost = $posts->last();
        $lastPost = $posts->first();
        
        $daysDiff = $firstPost->created_at->diffInDays($lastPost->created_at);
        
        if ($daysDiff === 0) {
            return 'Multiple posts per day';
        }

        $frequency = round($posts->count() / $daysDiff, 1);
        
        if ($frequency >= 1) {
            return $frequency . ' posts per day';
        } else {
            return round(1 / $frequency, 1) . ' days per post';
        }
    }
}