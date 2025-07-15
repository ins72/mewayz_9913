<?php

namespace App\Models;

use App\Models\Base\AudienceBroadcastSent as BaseAudienceBroadcastSent;

class AudienceBroadcastSent extends BaseAudienceBroadcastSent
{
	protected $fillable = [
		'broadcast_id',
		'broadcast_user_id',
		'status',
		'settings'
	];
}
