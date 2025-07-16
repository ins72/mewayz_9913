<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class EmailSubscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspace_id',
        'email',
        'first_name',
        'last_name',
        'phone',
        'location',
        'status',
        'tags',
        'custom_fields',
        'subscribed_at',
        'unsubscribed_at',
        'source',
        'ip_address'
    ];

    protected $casts = [
        'tags' => 'array',
        'custom_fields' => 'array',
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->subscribed_at)) {
                $model->subscribed_at = now();
            }
        });
    }

    /**
     * Get the workspace that owns the subscriber
     */
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    /**
     * Get the email lists this subscriber belongs to
     */
    public function emailLists(): BelongsToMany
    {
        return $this->belongsToMany(EmailList::class, 'email_list_subscribers', 'subscriber_id', 'list_id')
            ->withPivot(['subscribed_at', 'unsubscribed_at'])
            ->withTimestamps();
    }

    /**
     * Get the analytics for this subscriber
     */
    public function analytics(): HasMany
    {
        return $this->hasMany(EmailCampaignAnalytics::class, 'subscriber_id');
    }

    /**
     * Get full name
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Get display name (full name or email)
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->full_name ?: $this->email;
    }

    /**
     * Check if subscriber is active
     */
    public function isActive(): bool
    {
        return $this->status === 'subscribed';
    }

    /**
     * Subscribe the subscriber
     */
    public function subscribe()
    {
        $this->update([
            'status' => 'subscribed',
            'subscribed_at' => now(),
            'unsubscribed_at' => null
        ]);
    }

    /**
     * Unsubscribe the subscriber
     */
    public function unsubscribe()
    {
        $this->update([
            'status' => 'unsubscribed',
            'unsubscribed_at' => now()
        ]);
    }

    /**
     * Mark as bounced
     */
    public function markAsBounced()
    {
        $this->update([
            'status' => 'bounced'
        ]);
    }

    /**
     * Add tag to subscriber
     */
    public function addTag(string $tag)
    {
        $tags = $this->tags ?? [];
        if (!in_array($tag, $tags)) {
            $tags[] = $tag;
            $this->update(['tags' => $tags]);
        }
    }

    /**
     * Remove tag from subscriber
     */
    public function removeTag(string $tag)
    {
        $tags = $this->tags ?? [];
        $tags = array_filter($tags, fn($t) => $t !== $tag);
        $this->update(['tags' => array_values($tags)]);
    }

    /**
     * Check if subscriber has tag
     */
    public function hasTag(string $tag): bool
    {
        return in_array($tag, $this->tags ?? []);
    }

    /**
     * Get subscriber engagement metrics
     */
    public function getEngagementMetrics()
    {
        $analytics = $this->analytics;
        
        return [
            'total_campaigns' => $analytics->where('event_type', 'delivered')->count(),
            'opens' => $analytics->where('event_type', 'opened')->count(),
            'clicks' => $analytics->where('event_type', 'clicked')->count(),
            'unsubscribes' => $analytics->where('event_type', 'unsubscribed')->count(),
            'last_activity' => $analytics->max('event_timestamp'),
        ];
    }

    /**
     * Get status color for UI
     */
    public function getStatusColor(): string
    {
        return match($this->status) {
            'subscribed' => '#10B981',
            'unsubscribed' => '#6B7280',
            'bounced' => '#EF4444',
            'complained' => '#EF4444',
            default => '#6B7280'
        };
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by workspace
     */
    public function scopeByWorkspace($query, $workspaceId)
    {
        return $query->where('workspace_id', $workspaceId);
    }

    /**
     * Scope for filtering by tags
     */
    public function scopeByTag($query, $tag)
    {
        return $query->whereJsonContains('tags', $tag);
    }
}