<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AnalyticsEvent extends Model
{
    protected $table = 'analytics_events';
    
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'user_id',
        'workspace_id',
        'event_type',
        'event_name',
        'event_data',
        'user_properties',
        'session_id',
        'device_type',
        'platform',
        'ip_address',
        'user_agent',
        'referrer',
        'event_time'
    ];

    protected $casts = [
        'event_data' => 'array',
        'user_properties' => 'array',
        'event_time' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

    /**
     * Get the user that performed the event
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the workspace where the event occurred
     */
    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    /**
     * Scope to filter by event type
     */
    public function scopeByEventType($query, $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('event_time', [$startDate, $endDate]);
    }

    /**
     * Scope to filter by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by workspace
     */
    public function scopeByWorkspace($query, $workspaceId)
    {
        return $query->where('workspace_id', $workspaceId);
    }

    /**
     * Get events for analytics dashboard
     */
    public static function getAnalyticsData($filters = [])
    {
        $query = self::query();

        if (!empty($filters['event_type'])) {
            $query->byEventType($filters['event_type']);
        }

        if (!empty($filters['user_id'])) {
            $query->byUser($filters['user_id']);
        }

        if (!empty($filters['workspace_id'])) {
            $query->byWorkspace($filters['workspace_id']);
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->byDateRange($filters['start_date'], $filters['end_date']);
        }

        return $query->orderBy('event_time', 'desc')->get();
    }

    /**
     * Get event counts by type
     */
    public static function getEventCounts($filters = [])
    {
        $query = self::query();

        if (!empty($filters['user_id'])) {
            $query->byUser($filters['user_id']);
        }

        if (!empty($filters['workspace_id'])) {
            $query->byWorkspace($filters['workspace_id']);
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->byDateRange($filters['start_date'], $filters['end_date']);
        }

        return $query->selectRaw('event_type, event_name, COUNT(*) as count')
            ->groupBy('event_type', 'event_name')
            ->orderBy('count', 'desc')
            ->get();
    }

    /**
     * Get unique users count
     */
    public static function getUniqueUsersCount($filters = [])
    {
        $query = self::query();

        if (!empty($filters['workspace_id'])) {
            $query->byWorkspace($filters['workspace_id']);
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->byDateRange($filters['start_date'], $filters['end_date']);
        }

        return $query->distinct('user_id')->count('user_id');
    }
}