<?php

namespace App\Models;

use App\Models\Base\MediakitSiteSocial as BaseMediakitSiteSocial;

class MediakitSiteSocial extends BaseMediakitSiteSocial
{
	protected $fillable = [
		'uuid',
		'site_id',
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
