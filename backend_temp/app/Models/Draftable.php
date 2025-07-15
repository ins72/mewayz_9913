<?php

namespace App\Models;

use App\Models\Base\Draftable as BaseDraftable;

class Draftable extends BaseDraftable
{
	protected $fillable = [
		'draftable_data',
		'draftable_model',
		'draftable_id',
		'user_id',
		'data',
		'published_at'
	];
}
