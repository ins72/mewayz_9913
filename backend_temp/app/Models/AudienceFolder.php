<?php

namespace App\Models;

use App\Models\Base\AudienceFolder as BaseAudienceFolder;

class AudienceFolder extends BaseAudienceFolder
{
	protected $fillable = [
		'user_id',
		'name',
		'thumbnail',
		'settings'
	];


	/**
	 * Get all of the comments for the AudienceFolder
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function users()
	{
		return $this->hasMany(AudienceFoldersUser::class, 'folder_id', 'id');
	}
}
