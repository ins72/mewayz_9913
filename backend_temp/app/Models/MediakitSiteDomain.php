<?php

namespace App\Models;

use App\Models\Base\MediakitSiteDomain as BaseMediakitSiteDomain;

class MediakitSiteDomain extends BaseMediakitSiteDomain
{
	protected $fillable = [
		'site_id',
		'is_active',
		'is_connected',
		'scheme',
		'host',
		'settings'
	];
}
