<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspace_id',
        'transaction_id',
        'transaction_type',
        'fee_type',
        'transaction_amount',
        'fee_percentage',
        'fee_amount',
        'net_amount',
        'subscription_plan',
        'fee_breakdown',
        'metadata'
    ];

    protected $casts = [
        'transaction_amount' => 'decimal:2',
        'fee_percentage' => 'decimal:2',
        'fee_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'fee_breakdown' => 'array',
        'metadata' => 'array'
    ];

    /**
     * Get the workspace this fee belongs to
     */
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    /**
     * Calculate fee amount based on transaction amount and percentage
     */
    public static function calculateFee(float $transactionAmount, float $feePercentage): float
    {
        return round($transactionAmount * ($feePercentage / 100), 2);
    }

    /**
     * Create a transaction fee record
     */
    public static function createFee(array $data): self
    {
        $feeAmount = self::calculateFee($data['transaction_amount'], $data['fee_percentage']);
        $netAmount = $data['transaction_amount'] - $feeAmount;

        return self::create([
            'workspace_id' => $data['workspace_id'],
            'transaction_id' => $data['transaction_id'],
            'transaction_type' => $data['transaction_type'],
            'fee_type' => $data['fee_type'] ?? 'platform_fee',
            'transaction_amount' => $data['transaction_amount'],
            'fee_percentage' => $data['fee_percentage'],
            'fee_amount' => $feeAmount,
            'net_amount' => $netAmount,
            'subscription_plan' => $data['subscription_plan'],
            'fee_breakdown' => $data['fee_breakdown'] ?? [],
            'metadata' => $data['metadata'] ?? []
        ]);
    }

    /**
     * Get total fees for a workspace
     */
    public static function getTotalFeesForWorkspace(string $workspaceId, string $period = 'month'): float
    {
        $query = self::where('workspace_id', $workspaceId);

        if ($period === 'month') {
            $query->whereMonth('created_at', now()->month)
                  ->whereYear('created_at', now()->year);
        } elseif ($period === 'year') {
            $query->whereYear('created_at', now()->year);
        }

        return $query->sum('fee_amount');
    }

    /**
     * Get fees breakdown by transaction type
     */
    public static function getFeesBreakdown(string $workspaceId): array
    {
        return self::where('workspace_id', $workspaceId)
            ->selectRaw('transaction_type, SUM(fee_amount) as total_fees, COUNT(*) as transaction_count')
            ->groupBy('transaction_type')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->transaction_type => [
                    'total_fees' => $item->total_fees,
                    'transaction_count' => $item->transaction_count
                ]];
            })
            ->toArray();
    }

    /**
     * Scope for transaction type
     */
    public function scopeForTransactionType($query, string $transactionType)
    {
        return $query->where('transaction_type', $transactionType);
    }

    /**
     * Scope for date range
     */
    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}