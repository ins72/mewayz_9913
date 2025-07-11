<?php

namespace App\Models;

use App\Models\Base\CheckoutGo as BaseCheckoutGo;

class CheckoutGo extends BaseCheckoutGo
{
	protected $fillable = [
		'uref',
		'email',
		'currency',
		'price',
		'paid',
		'method',
		'callback',
		'call_function',
		'payment_type',
		'frequency',
		'keys',
		'meta'
	];

	protected $casts = [
		'keys' => 'array',
		'meta' => 'array'
	];

	public function setPaid(){

		$this->paid = 1;
		$this->save();
	}
}