<?php

namespace App\Models;

use App\Models\Base\PlansHistory as BasePlansHistory;

class PlansHistory extends BasePlansHistory
{
	protected $fillable = [
		'plan_id',
		'user_id'
	];
}
