<?php

namespace App\Models;

use App\Models\Base\SitesLinker as BaseSitesLinker;

class SitesLinker extends BaseSitesLinker
{
	protected $fillable = [
		'sites_id',
		'url',
		'slug'
	];
}
