<?php

namespace App\Models;

use App\Models\Base\SiteFooterGroup as BaseSiteFooterGroup;

class SiteFooterGroup extends BaseSiteFooterGroup
{
	protected $fillable = [
		// 'uuid',
		// 'site_id',
		'title',
		'links',
		'position',
		'settings'
	];

	protected $casts = [
		'settings' => 'array',
		'links' => 'array',
	];

    protected static function boot(){
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = $model->uuid ? $model->uuid : (string) str()->uuid();
        });
    }
}
