<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MobileSession extends Model
{
    protected $table = 'mobile_sessions';
    
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'user_id',
        'device_type',
        'platform',
        'device_info',
        'app_version',
        'screen_size',
        'session_start',
        'session_end',
        'ip_address',
        'user_agent',
        'session_data'
    ];

    protected $casts = [
        'device_info' => 'array',
        'screen_size' => 'array',
        'session_data' => 'array',
        'session_start' => 'datetime',
        'session_end' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

    /**
     * Get the user that owns the mobile session
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if session is active
     */
    public function isActive()
    {
        return $this->session_end === null;
    }

    /**
     * Get session duration
     */
    public function getDuration()
    {
        if ($this->session_end) {
            return $this->session_start->diffInMinutes($this->session_end);
        }
        
        return $this->session_start->diffInMinutes(now());
    }

    /**
     * End the session
     */
    public function endSession()
    {
        $this->update([
            'session_end' => now()
        ]);
    }

    /**
     * Check if mobile device
     */
    public function isMobile()
    {
        return $this->device_type === 'mobile';
    }

    /**
     * Check if tablet device
     */
    public function isTablet()
    {
        return $this->device_type === 'tablet';
    }

    /**
     * Check if iOS platform
     */
    public function isIOS()
    {
        return $this->platform === 'ios';
    }

    /**
     * Check if Android platform
     */
    public function isAndroid()
    {
        return $this->platform === 'android';
    }
}