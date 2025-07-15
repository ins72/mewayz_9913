<?php

namespace App\Models;

use App\Models\Base\Unsplashable as BaseUnsplashable;

class Unsplashable extends BaseUnsplashable
{
	protected $fillable = [
		'unsplash_asset_id',
		'unsplashables_id',
		'unsplashables_type'
	];
}
