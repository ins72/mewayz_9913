<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UnifiedAnalyticsEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'workspace_id',
        'event_type',
        'event_category',
        'platform',
        'entity_id',
        'entity_type',
        'properties',
        'session_id',
        'visitor_id',
        'timestamp',
        'revenue',
        'conversion_value',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'referrer',
        'user_agent',
        'ip_address',
        'location_country',
        'location_city',
        'device_type',
        'browser',
        'os',
        'is_mobile',
        'screen_resolution',
        'page_url',
        'page_title',
        'duration',
        'scroll_depth',
        'custom_attributes'
    ];

    protected $casts = [
        'properties' => 'array',
        'custom_attributes' => 'array',
        'timestamp' => 'datetime',
        'revenue' => 'decimal:2',
        'conversion_value' => 'decimal:2',
        'is_mobile' => 'boolean',
        'duration' => 'integer',
        'scroll_depth' => 'integer'
    ];

    /**
     * Get the user that owns the event
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the workspace that owns the event
     */
    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    /**
     * Scope for specific platforms
     */
    public function scopeForPlatform($query, $platform)
    {
        return $query->where('platform', $platform);
    }

    /**
     * Scope for specific event types
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('event_type', $type);
    }

    /**
     * Scope for specific time range
     */
    public function scopeInTimeRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('timestamp', [$startDate, $endDate]);
    }

    /**
     * Scope for conversion events
     */
    public function scopeConversions($query)
    {
        return $query->where('event_category', 'conversion');
    }

    /**
     * Scope for revenue events
     */
    public function scopeRevenue($query)
    {
        return $query->whereNotNull('revenue')->where('revenue', '>', 0);
    }

    /**
     * Get events for a specific entity
     */
    public function scopeForEntity($query, $entityType, $entityId)
    {
        return $query->where('entity_type', $entityType)
                    ->where('entity_id', $entityId);
    }

    /**
     * Get events for a specific session
     */
    public function scopeForSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    /**
     * Get events for a specific visitor
     */
    public function scopeForVisitor($query, $visitorId)
    {
        return $query->where('visitor_id', $visitorId);
    }

    /**
     * Get events with UTM parameters
     */
    public function scopeWithUTM($query)
    {
        return $query->whereNotNull('utm_source');
    }

    /**
     * Get mobile events
     */
    public function scopeMobile($query)
    {
        return $query->where('is_mobile', true);
    }

    /**
     * Get desktop events
     */
    public function scopeDesktop($query)
    {
        return $query->where('is_mobile', false);
    }

    /**
     * Get events from specific country
     */
    public function scopeFromCountry($query, $country)
    {
        return $query->where('location_country', $country);
    }

    /**
     * Get events with specific property
     */
    public function scopeWithProperty($query, $key, $value = null)
    {
        if ($value === null) {
            return $query->whereJsonContains('properties', [$key]);
        }
        
        return $query->whereJsonContains('properties', [$key => $value]);
    }

    /**
     * Calculate conversion rate for a set of events
     */
    public static function calculateConversionRate($events)
    {
        $totalEvents = $events->count();
        $conversionEvents = $events->where('event_category', 'conversion')->count();
        
        if ($totalEvents === 0) {
            return 0;
        }
        
        return ($conversionEvents / $totalEvents) * 100;
    }

    /**
     * Calculate total revenue for a set of events
     */
    public static function calculateTotalRevenue($events)
    {
        return $events->sum('revenue');
    }

    /**
     * Get unique visitors count
     */
    public static function getUniqueVisitors($events)
    {
        return $events->distinct('visitor_id')->count();
    }

    /**
     * Get unique sessions count
     */
    public static function getUniqueSessions($events)
    {
        return $events->distinct('session_id')->count();
    }

    /**
     * Get platform distribution
     */
    public static function getPlatformDistribution($events)
    {
        return $events->groupBy('platform')
                     ->map(function ($platformEvents) {
                         return $platformEvents->count();
                     });
    }

    /**
     * Get device type distribution
     */
    public static function getDeviceTypeDistribution($events)
    {
        return $events->groupBy('device_type')
                     ->map(function ($deviceEvents) {
                         return $deviceEvents->count();
                     });
    }

    /**
     * Get geographic distribution
     */
    public static function getGeographicDistribution($events)
    {
        return $events->groupBy('location_country')
                     ->map(function ($countryEvents) {
                         return $countryEvents->count();
                     });
    }

    /**
     * Get hourly distribution
     */
    public static function getHourlyDistribution($events)
    {
        return $events->groupBy(function ($event) {
            return $event->timestamp->format('H');
        })->map(function ($hourEvents) {
            return $hourEvents->count();
        });
    }

    /**
     * Get daily distribution
     */
    public static function getDailyDistribution($events)
    {
        return $events->groupBy(function ($event) {
            return $event->timestamp->format('Y-m-d');
        })->map(function ($dayEvents) {
            return $dayEvents->count();
        });
    }

    /**
     * Get event funnel analysis
     */
    public static function getFunnelAnalysis($events, $funnelSteps)
    {
        $funnelData = [];
        
        foreach ($funnelSteps as $step) {
            $stepEvents = $events->where('event_type', $step);
            $funnelData[$step] = [
                'count' => $stepEvents->count(),
                'unique_visitors' => $stepEvents->distinct('visitor_id')->count(),
                'conversion_rate' => self::calculateConversionRate($stepEvents)
            ];
        }
        
        return $funnelData;
    }

    /**
     * Get attribution analysis
     */
    public static function getAttributionAnalysis($events)
    {
        $conversionEvents = $events->where('event_category', 'conversion');
        
        $firstTouch = $conversionEvents->groupBy('utm_source')
                                     ->map(function ($sourceEvents) {
                                         return $sourceEvents->count();
                                     });
        
        $lastTouch = $conversionEvents->groupBy('utm_medium')
                                    ->map(function ($mediumEvents) {
                                        return $mediumEvents->count();
                                    });
        
        return [
            'first_touch' => $firstTouch,
            'last_touch' => $lastTouch,
            'total_conversions' => $conversionEvents->count(),
            'total_revenue' => $conversionEvents->sum('revenue')
        ];
    }

    /**
     * Get customer journey for a specific visitor
     */
    public static function getCustomerJourney($visitorId, $events)
    {
        $visitorEvents = $events->where('visitor_id', $visitorId)
                              ->sortBy('timestamp');
        
        $journey = [];
        
        foreach ($visitorEvents as $event) {
            $journey[] = [
                'timestamp' => $event->timestamp,
                'platform' => $event->platform,
                'event_type' => $event->event_type,
                'event_category' => $event->event_category,
                'page_url' => $event->page_url,
                'page_title' => $event->page_title,
                'duration' => $event->duration,
                'revenue' => $event->revenue,
                'properties' => $event->properties
            ];
        }
        
        return $journey;
    }

    /**
     * Get engagement score for a visitor
     */
    public static function getEngagementScore($visitorId, $events)
    {
        $visitorEvents = $events->where('visitor_id', $visitorId);
        
        $score = 0;
        
        // Base score for each event
        $score += $visitorEvents->count() * 1;
        
        // Bonus for conversions
        $score += $visitorEvents->where('event_category', 'conversion')->count() * 10;
        
        // Bonus for revenue events
        $score += $visitorEvents->whereNotNull('revenue')->count() * 5;
        
        // Bonus for engagement duration
        $totalDuration = $visitorEvents->sum('duration');
        $score += ($totalDuration / 60) * 0.5; // 0.5 points per minute
        
        // Bonus for scroll depth
        $avgScrollDepth = $visitorEvents->avg('scroll_depth');
        $score += ($avgScrollDepth / 100) * 2; // 2 points for full scroll
        
        return round($score, 2);
    }

    /**
     * Track a unified analytics event
     */
    public static function track($eventData)
    {
        return self::create([
            'user_id' => $eventData['user_id'] ?? null,
            'workspace_id' => $eventData['workspace_id'] ?? null,
            'event_type' => $eventData['event_type'],
            'event_category' => $eventData['event_category'] ?? 'engagement',
            'platform' => $eventData['platform'],
            'entity_id' => $eventData['entity_id'] ?? null,
            'entity_type' => $eventData['entity_type'] ?? null,
            'properties' => $eventData['properties'] ?? [],
            'session_id' => $eventData['session_id'] ?? null,
            'visitor_id' => $eventData['visitor_id'] ?? null,
            'timestamp' => $eventData['timestamp'] ?? now(),
            'revenue' => $eventData['revenue'] ?? null,
            'conversion_value' => $eventData['conversion_value'] ?? null,
            'utm_source' => $eventData['utm_source'] ?? null,
            'utm_medium' => $eventData['utm_medium'] ?? null,
            'utm_campaign' => $eventData['utm_campaign'] ?? null,
            'utm_term' => $eventData['utm_term'] ?? null,
            'utm_content' => $eventData['utm_content'] ?? null,
            'referrer' => $eventData['referrer'] ?? null,
            'user_agent' => $eventData['user_agent'] ?? null,
            'ip_address' => $eventData['ip_address'] ?? null,
            'location_country' => $eventData['location_country'] ?? null,
            'location_city' => $eventData['location_city'] ?? null,
            'device_type' => $eventData['device_type'] ?? null,
            'browser' => $eventData['browser'] ?? null,
            'os' => $eventData['os'] ?? null,
            'is_mobile' => $eventData['is_mobile'] ?? false,
            'screen_resolution' => $eventData['screen_resolution'] ?? null,
            'page_url' => $eventData['page_url'] ?? null,
            'page_title' => $eventData['page_title'] ?? null,
            'duration' => $eventData['duration'] ?? null,
            'scroll_depth' => $eventData['scroll_depth'] ?? null,
            'custom_attributes' => $eventData['custom_attributes'] ?? []
        ]);
    }
}