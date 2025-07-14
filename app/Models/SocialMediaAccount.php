<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SocialMediaAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'platform',
        'username',
        'display_name',
        'access_token',
        'access_token_secret',
        'avatar_url',
        'followers_count',
        'following_count',
        'is_active',
        'connected_at',
        'metadata',
    ];

    protected $casts = [
        'followers_count' => 'integer',
        'following_count' => 'integer',
        'is_active' => 'boolean',
        'connected_at' => 'datetime',
        'metadata' => 'array',
    ];

    protected $hidden = [
        'access_token',
        'access_token_secret',
    ];

    /**
     * Get the user that owns the social media account
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the posts associated with this account
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(SocialMediaPost::class, 'social_media_post_accounts');
    }

    /**
     * Scope for active accounts
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific platform
     */
    public function scopePlatform($query, $platform)
    {
        return $query->where('platform', $platform);
    }
}