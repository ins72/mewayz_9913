<?php

namespace App\Models;

use App\Models\Base\BioAddon as BaseBioAddon;

class BioAddon extends BaseBioAddon
{
	protected $fillable = [
		'uuid',
		'site_id',
		'slug',
		'name',
		'thumbnail',
		'addon',
		'content',
		'settings',
		'position'
	];
}
