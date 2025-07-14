<?php

namespace App\Models;

use App\Models\Base\WalletSettlement as BaseWalletSettlement;

class WalletSettlement extends BaseWalletSettlement
{
	protected $fillable = [
		'user',
		'settlement_id',
		'settlement'
	];
}
