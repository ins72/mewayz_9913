<?php

namespace App\Models;

use App\Models\Base\BioSiteDomain as BaseBioSiteDomain;

class BioSiteDomain extends BaseBioSiteDomain
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
