<?php

namespace App\Models;

use App\Models\Base\OrganizationPagesDraft as BaseOrganizationPagesDraft;

class OrganizationPagesDraft extends BaseOrganizationPagesDraft
{
	protected $fillable = [
		'_org',
		'_category',
		'published',
		'title',
		'slug',
		'content',
		'uuid',
		'published_at',
		'is_published',
		'is_current',
		'publisher_type',
		'publisher_id'
	];
}
