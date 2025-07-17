<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class BookingService extends Model
{
    use HasFactory;

    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [
        'user_id',
        'name',
        'thumbnail',
        'price',
        'duration',
        'settings',
        'booking_workhours',
        'booking_time_interval',
        'gallery',
        'description',
        'status',
        'position',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'settings' => 'array',
        'booking_workhours' => 'array',
        'gallery' => 'array',
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

    public function appointments(): HasMany
    {
        return $this->hasMany(BookingAppointment::class);
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(BookingAvailability::class);
    }

    public function calendar(): HasMany
    {
        return $this->hasMany(BookingCalendar::class);
    }

    // Accessors
    public function getFormattedPriceAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->price, 2);
    }

    public function getDurationFormattedAttribute(): string
    {
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;
        
        if ($hours > 0) {
            return $hours . 'h ' . ($minutes > 0 ? $minutes . 'm' : '');
        }
        
        return $minutes . 'm';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Business Logic Methods
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    public function getBookingStats(): array
    {
        return [
            'total_bookings' => $this->appointments()->count(),
            'confirmed_bookings' => $this->appointments()->where('status', 'confirmed')->count(),
            'completed_bookings' => $this->appointments()->where('status', 'completed')->count(),
            'total_revenue' => $this->appointments()->where('status', 'completed')->sum('total_amount'),
        ];
    }
}