<?php

namespace App\Models;

use App\Models\Base\ProductShippingLocation as BaseProductShippingLocation;

class ProductShippingLocation extends BaseProductShippingLocation
{
	protected $fillable = [
		'user',
		'shipping_id',
		'name',
		'description',
		'price',
		'extra'
	];
}
