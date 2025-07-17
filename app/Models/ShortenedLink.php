<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ShortenedLink extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'workspace_id',
        'original_url',
        'slug',
        'title',
        'description',
        'click_count',
        'expires_at',
        'password',
        'is_public',
        'is_active',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_content',
        'utm_term',
        'metadata',
    ];

    protected $casts = [
        'id' => 'string',
        'expires_at' => 'datetime',
        'is_public' => 'boolean',
        'is_active' => 'boolean',
        'click_count' => 'integer',
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
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(LinkClick::class);
    }

    // Accessors
    public function getShortUrlAttribute(): string
    {
        return url("/l/{$this->slug}");
    }

    public function getQrCodeUrlAttribute(): string
    {
        return url("/qr/{$this->slug}");
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at < now();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeByWorkspace($query, $workspaceId)
    {
        return $query->where('workspace_id', $workspaceId);
    }

    // Business Logic
    public function incrementClick(): void
    {
        $this->increment('click_count');
    }

    public function disable(): void
    {
        $this->update(['is_active' => false]);
    }

    public function enable(): void
    {
        $this->update(['is_active' => true]);
    }

    public function isPasswordProtected(): bool
    {
        return !empty($this->password);
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    public function getClicksInPeriod(int $days = 30): int
    {
        return $this->clicks()
            ->where('created_at', '>=', now()->subDays($days))
            ->count();
    }

    public function getTopReferrers(int $limit = 10)
    {
        return $this->clicks()
            ->selectRaw('referrer, COUNT(*) as count')
            ->groupBy('referrer')
            ->orderBy('count', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getClicksByDate(int $days = 30)
    {
        return $this->clicks()
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();
    }

    public function getDeviceBreakdown()
    {
        return $this->clicks()
            ->selectRaw('device_type, COUNT(*) as count')
            ->groupBy('device_type')
            ->orderBy('count', 'desc')
            ->get();
    }
}