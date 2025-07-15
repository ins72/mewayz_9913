<?php

namespace App\Models;

use App\Models\Base\SiteAccess as BaseSiteAccess;

class SiteAccess extends BaseSiteAccess
{
	protected $fillable = [
		'uuid',
		'team_id',
		'user_id',
		'site_id',
		'permission'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id', 'id');
	}

    protected static function boot(){
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = $model->uuid ? $model->uuid : (string) str()->uuid();
        });
    }
}
