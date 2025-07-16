<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class EmailCampaignAnalytics extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'subscriber_id',
        'event_type',
        'event_timestamp',
        'user_agent',
        'ip_address',
        'event_data'
    ];

    protected $casts = [
        'event_data' => 'array',
        'event_timestamp' => 'datetime'
    ];

    /**
     * Get the campaign this analytics belongs to
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(EmailCampaign::class, 'campaign_id');
    }

    /**
     * Get the subscriber this analytics belongs to
     */
    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(EmailSubscriber::class, 'subscriber_id');
    }

    /**
     * Get event icon for UI
     */
    public function getEventIcon(): string
    {
        return match($this->event_type) {
            'sent' => 'paper-airplane',
            'delivered' => 'check-circle',
            'opened' => 'eye',
            'clicked' => 'cursor-click',
            'unsubscribed' => 'x-circle',
            'bounced' => 'exclamation-circle',
            'complained' => 'flag',
            default => 'information-circle'
        };
    }

    /**
     * Get event color for UI
     */
    public function getEventColor(): string
    {
        return match($this->event_type) {
            'sent' => '#3B82F6',
            'delivered' => '#10B981',
            'opened' => '#8B5CF6',
            'clicked' => '#F59E0B',
            'unsubscribed' => '#6B7280',
            'bounced' => '#EF4444',
            'complained' => '#EF4444',
            default => '#6B7280'
        };
    }

    /**
     * Get formatted event name
     */
    public function getFormattedEventName(): string
    {
        return match($this->event_type) {
            'sent' => 'Sent',
            'delivered' => 'Delivered',
            'opened' => 'Opened',
            'clicked' => 'Clicked',
            'unsubscribed' => 'Unsubscribed',
            'bounced' => 'Bounced',
            'complained' => 'Complained',
            default => 'Unknown'
        };
    }

    /**
     * Record a new event
     */
    public static function recordEvent(
        string $campaignId,
        string $subscriberId,
        string $eventType,
        ?string $userAgent = null,
        ?string $ipAddress = null,
        ?array $eventData = null
    ): self {
        return self::create([
            'campaign_id' => $campaignId,
            'subscriber_id' => $subscriberId,
            'event_type' => $eventType,
            'event_timestamp' => now(),
            'user_agent' => $userAgent,
            'ip_address' => $ipAddress,
            'event_data' => $eventData
        ]);
    }

    /**
     * Scope for filtering by campaign
     */
    public function scopeByCampaign($query, $campaignId)
    {
        return $query->where('campaign_id', $campaignId);
    }

    /**
     * Scope for filtering by subscriber
     */
    public function scopeBySubscriber($query, $subscriberId)
    {
        return $query->where('subscriber_id', $subscriberId);
    }

    /**
     * Scope for filtering by event type
     */
    public function scopeByEventType($query, $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('event_timestamp', [$startDate, $endDate]);
    }

    /**
     * Scope for recent events
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('event_timestamp', '>=', now()->subDays($days));
    }
}