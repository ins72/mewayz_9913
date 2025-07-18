<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Affiliate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'affiliate_code',
        'commission_rate',
        'status',
        'total_referrals',
        'pending_referrals',
        'converted_referrals',
        'total_commissions',
        'pending_commissions',
        'paid_commissions'
    ];

    protected $casts = [
        'commission_rate' => 'decimal:4',
        'total_commissions' => 'decimal:2',
        'pending_commissions' => 'decimal:2',
        'paid_commissions' => 'decimal:2'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->affiliate_code) {
                $model->affiliate_code = Str::upper(Str::random(8));
            }
        });
    }

    /**
     * Get the user that owns the affiliate
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the referrals for the affiliate
     */
    public function referrals()
    {
        return $this->hasMany(AffiliateReferral::class);
    }

    /**
     * Get the commissions for the affiliate
     */
    public function commissions()
    {
        return $this->hasMany(AffiliateCommission::class);
    }

    /**
     * Get active referrals
     */
    public function activeReferrals()
    {
        return $this->referrals()->where('status', 'active');
    }

    /**
     * Get converted referrals
     */
    public function convertedReferrals()
    {
        return $this->referrals()->where('status', 'converted');
    }

    /**
     * Get pending commissions
     */
    public function pendingCommissions()
    {
        return $this->commissions()->where('status', 'pending');
    }

    /**
     * Get paid commissions
     */
    public function paidCommissions()
    {
        return $this->commissions()->where('status', 'paid');
    }

    /**
     * Calculate conversion rate
     */
    public function getConversionRateAttribute()
    {
        return $this->total_referrals > 0 
            ? ($this->converted_referrals / $this->total_referrals) * 100 
            : 0;
    }
}