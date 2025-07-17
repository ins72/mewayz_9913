<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class BookingAvailability extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'service_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_available',
        'max_bookings',
        'break_start_time',
        'break_end_time',
    ];

    protected $casts = [
        'id' => 'string',
        'is_available' => 'boolean',
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
    public function service(): BelongsTo
    {
        return $this->belongsTo(BookingService::class);
    }

    // Accessors
    public function getDayDisplayAttribute(): string
    {
        return ucfirst($this->day_of_week);
    }

    public function getTimeRangeAttribute(): string
    {
        if (!$this->is_available || !$this->start_time || !$this->end_time) {
            return 'Unavailable';
        }

        return $this->start_time . ' - ' . $this->end_time;
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeForDay($query, $dayOfWeek)
    {
        return $query->where('day_of_week', strtolower($dayOfWeek));
    }

    // Business Logic Methods
    public function hasBreak(): bool
    {
        return !empty($this->break_start_time) && !empty($this->break_end_time);
    }

    public function getWorkingMinutes(): int
    {
        if (!$this->is_available || !$this->start_time || !$this->end_time) {
            return 0;
        }

        $start = \Carbon\Carbon::parse($this->start_time);
        $end = \Carbon\Carbon::parse($this->end_time);
        $totalMinutes = $end->diffInMinutes($start);

        if ($this->hasBreak()) {
            $breakStart = \Carbon\Carbon::parse($this->break_start_time);
            $breakEnd = \Carbon\Carbon::parse($this->break_end_time);
            $breakMinutes = $breakEnd->diffInMinutes($breakStart);
            $totalMinutes -= $breakMinutes;
        }

        return $totalMinutes;
    }
}