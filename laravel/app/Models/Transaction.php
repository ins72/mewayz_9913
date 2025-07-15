<?php

namespace App\Models;

use App\Models\Base\Transaction as BaseTransaction;

class Transaction extends BaseTransaction
{
	protected $fillable = [
		'payable_type',
		'payable_id',
		'wallet_id',
		'type',
		'amount',
		'confirmed',
		'meta',
		'uuid'
	];
}
