<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InstagramAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'workspace_id',
        'instagram_id',
        'username',
        'display_name',
        'bio',
        'profile_picture_url',
        'website',
        'followers_count',
        'following_count',
        'media_count',
        'account_type',
        'is_verified',
        'is_private',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'is_active',
        'permissions',
        'metadata',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_private' => 'boolean',
        'is_active' => 'boolean',
        'permissions' => 'array',
        'metadata' => 'array',
        'token_expires_at' => 'datetime',
    ];

    protected $hidden = [
        'access_token',
        'refresh_token',
    ];

    /**
     * Get the user that owns this Instagram account
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the workspace that owns this Instagram account
     */
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    /**
     * Get posts for this Instagram account
     */
    public function posts(): HasMany
    {
        return $this->hasMany(InstagramPost::class);
    }

    /**
     * Get stories for this Instagram account
     */
    public function stories(): HasMany
    {
        return $this->hasMany(InstagramStory::class);
    }

    /**
     * Get analytics for this Instagram account
     */
    public function analytics(): HasMany
    {
        return $this->hasMany(InstagramAnalytics::class);
    }

    /**
     * Get recent posts
     */
    public function recentPosts($limit = 10)
    {
        return $this->posts()->latest()->limit($limit)->get();
    }

    /**
     * Get published posts
     */
    public function publishedPosts()
    {
        return $this->posts()->where('status', 'published');
    }

    /**
     * Get scheduled posts
     */
    public function scheduledPosts()
    {
        return $this->posts()->where('status', 'scheduled');
    }

    /**
     * Get recent stories
     */
    public function recentStories($limit = 10)
    {
        return $this->stories()->latest()->limit($limit)->get();
    }

    /**
     * Get active stories (not expired)
     */
    public function activeStories()
    {
        return $this->stories()->where('status', 'published')->where('expires_at', '>', now());
    }

    /**
     * Check if token is expired
     */
    public function isTokenExpired(): bool
    {
        return $this->token_expires_at && $this->token_expires_at->isPast();
    }

    /**
     * Get engagement rate
     */
    public function getEngagementRate(): float
    {
        $recentPosts = $this->recentPosts(20);
        if ($recentPosts->isEmpty() || $this->followers_count == 0) {
            return 0.0;
        }

        $totalEngagement = $recentPosts->sum(function($post) {
            return $post->likes_count + $post->comments_count + $post->shares_count;
        });

        return ($totalEngagement / ($recentPosts->count() * $this->followers_count)) * 100;
    }

    /**
     * Get formatted follower count
     */
    public function getFormattedFollowersCount(): string
    {
        if ($this->followers_count >= 1000000) {
            return round($this->followers_count / 1000000, 1) . 'M';
        } elseif ($this->followers_count >= 1000) {
            return round($this->followers_count / 1000, 1) . 'K';
        }
        return (string) $this->followers_count;
    }

    /**
     * Get account type badge
     */
    public function getAccountTypeBadge(): string
    {
        $badges = [
            'personal' => 'Personal',
            'business' => 'Business',
            'creator' => 'Creator',
        ];

        return $badges[$this->account_type] ?? 'Unknown';
    }

    /**
     * Scope to get active accounts
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get accounts by workspace
     */
    public function scopeByWorkspace($query, $workspaceId)
    {
        return $query->where('workspace_id', $workspaceId);
    }

    /**
     * Scope to get business accounts
     */
    public function scopeBusiness($query)
    {
        return $query->where('account_type', 'business');
    }

    /**
     * Scope to get verified accounts
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Update account statistics
     */
    public function updateStats(array $stats)
    {
        $this->update([
            'followers_count' => $stats['followers_count'] ?? $this->followers_count,
            'following_count' => $stats['following_count'] ?? $this->following_count,
            'media_count' => $stats['media_count'] ?? $this->media_count,
        ]);
    }

    /**
     * Refresh access token
     */
    public function refreshToken()
    {
        // TODO: Implement Instagram token refresh logic
        // This would typically involve calling Instagram's token refresh endpoint
    }
}