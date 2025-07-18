<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstagramPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'user_id',
        'post_id',
        'caption',
        'media_type',
        'media_url',
        'permalink',
        'likes_count',
        'comments_count',
        'shares_count',
        'impressions_count',
        'reach_count',
        'posted_at',
        'is_active'
    ];

    protected $casts = [
        'likes_count' => 'integer',
        'comments_count' => 'integer',
        'shares_count' => 'integer',
        'impressions_count' => 'integer',
        'reach_count' => 'integer',
        'posted_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    public function account()
    {
        return $this->belongsTo(SocialMediaAccount::class, 'account_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}