<?php

namespace App\Models;

use App\Models\Base\ProductShipping as BaseProductShipping;

class ProductShipping extends BaseProductShipping
{
	protected $fillable = [
		'user',
		'country_iso',
		'country',
		'locations',
		'extra'
	];
}
