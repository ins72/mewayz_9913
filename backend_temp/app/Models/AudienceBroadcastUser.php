<?php

namespace App\Models;

use App\Models\Base\AudienceBroadcastUser as BaseAudienceBroadcastUser;

class AudienceBroadcastUser extends BaseAudienceBroadcastUser
{
	protected $fillable = [
		'broadcast_id',
		'audience_id',
		'settings'
	];

	protected $casts = [
		'settings' => 'array'
	];
}
