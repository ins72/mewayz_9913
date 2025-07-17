<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class LinkClick extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'shortened_link_id',
        'ip_address',
        'user_agent',
        'referrer',
        'country',
        'city',
        'device_type',
        'browser',
        'platform',
        'clicked_at',
        'session_id',
        'metadata',
    ];

    protected $casts = [
        'id' => 'string',
        'clicked_at' => 'datetime',
        'metadata' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    // Relationships
    public function shortenedLink(): BelongsTo
    {
        return $this->belongsTo(ShortenedLink::class);
    }

    // Accessors
    public function getIsUniqueAttribute(): bool
    {
        return $this->shortenedLink->clicks()
            ->where('ip_address', $this->ip_address)
            ->where('created_at', '<', $this->created_at)
            ->doesntExist();
    }

    public function getLocationAttribute(): string
    {
        return $this->city ? "{$this->city}, {$this->country}" : $this->country;
    }

    // Scopes
    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    public function scopeByCountry($query, $country)
    {
        return $query->where('country', $country);
    }

    public function scopeByDevice($query, $deviceType)
    {
        return $query->where('device_type', $deviceType);
    }

    public function scopeByReferrer($query, $referrer)
    {
        return $query->where('referrer', $referrer);
    }

    public function scopeUnique($query)
    {
        return $query->selectRaw('DISTINCT ip_address, shortened_link_id, MIN(created_at) as first_click')
            ->groupBy('ip_address', 'shortened_link_id');
    }

    // Business Logic
    public function isFromBot(): bool
    {
        $botPatterns = [
            'bot', 'crawler', 'spider', 'scraper', 'robot',
            'googlebot', 'bingbot', 'slurp', 'duckduckbot',
            'baiduspider', 'yandexbot', 'facebookexternalhit'
        ];

        $userAgent = strtolower($this->user_agent);
        
        foreach ($botPatterns as $pattern) {
            if (strpos($userAgent, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }

    public function isMobile(): bool
    {
        return $this->device_type === 'Mobile';
    }

    public function isDesktop(): bool
    {
        return $this->device_type === 'Desktop';
    }

    public function isTablet(): bool
    {
        return $this->device_type === 'Tablet';
    }
}