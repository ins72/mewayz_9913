<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'feature_key',
        'stripe_subscription_item_id',
        'quantity',
        'unit_price',
        'quota_limit',
        'usage_count',
        'metadata',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'quota_limit' => 'integer',
        'usage_count' => 'integer',
        'metadata' => 'array',
    ];

    /**
     * Get the subscription that owns this item.
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Get the feature for this item.
     */
    public function feature(): BelongsTo
    {
        return $this->belongsTo(Feature::class, 'feature_key', 'key');
    }

    /**
     * Get the total price for this item.
     */
    public function getTotalPrice(): float
    {
        return (float) $this->unit_price * $this->quantity;
    }

    /**
     * Check if this item has reached its quota limit.
     */
    public function hasReachedQuotaLimit(): bool
    {
        if (!$this->quota_limit) {
            return false;
        }

        return $this->usage_count >= $this->quota_limit;
    }

    /**
     * Get the remaining quota for this item.
     */
    public function getRemainingQuota(): int
    {
        if (!$this->quota_limit) {
            return PHP_INT_MAX;
        }

        return max(0, $this->quota_limit - $this->usage_count);
    }

    /**
     * Get the usage percentage for this item.
     */
    public function getUsagePercentage(): float
    {
        if (!$this->quota_limit) {
            return 0.0;
        }

        return min(100.0, ($this->usage_count / $this->quota_limit) * 100);
    }

    /**
     * Increment the usage count.
     */
    public function incrementUsage(int $amount = 1): void
    {
        $this->increment('usage_count', $amount);
    }

    /**
     * Reset the usage count.
     */
    public function resetUsage(): void
    {
        $this->update(['usage_count' => 0]);
    }

    /**
     * Check if this item is approaching its quota limit.
     */
    public function isApproachingQuotaLimit(float $threshold = 0.8): bool
    {
        if (!$this->quota_limit) {
            return false;
        }

        return $this->getUsagePercentage() >= ($threshold * 100);
    }
}