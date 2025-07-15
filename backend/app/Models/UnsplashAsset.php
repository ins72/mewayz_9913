<?php

namespace App\Models;

use App\Models\Base\UnsplashAsset as BaseUnsplashAsset;

class UnsplashAsset extends BaseUnsplashAsset
{
	protected $fillable = [
		'unsplash_id',
		'name',
		'author',
		'author_link'
	];
}
