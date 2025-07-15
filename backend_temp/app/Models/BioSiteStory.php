<?php

namespace App\Models;

use App\Models\Base\BioSiteStory as BaseBioSiteStory;

class BioSiteStory extends BaseBioSiteStory
{
	protected $fillable = [
		'uuid',
		'site_id',
		'thumbnail',
		'name',
		'link',
		'settings',
		'position'
	];
}
