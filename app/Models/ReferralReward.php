<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ReferralReward extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'referral_id',
        'reward_type',
        'amount',
        'currency',
        'description',
        'status',
        'paid_at',
        'payment_method',
        'payment_reference',
        'metadata',
    ];

    protected $casts = [
        'id' => 'string',
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
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

    public function referral(): BelongsTo
    {
        return $this->belongsTo(Referral::class);
    }

    // Accessors
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pending Payment',
            'paid' => 'Paid',
            'cancelled' => 'Cancelled',
            'failed' => 'Failed',
            default => 'Unknown'
        };
    }

    public function getRewardTypeLabelAttribute(): string
    {
        return match($this->reward_type) {
            'commission' => 'Commission',
            'bonus' => 'Bonus',
            'credit' => 'Credit',
            default => 'Unknown'
        };
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2) . ' ' . strtoupper($this->currency);
    }

    public function getDaysToPayAttribute(): ?int
    {
        if (!$this->paid_at) {
            return null;
        }

        return $this->created_at->diffInDays($this->paid_at);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByRewardType($query, $type)
    {
        return $query->where('reward_type', $type);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeInPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeRecentlyPaid($query, $days = 30)
    {
        return $query->where('status', 'paid')
            ->where('paid_at', '>=', now()->subDays($days));
    }

    // Business Logic
    public function markAsPaid(string $paymentMethod = null, string $paymentReference = null): void
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
            'payment_method' => $paymentMethod,
            'payment_reference' => $paymentReference,
        ]);
    }

    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
    }

    public function markAsFailed(): void
    {
        $this->update(['status' => 'failed']);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function canBePaid(): bool
    {
        return $this->status === 'pending' && $this->amount > 0;
    }

    public function canBeCancelled(): bool
    {
        return $this->status === 'pending';
    }
}