<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SocialMediaPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'content',
        'media_urls',
        'hashtags',
        'scheduled_at',
        'published_at',
        'status',
        'post_type',
        'platform_post_ids',
        'engagement_data',
        'error_message',
    ];

    protected $casts = [
        'media_urls' => 'array',
        'hashtags' => 'array',
        'scheduled_at' => 'datetime',
        'published_at' => 'datetime',
        'platform_post_ids' => 'array',
        'engagement_data' => 'array',
    ];

    /**
     * Get the user that owns the post
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the social media accounts for this post
     */
    public function accounts(): BelongsToMany
    {
        return $this->belongsToMany(SocialMediaAccount::class, 'social_media_post_accounts');
    }

    /**
     * Scope for scheduled posts
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled')
                    ->where('scheduled_at', '>', now());
    }

    /**
     * Scope for published posts
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope for draft posts
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }
}