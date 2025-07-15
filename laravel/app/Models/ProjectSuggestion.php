<?php

namespace App\Models;

use App\Models\Base\ProjectSuggestion as BaseProjectSuggestion;

class ProjectSuggestion extends BaseProjectSuggestion
{
	protected $fillable = [
		'project_id',
		'response',
		'settings'
	];
}
