<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstagramPost extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'workspace_id',
        'user_id',
        'title',
        'caption',
        'media_urls',
        'hashtags',
        'post_type',
        'status',
        'scheduled_at',
        'published_at',
        'instagram_post_id',
        'analytics',
        'metadata'
    ];
    
    protected $casts = [
        'media_urls' => 'array',
        'hashtags' => 'array',
        'analytics' => 'array',
        'metadata' => 'array',
        'scheduled_at' => 'datetime',
        'published_at' => 'datetime'
    ];
    
    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }
    
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
    
    public function scopeDrafts($query)
    {
        return $query->where('status', 'draft');
    }
    
    public function isScheduled()
    {
        return $this->status === 'scheduled' && $this->scheduled_at && $this->scheduled_at->isFuture();
    }
    
    public function isPublished()
    {
        return $this->status === 'published' && $this->published_at;
    }
    
    public function getEngagementRate()
    {
        $analytics = $this->analytics ?? [];
        $likes = $analytics['likes_count'] ?? 0;
        $comments = $analytics['comments_count'] ?? 0;
        $followers = $analytics['followers_count'] ?? 1;
        
        return $followers > 0 ? (($likes + $comments) / $followers) * 100 : 0;
    }
}
