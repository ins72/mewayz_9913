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

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'duration_minutes',
        'price',
        'currency',
        'buffer_time_before',
        'buffer_time_after',
        'max_advance_booking_days',
        'min_notice_hours',
        'is_active',
        'requires_approval',
        'max_bookings_per_day',
        'category',
        'location',
        'online_meeting_url',
        'preparation_instructions',
        'cancellation_policy',
    ];

    protected $casts = [
        'id' => 'string',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'requires_approval' => 'boolean',
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