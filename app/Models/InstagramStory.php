<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class InstagramStory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'workspace_id',
        'instagram_account_id',
        'instagram_story_id',
        'content',
        'media_url',
        'media_type',
        'status',
        'scheduled_at',
        'published_at',
        'expires_at',
        'failure_reason',
        'views_count',
        'replies_count',
        'exits_count',
        'taps_forward',
        'taps_back',
        'interactive_elements',
        'engagement_data',
        'location_data',
        'user_tags',
        'metadata',
    ];

    protected $casts = [
        'interactive_elements' => 'array',
        'engagement_data' => 'array',
        'location_data' => 'array',
        'user_tags' => 'array',
        'metadata' => 'array',
        'scheduled_at' => 'datetime',
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the user that owns this story
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the workspace that owns this story
     */
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    /**
     * Get the Instagram account that owns this story
     */
    public function instagramAccount(): BelongsTo
    {
        return $this->belongsTo(InstagramAccount::class);
    }

    /**
     * Check if story is published
     */
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Check if story is scheduled
     */
    public function isScheduled(): bool
    {
        return $this->status === 'scheduled';
    }

    /**
     * Check if story is expired
     */
    public function isExpired(): bool
    {
        return $this->status === 'expired' || 
               ($this->expires_at && $this->expires_at->isPast());
    }

    /**
     * Check if story is active (published and not expired)
     */
    public function isActive(): bool
    {
        return $this->status === 'published' && 
               (!$this->expires_at || $this->expires_at->isFuture());
    }

    /**
     * Check if story is ready to publish
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
        if ($this->views_count == 0) {
            return 0.0;
        }

        $totalEngagement = $this->replies_count + $this->taps_forward + $this->taps_back;
        return ($totalEngagement / $this->views_count) * 100;
    }

    /**
     * Get completion rate
     */
    public function getCompletionRate(): float
    {
        if ($this->views_count == 0) {
            return 0.0;
        }

        $completed = $this->views_count - $this->exits_count;
        return ($completed / $this->views_count) * 100;
    }

    /**
     * Get formatted views count
     */
    public function getFormattedViewsCount(): string
    {
        if ($this->views_count >= 1000000) {
            return round($this->views_count / 1000000, 1) . 'M';
        } elseif ($this->views_count >= 1000) {
            return round($this->views_count / 1000, 1) . 'K';
        }
        return (string) $this->views_count;
    }

    /**
     * Get media type badge
     */
    public function getMediaTypeBadge(): string
    {
        $badges = [
            'photo' => 'Photo',
            'video' => 'Video',
        ];

        return $badges[$this->media_type] ?? 'Unknown';
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
            'expired' => 'bg-yellow-100 text-yellow-800',
            'failed' => 'bg-red-100 text-red-800',
        ];

        return $classes[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Get time until expiration
     */
    public function getTimeUntilExpiration(): ?string
    {
        if (!$this->expires_at) {
            return null;
        }

        if ($this->expires_at->isPast()) {
            return 'Expired';
        }

        return $this->expires_at->diffForHumans();
    }

    /**
     * Scope to get published stories
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope to get scheduled stories
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    /**
     * Scope to get active stories (published and not expired)
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'published')
                     ->where(function($query) {
                         $query->whereNull('expires_at')
                               ->orWhere('expires_at', '>', now());
                     });
    }

    /**
     * Scope to get expired stories
     */
    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')
                     ->orWhere('expires_at', '<=', now());
    }

    /**
     * Scope to get stories ready to publish
     */
    public function scopeReadyToPublish($query)
    {
        return $query->where('status', 'scheduled')
                     ->where('scheduled_at', '<=', now());
    }

    /**
     * Scope to get stories by account
     */
    public function scopeByAccount($query, $accountId)
    {
        return $query->where('instagram_account_id', $accountId);
    }

    /**
     * Scope to get stories by media type
     */
    public function scopeByMediaType($query, $type)
    {
        return $query->where('media_type', $type);
    }

    /**
     * Scope to get recent stories
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    /**
     * Mark story as published
     */
    public function markAsPublished(string $instagramStoryId)
    {
        $this->update([
            'status' => 'published',
            'instagram_story_id' => $instagramStoryId,
            'published_at' => now(),
            'expires_at' => now()->addDay(), // Stories expire after 24 hours
        ]);
    }

    /**
     * Mark story as expired
     */
    public function markAsExpired()
    {
        $this->update([
            'status' => 'expired',
        ]);
    }

    /**
     * Mark story as failed
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
            'views_count' => $metrics['views_count'] ?? $this->views_count,
            'replies_count' => $metrics['replies_count'] ?? $this->replies_count,
            'exits_count' => $metrics['exits_count'] ?? $this->exits_count,
            'taps_forward' => $metrics['taps_forward'] ?? $this->taps_forward,
            'taps_back' => $metrics['taps_back'] ?? $this->taps_back,
            'engagement_data' => array_merge($this->engagement_data ?? [], $metrics['engagement_data'] ?? []),
        ]);
    }
}