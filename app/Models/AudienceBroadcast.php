<?php

namespace App\Models;

use App\Models\Base\AudienceBroadcast as BaseAudienceBroadcast;

class AudienceBroadcast extends BaseAudienceBroadcast
{
	protected $fillable = [
		'user_id',
		'subject',
		'name',
		'email',
		'content',
		'folder_id',
		'schedule',
		'schedule_on',
		'thumbnail',
		'settings'
	];

	protected $casts = [
		'settings' => 'array',
		'content' => 'array',
	];


	/**
	 * Get all of the comments for the AudienceFolder
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function users()
	{
		return $this->hasMany(AudienceBroadcastUser::class, 'broadcast_id', 'id');
	}

	public function sent(){
		return $this->hasMany(AudienceBroadcastSent::class, 'broadcast_id', 'id');
	}

	// /**
	//  * Get all of the comments for the AudienceFolder
	//  *
	//  * @return \Illuminate\Database\Eloquent\Relations\HasMany
	//  */
	// public function audience()
	// {
	// 	return $this->hasMany(Audience::class, 'broadcast_id', 'id');
	// }
}
