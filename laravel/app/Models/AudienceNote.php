<?php

namespace App\Models;

use App\Models\Base\AudienceNote as BaseAudienceNote;

class AudienceNote extends BaseAudienceNote
{
	protected $fillable = [
		'audience_id',
		'note',
		'extra'
	];

	protected $casts = [
		'extra' => 'array'
	];
}
