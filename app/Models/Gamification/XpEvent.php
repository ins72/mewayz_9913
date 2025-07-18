<?php

namespace App\Models\Gamification;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class XpEvent extends Model
{
    protected $table = 'gamification_xp_events';
    
    protected $fillable = [
        'user_id', 'event_type', 'event_category', 'xp_amount', 'multiplier', 'final_xp',
        'source_type', 'source_id', 'description', 'metadata', 'is_bonus', 'bonus_reason'
    ];

    protected $casts = [
        'xp_amount' => 'integer',
        'multiplier' => 'integer',
        'final_xp' => 'integer',
        'source_id' => 'integer',
        'metadata' => 'array',
        'is_bonus' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function source()
    {
        if ($this->source_type && $this->source_id) {
            return $this->belongsTo($this->source_type, 'source_id');
        }
        return null;
    }

    public function scopeByEventType($query, $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('event_category', $category);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeBonus($query)
    {
        return $query->where('is_bonus', true);
    }

    public static function getEventTypes()
    {
        return [
            'login' => 'Daily Login',
            'profile_complete' => 'Profile Completion',
            'post_created' => 'Post Created',
            'bio_site_created' => 'Bio Site Created',
            'bio_site_updated' => 'Bio Site Updated',
            'course_created' => 'Course Created',
            'course_completed' => 'Course Completed',
            'email_campaign_sent' => 'Email Campaign Sent',
            'social_media_post' => 'Social Media Post',
            'achievement_completed' => 'Achievement Completed',
            'challenge_completed' => 'Challenge Completed',
            'streak_milestone' => 'Streak Milestone',
            'referral_signup' => 'Referral Signup',
            'subscription_upgrade' => 'Subscription Upgrade',
            'revenue_milestone' => 'Revenue Milestone',
            'engagement_milestone' => 'Engagement Milestone'
        ];
    }

    public static function getEventCategories()
    {
        return [
            'general' => 'General',
            'engagement' => 'Engagement',
            'content' => 'Content Creation',
            'learning' => 'Learning',
            'business' => 'Business',
            'social' => 'Social',
            'milestone' => 'Milestone'
        ];
    }

    public static function getXpForEventType($eventType)
    {
        $xpMapping = [
            'login' => 5,
            'profile_complete' => 50,
            'post_created' => 10,
            'bio_site_created' => 100,
            'bio_site_updated' => 25,
            'course_created' => 200,
            'course_completed' => 150,
            'email_campaign_sent' => 75,
            'social_media_post' => 15,
            'achievement_completed' => 0, // XP awarded by achievement
            'challenge_completed' => 0, // XP awarded by challenge
            'streak_milestone' => 50,
            'referral_signup' => 100,
            'subscription_upgrade' => 250,
            'revenue_milestone' => 500,
            'engagement_milestone' => 300
        ];

        return $xpMapping[$eventType] ?? 10;
    }

    public function getEventTypeLabel()
    {
        $eventTypes = self::getEventTypes();
        return $eventTypes[$this->event_type] ?? ucfirst(str_replace('_', ' ', $this->event_type));
    }

    public function getCategoryLabel()
    {
        $categories = self::getEventCategories();
        return $categories[$this->event_category] ?? ucfirst($this->event_category);
    }
}