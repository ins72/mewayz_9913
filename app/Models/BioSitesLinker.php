<?php

namespace App\Models;

use App\Models\Base\BioSitesLinker as BaseBioSitesLinker;

class BioSitesLinker extends BaseBioSitesLinker
{
	protected $fillable = [
		'site_id',
		'url',
		'slug'
	];
}
