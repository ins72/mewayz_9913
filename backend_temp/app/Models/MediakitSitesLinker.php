<?php

namespace App\Models;

use App\Models\Base\MediakitSitesLinker as BaseMediakitSitesLinker;

class MediakitSitesLinker extends BaseMediakitSitesLinker
{
	protected $fillable = [
		'site_id',
		'url',
		'slug'
	];
}
