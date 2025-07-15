<?php

namespace App\Models;

use App\Models\Base\SitesStaticThumbnail as BaseSitesStaticThumbnail;

class SitesStaticThumbnail extends BaseSitesStaticThumbnail
{
	protected $fillable = [
		'uuid',
		'site_id',
		'thumbnail'
	];
	
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = $model->uuid ? $model->uuid : (string) str()->uuid();
        });
    }
}
