<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class InstagramPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'workspace_id',
        'instagram_account_id',
        'instagram_post_id',
        'caption',
        'media_urls',
        'hashtags',
        'post_type',
        'status',
        'scheduled_at',
        'published_at',
        'failure_reason',
        'likes_count',
        'comments_count',
        'shares_count',
        'saves_count',
        'reach',
        'impressions',
        'engagement_data',
        'location_data',
        'user_tags',
        'metadata',
    ];

    protected $casts = [
        'media_urls' => 'array',
        'hashtags' => 'array',
        'engagement_data' => 'array',
        'location_data' => 'array',
        'user_tags' => 'array',
        'metadata' => 'array',
        'scheduled_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    /**
     * Get the user that owns this post
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the workspace that owns this post
     */
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    /**
     * Get the Instagram account that owns this post
     */
    public function instagramAccount(): BelongsTo
    {
        return $this->belongsTo(InstagramAccount::class);
    }

    /**
     * Check if post is published
     */
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Check if post is scheduled
     */
    public function isScheduled(): bool
    {
        return $this->status === 'scheduled';
    }

    /**
     * Check if post is draft
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if post has failed
     */
    public function hasFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if post is ready to publish
     */
    public function isReadyToPublish(): bool
    {
        return $this->status === 'scheduled' && 
               $this->scheduled_at && 
               $this->scheduled_at->isPast();
    }

    /**
     * Get engagement rate
     */
    public function getEngagementRate(): float
    {
        $account = $this->instagramAccount;
        if (!$account || $account->followers_count == 0) {
            return 0.0;
        }

        $totalEngagement = $this->likes_count + $this->comments_count + $this->shares_count;
        return ($totalEngagement / $account->followers_count) * 100;
    }

    /**
     * Get formatted engagement count
     */
    public function getFormattedEngagement(): string
    {
        $total = $this->likes_count + $this->comments_count + $this->shares_count;
        
        if ($total >= 1000000) {
            return round($total / 1000000, 1) . 'M';
        } elseif ($total >= 1000) {
            return round($total / 1000, 1) . 'K';
        }
        return (string) $total;
    }

    /**
     * Get post type badge
     */
    public function getPostTypeBadge(): string
    {
        $badges = [
            'photo' => 'Photo',
            'video' => 'Video',
            'carousel' => 'Carousel',
            'reel' => 'Reel',
        ];

        return $badges[$this->post_type] ?? 'Unknown';
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClass(): string
    {
        $classes = [
            'draft' => 'bg-gray-100 text-gray-800',
            'scheduled' => 'bg-blue-100 text-blue-800',
            'published' => 'bg-green-100 text-green-800',
            'failed' => 'bg-red-100 text-red-800',
        ];

        return $classes[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Get hashtags as string
     */
    public function getHashtagsString(): string
    {
        if (empty($this->hashtags)) {
            return '';
        }

        return implode(' ', array_map(function($hashtag) {
            return '#' . $hashtag;
        }, $this->hashtags));
    }

    /**
     * Get primary media URL
     */
    public function getPrimaryMediaUrl(): ?string
    {
        return $this->media_urls[0] ?? null;
    }

    /**
     * Get media count
     */
    public function getMediaCount(): int
    {
        return count($this->media_urls ?? []);
    }

    /**
     * Scope to get published posts
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope to get scheduled posts
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    /**
     * Scope to get draft posts
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope to get failed posts
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope to get posts ready to publish
     */
    public function scopeReadyToPublish($query)
    {
        return $query->where('status', 'scheduled')
                     ->where('scheduled_at', '<=', now());
    }

    /**
     * Scope to get posts by account
     */
    public function scopeByAccount($query, $accountId)
    {
        return $query->where('instagram_account_id', $accountId);
    }

    /**
     * Scope to get posts by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('post_type', $type);
    }

    /**
     * Scope to get recent posts
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    /**
     * Mark post as published
     */
    public function markAsPublished(string $instagramPostId)
    {
        $this->update([
            'status' => 'published',
            'instagram_post_id' => $instagramPostId,
            'published_at' => now(),
        ]);
    }

    /**
     * Mark post as failed
     */
    public function markAsFailed(string $reason)
    {
        $this->update([
            'status' => 'failed',
            'failure_reason' => $reason,
        ]);
    }

    /**
     * Update engagement metrics
     */
    public function updateEngagement(array $metrics)
    {
        $this->update([
            'likes_count' => $metrics['likes_count'] ?? $this->likes_count,
            'comments_count' => $metrics['comments_count'] ?? $this->comments_count,
            'shares_count' => $metrics['shares_count'] ?? $this->shares_count,
            'saves_count' => $metrics['saves_count'] ?? $this->saves_count,
            'reach' => $metrics['reach'] ?? $this->reach,
            'impressions' => $metrics['impressions'] ?? $this->impressions,
            'engagement_data' => array_merge($this->engagement_data ?? [], $metrics['engagement_data'] ?? []),
        ]);
    }
}