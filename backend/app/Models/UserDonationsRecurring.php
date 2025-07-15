<?php

namespace App\Models;

use App\Models\Base\UserDonationsRecurring as BaseUserDonationsRecurring;

class UserDonationsRecurring extends BaseUserDonationsRecurring
{
    protected $fillable = [
        'user_id',
        'is_active',
        'last_subscription_uref'
    ];

    public function donations()
    {
        return $this->hasMany(UserDonation::class, 'recurring_id', 'id');
    }

    public function getLastDonation()
    {
        return $this->donations()->orderBy('created_at', 'desc')->first();
    }
}
