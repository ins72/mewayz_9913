<?php

namespace App\Models;

use App\Models\Base\OrganizationSpace as BaseOrganizationSpace;

class OrganizationSpace extends BaseOrganizationSpace
{
	protected $fillable = [
		'_org',
		'default',
		'name',
		'slug'
	];
}
