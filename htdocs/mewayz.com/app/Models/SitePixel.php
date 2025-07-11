<?php

namespace App\Models;

use App\Models\Base\SitePixel as BaseSitePixel;

class SitePixel extends BaseSitePixel
{
	protected $fillable = [
		// 'site_id',
		'name',
		'status',
		'pixel_id',
		'pixel_type'
	];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = $model->uuid ? $model->uuid : (string) str()->uuid();
        });
    }
}
