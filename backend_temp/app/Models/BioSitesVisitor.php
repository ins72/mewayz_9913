<?php

namespace App\Models;

use App\Models\Base\BioSitesVisitor as BaseBioSitesVisitor;

class BioSitesVisitor extends BaseBioSitesVisitor
{
	protected $fillable = [
		'site_id',
		'slug',
		'session',
		'ip',
		'tracking',
		'page_slug',
		'views'
	];
	protected $casts = [
		'tracking' => 'array'
	];
}
