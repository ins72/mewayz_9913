<?php

namespace App\Models;

use App\Models\Base\OrganizationDomain as BaseOrganizationDomain;

class OrganizationDomain extends BaseOrganizationDomain
{
	protected $fillable = [
		'_org',
		'is_active',
		'scheme',
		'host',
		'settings'
	];
}
