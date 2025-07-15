<?php

namespace App\Models;

use App\Models\Base\ProductOption as BaseProductOption;

class ProductOption extends BaseProductOption
{
	protected $fillable = [
		'user',
		'product_id',
		'name',
		'price',
		'stock',
		'description',
		'files',
		'position'
	];
}
