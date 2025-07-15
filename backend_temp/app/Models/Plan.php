<?php

namespace App\Models;

use App\Models\Base\Plan as BasePlan;

class Plan extends BasePlan
{
	protected $fillable = [];

    protected $casts = [
        'metadata' => 'object',
    ];
	
    public function features()
    {
        return $this->hasMany(config('plans.models.feature'), 'plan_id');
    }

    public function getSubscriptionCount(){
        $subscriptionModel = config('plans.models.subscription');
        $count = 0;
        $subs = $subscriptionModel::where('plan_id', $this->id)->get();

        foreach ($subs as $item) {
            if($item->isActive()) $count+=1;
        }
        return $count;
    }

    public function _can_take_trial(){
        if(!$this->has_trial) return false;

        if(PlansHistory::where('user_id', \Auth::user()->id)->where('plan_id', $this->id)->where('trial', 1)->first()) return false;
        
        return true;
    }
	
    public function subscribers()
    {
        return $this->hasMany(config('plans.models.subscription'), 'plan_id');
    }
}
