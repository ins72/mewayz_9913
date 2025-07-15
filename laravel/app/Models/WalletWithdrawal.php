<?php

namespace App\Models;

use App\Models\Base\WalletWithdrawal as BaseWalletWithdrawal;

class WalletWithdrawal extends BaseWalletWithdrawal
{
	protected $fillable = [
		'user_id',
		'amount',
		'note',
		'is_paid',
		'transaction',
		'extra'
	];

	protected $casts = [
		'transaction' => 'array'
	];
}
