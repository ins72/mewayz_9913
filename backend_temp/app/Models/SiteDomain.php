<?php

namespace App\Models;

use App\Models\Base\SiteDomain as BaseSiteDomain;

class SiteDomain extends BaseSiteDomain
{
	protected $fillable = [
		'site_id',
		'is_active',
		'scheme',
		'host',
		'settings'
	];

	protected $casts = [
		'settings' => 'array'
	];
}
