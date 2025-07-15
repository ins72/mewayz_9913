<?php

namespace App\Models;

use App\Models\Base\OrganizationCategoryDraft as BaseOrganizationCategoryDraft;

class OrganizationCategoryDraft extends BaseOrganizationCategoryDraft
{
	protected $fillable = [
		'_org',
		'published',
		'title',
		'slug',
		'content'
	];
}
