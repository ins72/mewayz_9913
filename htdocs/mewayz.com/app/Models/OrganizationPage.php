<?php

namespace App\Models;

use App\Draftable\traits\DraftableModel;
use App\Models\Base\OrganizationPage as BaseOrganizationPage;

class OrganizationPage extends BaseOrganizationPage
{
    use DraftableModel;
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
