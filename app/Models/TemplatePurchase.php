<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TemplatePurchase extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'template_id',
        'creator_id',
        'amount',
        'currency',
        'payment_method',
        'payment_token',
        'payment_reference',
        'status',
        'completed_at',
        'failure_reason',
        'metadata',
    ];

    protected $casts = [
        'id' => 'string',
        'amount' => 'decimal:2',
        'completed_at' => 'datetime',
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

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    // Accessors
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pending',
            'processing' => 'Processing',
            'completed' => 'Completed',
            'failed' => 'Failed',
            'refunded' => 'Refunded',
            'cancelled' => 'Cancelled',
            default => 'Unknown'
        };
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            'stripe' => 'Credit Card',
            'paypal' => 'PayPal',
            'wallet' => 'Wallet',
            default => 'Unknown'
        };
    }

    public function getFormattedAmountAttribute(): string
    {
        return '$' . number_format($this->amount, 2);
    }

    public function getCreatorEarningsAttribute(): float
    {
        // Assuming 70% goes to creator, 30% to platform
        return $this->amount * 0.70;
    }

    public function getPlatformCommissionAttribute(): float
    {
        return $this->amount * 0.30;
    }

    public function getDaysToCompleteAttribute(): ?int
    {
        if (!$this->completed_at) {
            return null;
        }

        return $this->created_at->diffInDays($this->completed_at);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByTemplate($query, $templateId)
    {
        return $query->where('template_id', $templateId);
    }

    public function scopeByCreator($query, $creatorId)
    {
        return $query->where('creator_id', $creatorId);
    }

    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    public function scopeInPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeRecentlyCompleted($query, $days = 30)
    {
        return $query->where('status', 'completed')
            ->where('completed_at', '>=', now()->subDays($days));
    }

    // Business Logic
    public function markAsCompleted(string $paymentReference = null): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'payment_reference' => $paymentReference,
        ]);
    }

    public function markAsFailed(string $reason = null): void
    {
        $this->update([
            'status' => 'failed',
            'failure_reason' => $reason,
        ]);
    }

    public function markAsRefunded(): void
    {
        $this->update(['status' => 'refunded']);
    }

    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function canBeRefunded(): bool
    {
        return $this->status === 'completed' && 
               $this->completed_at >= now()->subDays(30); // 30-day refund policy
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    public function processRefund(): bool
    {
        if (!$this->canBeRefunded()) {
            return false;
        }

        // Implement actual refund logic here
        $this->markAsRefunded();
        
        return true;
    }
}