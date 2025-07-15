<?php

namespace App\Models;

use App\Models\Base\ProductCouponHistory as BaseProductCouponHistory;

class ProductCouponHistory extends BaseProductCouponHistory
{
	protected $fillable = [
		'user_id',
		'coupon_id',
		'settings'
	];
}
