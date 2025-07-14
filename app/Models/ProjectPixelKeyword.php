<?php

namespace App\Models;

use App\Models\Base\ProjectPixelKeyword as BaseProjectPixelKeyword;

class ProjectPixelKeyword extends BaseProjectPixelKeyword
{
	protected $fillable = [
		'project_id',
		'feedback_id',
		'keyword',
		'settings',
		'status'
	];
}
