<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliateReferral extends Model
{
    use HasFactory;

    protected $fillable = [
        'affiliate_id',
        'referred_user_id',
        'referral_code',
        'campaign',
        'status',
        'ip_address',
        'user_agent',
        'converted_at'
    ];

    protected $casts = [
        'converted_at' => 'datetime'
    ];

    /**
     * Get the affiliate that owns the referral
     */
    public function affiliate()
    {
        return $this->belongsTo(Affiliate::class);
    }

    /**
     * Get the referred user
     */
    public function referred_user()
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }

    /**
     * Scope for converted referrals
     */
    public function scopeConverted($query)
    {
        return $query->where('status', 'converted');
    }

    /**
     * Scope for pending referrals
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Mark referral as converted
     */
    public function markAsConverted()
    {
        $this->update([
            'status' => 'converted',
            'converted_at' => now()
        ]);
    }
}