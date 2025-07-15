<?php

namespace App\Models;

use App\Models\Base\BioAddonsDb as BaseBioAddonsDb;

class BioAddonsDb extends BaseBioAddonsDb
{
	protected $fillable = [
		'uuid',
		'site_id',
		'addon',
		'email',
		'database'
	];
}
