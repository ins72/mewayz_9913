<?php

namespace App\Models;

use App\Models\Base\MediakitSitesVisitor as BaseMediakitSitesVisitor;

class MediakitSitesVisitor extends BaseMediakitSitesVisitor
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
