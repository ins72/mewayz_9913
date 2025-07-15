<?php

namespace App\Models;

use App\Models\Base\Session as BaseSession;

class Session extends BaseSession
{
	protected $fillable = [
		'user_id',
		'site_id',
		'ip_address',
		'user_agent',
		'payload',
		'tracking',
		'last_activity'
	];
}
