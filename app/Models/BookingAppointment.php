<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BookingAppointment extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'service_id',
        'user_id',
        'start_time',
        'end_time',
        'client_name',
        'client_email',
        'client_phone',
        'notes',
        'status',
        'status_notes',
        'status_updated_at',
        'total_amount',
        'currency',
        'payment_status',
        'payment_id',
        'timezone',
        'booking_reference',
        'reminder_sent_at',
        'follow_up_sent_at',
    ];

    protected $casts = [
        'id' => 'string',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'status_updated_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'follow_up_sent_at' => 'datetime',
        'total_amount' => 'decimal:2',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Accessors
    public function getStatusBadgeAttribute(): array
    {
        $statusMap = [
            'pending' => ['color' => 'warning', 'text' => 'Pending'],
            'confirmed' => ['color' => 'success', 'text' => 'Confirmed'],
            'completed' => ['color' => 'info', 'text' => 'Completed'],
            'cancelled' => ['color' => 'error', 'text' => 'Cancelled'],
            'no_show' => ['color' => 'secondary', 'text' => 'No Show'],
        ];

        return $statusMap[$this->status] ?? ['color' => 'secondary', 'text' => ucfirst($this->status)];
    }

    public function getTimeUntilAppointmentAttribute(): ?string
    {
        if ($this->start_time->isPast()) {
            return null;
        }

        $diff = now()->diffInHours($this->start_time, false);
        
        if ($diff < 24) {
            return $diff . ' hours';
        }
        
        return ceil($diff / 24) . ' days';
    }

    public function getFormattedDateTimeAttribute(): string
    {
        return $this->start_time->format('M j, Y \a\t g:i A');
    }

    // Scopes
    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>', now())
                    ->whereIn('status', ['pending', 'confirmed']);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('start_time', today());
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Business Logic Methods
    public function confirm(): void
    {
        $this->update([
            'status' => 'confirmed',
            'status_updated_at' => now(),
        ]);
    }

    public function complete(): void
    {
        $this->update([
            'status' => 'completed',
            'status_updated_at' => now(),
        ]);
    }

    public function cancel(string $reason = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'status_notes' => $reason,
            'status_updated_at' => now(),
        ]);
    }

    public function markNoShow(): void
    {
        $this->update([
            'status' => 'no_show',
            'status_updated_at' => now(),
        ]);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']) && 
               $this->start_time->isFuture();
    }

    public function canBeRescheduled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']) && 
               $this->start_time->isFuture();
    }

    public function needsReminder(): bool
    {
        return $this->status === 'confirmed' && 
               !$this->reminder_sent_at && 
               $this->start_time->diffInHours(now()) <= 24;
    }
}