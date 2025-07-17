<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class BookingCalendar extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'service_id',
        'date',
        'start_time',
        'end_time',
        'is_available',
        'override_reason',
        'notes',
    ];

    protected $casts = [
        'id' => 'string',
        'date' => 'date',
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
        return $this->belongsTo(BookingService::class, 'service_id', 'id');
    }

    // Accessors
    public function getFormattedTimeAttribute(): string
    {
        return $this->start_time . ' - ' . $this->end_time;
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeByDate($query, $date)
    {
        return $query->where('date', $date);
    }

    public function scopeByService($query, $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }

    // Business Logic Methods
    public function makeUnavailable(string $reason = null): void
    {
        $this->update([
            'is_available' => false,
            'override_reason' => $reason
        ]);
    }

    public function makeAvailable(): void
    {
        $this->update([
            'is_available' => true,
            'override_reason' => null
        ]);
    }
}