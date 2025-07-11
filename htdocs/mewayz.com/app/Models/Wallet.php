<?php

namespace App\Models;

use App\Models\Base\Wallet as BaseWallet;

class Wallet extends BaseWallet
{
	protected $fillable = [
		'holder_type',
		'holder_id',
		'name',
		'slug',
		'uuid',
		'description',
		'meta',
		'balance',
		'decimal_places'
	];
}
