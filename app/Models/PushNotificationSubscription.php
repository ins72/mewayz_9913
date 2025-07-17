<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PushNotificationSubscription extends Model
{
    protected $table = 'push_notification_subscriptions';
    
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'user_id',
        'endpoint',
        'p256dh_key',
        'auth_key',
        'user_agent',
        'notification_types',
        'quiet_hours',
        'frequency',
        'subscribed_at',
        'unsubscribed_at'
    ];

    protected $casts = [
        'notification_types' => 'array',
        'quiet_hours' => 'array',
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

    /**
     * Get the user that owns the subscription
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if subscription is active
     */
    public function isActive()
    {
        return $this->unsubscribed_at === null;
    }

    /**
     * Unsubscribe from push notifications
     */
    public function unsubscribe()
    {
        $this->update([
            'unsubscribed_at' => now()
        ]);
    }

    /**
     * Resubscribe to push notifications
     */
    public function resubscribe()
    {
        $this->update([
            'unsubscribed_at' => null
        ]);
    }

    /**
     * Check if notification type is enabled
     */
    public function isNotificationTypeEnabled($type)
    {
        return in_array($type, $this->notification_types ?? []);
    }

    /**
     * Check if within quiet hours
     */
    public function isQuietHours()
    {
        if (!$this->quiet_hours) {
            return false;
        }

        $now = now();
        $start = $this->quiet_hours['start'] ?? null;
        $end = $this->quiet_hours['end'] ?? null;

        if ($start && $end) {
            $startTime = $now->copy()->setTimeFromTimeString($start);
            $endTime = $now->copy()->setTimeFromTimeString($end);

            return $now->between($startTime, $endTime);
        }

        return false;
    }

    /**
     * Get subscription data for push service
     */
    public function getSubscriptionData()
    {
        return [
            'endpoint' => $this->endpoint,
            'keys' => [
                'p256dh' => $this->p256dh_key,
                'auth' => $this->auth_key
            ]
        ];
    }
}