<?php

namespace App\Models;

use App\Models\Base\ProductOrder as BaseProductOrder;

class ProductOrder extends BaseProductOrder
{
	protected $fillable = [
		'user_id',
		'payee_user_id',
		'details',
		'currency',
		'email',
		'ref',
		'price',
		'is_deal',
		'deal_discount',
		'products',
		'extra',
		'status'
	];

	protected $casts = [
		'details' => 'array',
		'products' => 'array',
		'extra' => 'array'
	];

	public function payee(){
		return $this->belongsTo(User::class, 'payee_user_id', 'id');
	}
}
