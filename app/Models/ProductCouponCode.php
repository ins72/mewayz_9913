<?php

namespace App\Models;

use App\Models\Base\ProductCouponCode as BaseProductCouponCode;

class ProductCouponCode extends BaseProductCouponCode
{
	protected $fillable = [
		'user_id',
		'product_id',
		'code',
		'type',
		'start_date',
		'end_date',
		'discount',
		'settings'
	];

	protected $casts = [
        // 'end_date' => 'date:Y/m/d'
    ];
}
