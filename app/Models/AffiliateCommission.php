<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliateCommission extends Model
{
    use HasFactory;

    protected $fillable = [
        'affiliate_id',
        'user_id',
        'amount',
        'transaction_amount',
        'transaction_type',
        'commission_rate',
        'status',
        'due_date',
        'paid_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_amount' => 'decimal:2',
        'commission_rate' => 'decimal:4',
        'due_date' => 'datetime',
        'paid_at' => 'datetime'
    ];

    /**
     * Get the affiliate that owns the commission
     */
    public function affiliate()
    {
        return $this->belongsTo(Affiliate::class);
    }

    /**
     * Get the user that generated the commission
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for pending commissions
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for paid commissions
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Mark commission as paid
     */
    public function markAsPaid()
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now()
        ]);
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute()
    {
        return '$' . number_format($this->amount, 2);
    }
}