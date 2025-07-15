<?php

namespace App\Models;

use App\Models\Base\PlanPayment as BasePlanPayment;

class PlanPayment extends BasePlanPayment
{
	protected $fillable = [
		'user',
		'name',
		'plan',
		'plan_name',
		'duration',
		'email',
		'ref',
		'currency',
		'price',
		'gateway'
	];
}
