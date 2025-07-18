<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialMediaAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'platform',
        'username',
        'account_id',
        'access_token',
        'refresh_token',
        'expires_at',
        'is_active',
        'followers_count',
        'following_count',
        'posts_count',
        'is_verified',
        'account_type',
        'last_sync_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'followers_count' => 'integer',
        'following_count' => 'integer',
        'posts_count' => 'integer',
        'is_verified' => 'boolean',
        'last_sync_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function posts()
    {
        return $this->hasMany(InstagramPost::class, 'account_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByPlatform($query, $platform)
    {
        return $query->where('platform', $platform);
    }
}