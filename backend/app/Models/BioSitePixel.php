<?php

namespace App\Models;

use App\Models\Base\BioSitePixel as BaseBioSitePixel;

class BioSitePixel extends BaseBioSitePixel
{
	protected $fillable = [
		'uuid',
		'site_id',
		'name',
		'status',
		'pixel_id',
		'pixel_type'
	];
}
