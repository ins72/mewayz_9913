<?php

namespace App\Models;

use App\Models\Base\YenaEmbedStore as BaseYenaEmbedStore;

class YenaEmbedStore extends BaseYenaEmbedStore
{
	protected $fillable = [
		'link',
		'data'
	];

	protected $casts = [
		'data' => 'array'
	];
}
