<?php

namespace App\Models;

use App\Models\Base\SitePost as BaseSitePost;

class SitePost extends BaseSitePost
{
	protected $fillable = [
		'uuid',
		'site_id',
		'name',
		'slug',
		'published',
		'seo',
		'content',
		'description',
		'settings',
		'section_settings'
	];

	protected $casts = [
		'content' => 'array',
		'seo' => 'array',
		'settings' => 'array',
		'section_settings' => 'array',
	];
	
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = $model->uuid ? $model->uuid : (string) str()->uuid();
        });
    }
}
