<?php

namespace App\Models;

use App\Models\Base\PlansFeature as BasePlansFeature;

class PlansFeature extends BasePlansFeature
{
	protected $fillable = [
		'plan_id',
		'name',
		'code',
		'description',
		'type',
		'limit'
	];
	
	protected $casts = [
        'metadata' => 'object',
    ];

    public function plan()
    {
        return $this->belongsTo(config('plans.models.plan'), 'plan_id');
    }

    public function hasFeature(){

        return $this->plan;
        
        $previews = 5;

        if($subscription = $this->activeSubscription()){
            $feature = $subscription->features()->code('consume.previews')->first();
            $previews = $feature->limit;
        }

        return $previews;
    }

    public function scopeCode($query, string $code)
    {
        return $query->where('code', $code);
    }

    public function scopeLimited($query)
    {
        return $query->where('type', 'limit');
    }

    public function scopeFeature($query)
    {
        return $query->where('type', 'feature');
    }

    public function isUnlimited()
    {
        return (bool) ($this->type == 'limit' && $this->limit < 0);
    }
}
