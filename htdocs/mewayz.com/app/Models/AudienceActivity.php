<?php

namespace App\Models;

use App\Models\Base\AudienceActivity as BaseAudienceActivity;

class AudienceActivity extends BaseAudienceActivity
{
	protected $fillable = [
		'user',
		'audience_id',
		'type',
		'message',
		'ip',
		'os',
		'browser'
	];
}
