<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstagramHashtag extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'workspace_id',
        'hashtag',
        'post_count',
        'engagement_rate',
        'difficulty',
        'is_trending',
        'related_hashtags',
        'analytics'
    ];
    
    protected $casts = [
        'related_hashtags' => 'array',
        'analytics' => 'array',
        'is_trending' => 'boolean',
        'engagement_rate' => 'decimal:2'
    ];
    
    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }
    
    public function scopeTrending($query)
    {
        return $query->where('is_trending', true);
    }
    
    public function scopeByDifficulty($query, $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }
    
    public function scopePopular($query)
    {
        return $query->where('post_count', '>', 100000);
    }
    
    public function scopeNiche($query)
    {
        return $query->where('post_count', '<', 100000);
    }
    
    public function getDifficultyColor()
    {
        return match($this->difficulty) {
            'easy' => '#28a745',
            'medium' => '#ffc107',
            'hard' => '#dc3545',
            default => '#6c757d'
        };
    }
    
    public function getFormattedPostCount()
    {
        if ($this->post_count >= 1000000) {
            return round($this->post_count / 1000000, 1) . 'M';
        } elseif ($this->post_count >= 1000) {
            return round($this->post_count / 1000, 1) . 'K';
        }
        return $this->post_count;
    }
}
