<?php

namespace App\Models;

use App\Models\Base\ProjectSummary as BaseProjectSummary;

class ProjectSummary extends BaseProjectSummary
{
	protected $fillable = [
		'project_id',
		'response',
		'settings'
	];
}
