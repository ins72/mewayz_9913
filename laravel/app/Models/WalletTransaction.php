<?php

namespace App\Models;

use App\Models\Base\WalletTransaction as BaseWalletTransaction;

class WalletTransaction extends BaseWalletTransaction
{
	protected $fillable = [
		'user',
		'method',
		'spv_id',
		'type',
		'amount',
		'amount_settled',
		'currency',
		'transaction',
		'payload'
	];


	protected $casts = [
		'transaction' => 'array',
		'payload' => 'array'
	];
}
