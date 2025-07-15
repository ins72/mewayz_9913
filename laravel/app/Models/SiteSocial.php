<?php

namespace App\Models;

use App\Models\Base\SiteSocial as BaseSiteSocial;

class SiteSocial extends BaseSiteSocial
{
	protected $fillable = [
		'uuid',
		// 'site_id',
		'social',
		'link',
		'position',
		'settings'
	];
	
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = $model->uuid ? $model->uuid : (string) str()->uuid();
        });
    }
}
