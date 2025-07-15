<?php

namespace App\Models;

use App\Models\Base\ProductOrderTimeline as BaseProductOrderTimeline;

class ProductOrderTimeline extends BaseProductOrderTimeline
{
	protected $fillable = [
		'user',
		'tid',
		'type',
		'data'
	];

	protected $casts = [
		'data' => 'array'
	];
}
