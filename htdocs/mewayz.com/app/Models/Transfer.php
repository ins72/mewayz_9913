<?php

namespace App\Models;

use App\Models\Base\Transfer as BaseTransfer;

class Transfer extends BaseTransfer
{
	protected $fillable = [
		'from_type',
		'from_id',
		'to_type',
		'to_id',
		'status',
		'status_last',
		'deposit_id',
		'withdraw_id',
		'discount',
		'fee',
		'uuid'
	];
}
