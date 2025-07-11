<?php

namespace App\Models;

use App\Models\Base\OrganizationCategory as BaseOrganizationCategory;

class OrganizationCategory extends BaseOrganizationCategory
{
	protected $fillable = [
		'_org',
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
