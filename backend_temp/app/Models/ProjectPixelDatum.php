<?php

namespace App\Models;

use App\Models\Base\ProjectPixelDatum as BaseProjectPixelDatum;

class ProjectPixelDatum extends BaseProjectPixelDatum
{
	protected $fillable = [
		'project_id',
		'email',
		'feedback',
		'reaction',
		'_tracking',
		'_tags',
		'settings',
		'status'
	];

	protected $casts = [
		'_tracking' => 'array',
		'_tags' => 'array',
		'settings' => 'array',
	];
}
