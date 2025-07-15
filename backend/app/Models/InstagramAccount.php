<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstagramAccount extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'workspace_id',
        'user_id',
        'username',
        'instagram_user_id',
        'profile_picture_url',
        'bio',
        'followers_count',
        'following_count',
        'media_count',
        'access_token',
        'token_expires_at',
        'is_connected',
        'is_primary',
        'account_info'
    ];
    
    protected $casts = [
        'account_info' => 'array',
        'token_expires_at' => 'datetime',
        'is_connected' => 'boolean',
        'is_primary' => 'boolean'
    ];
    
    protected $hidden = [
        'access_token'
    ];
    
    public function workspace()
    {
        return $this->belongsTo(Organization::class, 'workspace_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function posts()
    {
        return $this->hasMany(InstagramPost::class, 'workspace_id', 'workspace_id');
    }
    
    public function scopeConnected($query)
    {
        return $query->where('is_connected', true);
    }
    
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }
    
    public function isTokenValid()
    {
        return $this->access_token && 
               $this->token_expires_at && 
               $this->token_expires_at->isFuture();
    }
    
    public function getEngagementRate()
    {
        $recentPosts = $this->posts()
            ->where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->limit(10)
            ->get();
        
        if ($recentPosts->isEmpty()) {
            return 0;
        }
        
        $totalEngagement = $recentPosts->sum(function($post) {
            return $post->getEngagementRate();
        });
        
        return $totalEngagement / $recentPosts->count();
    }
}
