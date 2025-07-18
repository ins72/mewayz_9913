<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RetentionAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'type',
        'reason',
        'feedback',
        'offer_type',
        'offer_data',
        'success',
        'metadata',
    ];

    protected $casts = [
        'success' => 'boolean',
        'offer_data' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Get the subscription that this retention attempt belongs to.
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Scope for successful retention attempts.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('success', true);
    }

    /**
     * Scope for failed retention attempts.
     */
    public function scopeFailed($query)
    {
        return $query->where('success', false);
    }

    /**
     * Scope for specific retention type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get the display name for the retention type.
     */
    public function getTypeDisplayName(): string
    {
        return match ($this->type) {
            'cancellation_save' => 'Cancellation Prevention',
            'payment_retry' => 'Payment Retry',
            'win_back' => 'Win Back Campaign',
            'upgrade_prevention' => 'Upgrade Prevention',
            default => ucwords(str_replace('_', ' ', $this->type)),
        };
    }

    /**
     * Get the display name for the cancellation reason.
     */
    public function getReasonDisplayName(): string
    {
        return match ($this->reason) {
            'too_expensive' => 'Too Expensive',
            'not_using_enough' => 'Not Using Enough',
            'missing_features' => 'Missing Features',
            'technical_issues' => 'Technical Issues',
            'found_alternative' => 'Found Alternative',
            'business_closure' => 'Business Closure',
            'temporary_pause' => 'Temporary Pause',
            default => ucwords(str_replace('_', ' ', $this->reason ?? 'Unknown')),
        ];
    }

    /**
     * Get the estimated value of this retention attempt.
     */
    public function getEstimatedValue(): float
    {
        if (!$this->success) {
            return 0;
        }

        // Estimate based on subscription value and retention duration
        $monthlyValue = $this->subscription?->getMonthlyCost() ?? 0;
        $estimatedRetentionMonths = match ($this->offer_type) {
            'discount' => $this->offer_data['duration_months'] ?? 12,
            'downgrade' => 12,
            'pause' => 3,
            'annual_discount' => 12,
            'feature_training' => 24,
            'priority_support' => 12,
            default => 12,
        };

        return $monthlyValue * $estimatedRetentionMonths;
    }

    /**
     * Get the retention attempt status.
     */
    public function getStatus(): string
    {
        if ($this->success) {
            return 'successful';
        }

        if ($this->created_at->diffInHours(now()) < 24) {
            return 'pending';
        }

        return 'failed';
    }

    /**
     * Get the retention attempt badge color.
     */
    public function getStatusBadgeColor(): string
    {
        return match ($this->getStatus()) {
            'successful' => 'bg-green-100 text-green-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'failed' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Check if this retention attempt is recent.
     */
    public function isRecent(): bool
    {
        return $this->created_at->diffInHours(now()) <= 24;
    }

    /**
     * Get the most common cancellation reasons.
     */
    public static function getMostCommonReasons(int $limit = 5): array
    {
        return self::selectRaw('reason, COUNT(*) as count')
            ->whereNotNull('reason')
            ->groupBy('reason')
            ->orderBy('count', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'reason' => $item->reason,
                    'display_name' => (new self(['reason' => $item->reason]))->getReasonDisplayName(),
                    'count' => $item->count,
                ];
            })
            ->toArray();
    }

    /**
     * Get success rate by offer type.
     */
    public static function getSuccessRateByOfferType(): array
    {
        return self::selectRaw('offer_type, COUNT(*) as total, SUM(CASE WHEN success = 1 THEN 1 ELSE 0 END) as successful')
            ->whereNotNull('offer_type')
            ->groupBy('offer_type')
            ->get()
            ->map(function ($item) {
                return [
                    'offer_type' => $item->offer_type,
                    'total' => $item->total,
                    'successful' => $item->successful,
                    'success_rate' => $item->total > 0 ? ($item->successful / $item->total) * 100 : 0,
                ];
            })
            ->toArray();
    }
}