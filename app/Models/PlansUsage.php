<?php

namespace App\Models;

use App\Models\Base\PlansUsage as BasePlansUsage;

class PlansUsage extends BasePlansUsage
{
	protected $fillable = [
		'subscription_id',
		'code',
		'used'
	];
	
    public function subscription()
    {
        return $this->belongsTo(config('plans.models.subscription'), 'subscription_id');
    }

    public function scopeCode($query, string $code)
    {
        return $query->where('code', $code);
    }
}
