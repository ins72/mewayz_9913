<?php

namespace App\Models;

use App\Models\Base\ProductOrderNote as BaseProductOrderNote;

class ProductOrderNote extends BaseProductOrderNote
{
	protected $fillable = [
		'user',
		'tid',
		'note'
	];
}
