<?php

namespace App\Models;

use App\Models\Base\AudienceFoldersUser as BaseAudienceFoldersUser;

class AudienceFoldersUser extends BaseAudienceFoldersUser
{
	protected $fillable = [
		'folder_id',
		'audience_id',
		'settings'
	];
}
