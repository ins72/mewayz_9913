<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentFailure extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'stripe_invoice_id',
        'failure_reason',
        'failure_code',
        'retry_attempt',
        'next_retry_at',
        'resolved_at',
        'resolution_method',
        'metadata',
    ];

    protected $casts = [
        'retry_attempt' => 'integer',
        'next_retry_at' => 'datetime',
        'resolved_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the subscription that owns this payment failure.
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Scope for unresolved payment failures.
     */
    public function scopeUnresolved($query)
    {
        return $query->whereNull('resolved_at');
    }

    /**
     * Scope for resolved payment failures.
     */
    public function scopeResolved($query)
    {
        return $query->whereNotNull('resolved_at');
    }

    /**
     * Check if this payment failure is resolved.
     */
    public function isResolved(): bool
    {
        return $this->resolved_at !== null;
    }

    /**
     * Check if this payment failure is pending retry.
     */
    public function isPendingRetry(): bool
    {
        return $this->next_retry_at && $this->next_retry_at->isFuture() && !$this->isResolved();
    }

    /**
     * Check if this payment failure is ready for retry.
     */
    public function isReadyForRetry(): bool
    {
        return $this->next_retry_at && $this->next_retry_at->isPast() && !$this->isResolved();
    }

    /**
     * Mark this payment failure as resolved.
     */
    public function markAsResolved(string $resolutionMethod = 'payment_succeeded'): void
    {
        $this->update([
            'resolved_at' => now(),
            'resolution_method' => $resolutionMethod,
        ]);
    }

    /**
     * Get the failure reason display name.
     */
    public function getFailureReasonDisplayName(): string
    {
        return match ($this->failure_code) {
            'card_declined' => 'Card was declined',
            'insufficient_funds' => 'Insufficient funds',
            'expired_card' => 'Card has expired',
            'incorrect_cvc' => 'Incorrect CVC',
            'processing_error' => 'Processing error',
            'authentication_required' => 'Authentication required',
            default => $this->failure_reason ?? 'Unknown error',
        };
    }

    /**
     * Get the next retry date in a human-readable format.
     */
    public function getNextRetryDateForHumans(): ?string
    {
        if (!$this->next_retry_at) {
            return null;
        }

        return $this->next_retry_at->diffForHumans();
    }

    /**
     * Get the retry strategy for this failure.
     */
    public function getRetryStrategy(): array
    {
        $retryDelays = [
            1 => 1,    // 1 day
            2 => 3,    // 3 days
            3 => 5,    // 5 days
            4 => 7,    // 7 days
            5 => 14,   // 14 days
            6 => 21,   // 21 days
            7 => 28,   // 28 days
        ];

        return [
            'max_attempts' => count($retryDelays),
            'next_delay' => $retryDelays[$this->retry_attempt + 1] ?? null,
            'should_retry' => $this->retry_attempt < count($retryDelays) && !$this->isResolved(),
        ];
    }

    /**
     * Schedule the next retry attempt.
     */
    public function scheduleNextRetry(): void
    {
        $strategy = $this->getRetryStrategy();
        
        if ($strategy['should_retry'] && $strategy['next_delay']) {
            $this->update([
                'retry_attempt' => $this->retry_attempt + 1,
                'next_retry_at' => now()->addDays($strategy['next_delay']),
            ]);
        }
    }
}