<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserPreference extends Model
{
    protected $table = 'user_preferences';
    
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'user_id',
        'theme',
        'language',
        'timezone',
        'primary_goals',
        'business_type',
        'experience_level',
        'team_size',
        'recommended_features',
        'feature_priorities',
        'notification_preferences',
        'dashboard_layout',
        'accessibility_options',
        'privacy_settings'
    ];

    protected $casts = [
        'primary_goals' => 'array',
        'recommended_features' => 'array',
        'feature_priorities' => 'array',
        'notification_preferences' => 'array',
        'dashboard_layout' => 'array',
        'accessibility_options' => 'array',
        'privacy_settings' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

    /**
     * Get the user that owns the preferences
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get default preferences
     */
    public static function getDefaults()
    {
        return [
            'theme' => 'dark',
            'language' => 'en',
            'timezone' => 'UTC',
            'primary_goals' => [],
            'business_type' => null,
            'experience_level' => 'beginner',
            'team_size' => 1,
            'recommended_features' => [],
            'feature_priorities' => [],
            'notification_preferences' => [
                'email' => true,
                'push' => true,
                'sms' => false,
                'marketing' => true
            ],
            'dashboard_layout' => [
                'widgets' => ['overview', 'recent_activity', 'quick_actions'],
                'layout' => 'grid'
            ],
            'accessibility_options' => [
                'high_contrast' => false,
                'reduced_motion' => false,
                'large_text' => false
            ],
            'privacy_settings' => [
                'profile_visibility' => 'private',
                'data_sharing' => false,
                'analytics_tracking' => true
            ]
        ];
    }

    /**
     * Check if user prefers dark theme
     */
    public function isDarkTheme()
    {
        return $this->theme === 'dark';
    }

    /**
     * Get localized preferences
     */
    public function getLocalizedPreferences()
    {
        return [
            'theme' => $this->theme,
            'language' => $this->language,
            'timezone' => $this->timezone,
            'accessibility' => $this->accessibility_options
        ];
    }
}