<?php

namespace App\Models;

use App\Models\Base\MediakitSectionItem as BaseMediakitSectionItem;

class MediakitSectionItem extends BaseMediakitSectionItem
{
	protected $fillable = [
		'uuid',
		'section_id',
		'image',
		'content',
		'settings',
		'position'
	];

	protected $casts = [
		'image' => 'array',
		'content' => 'array',
		'settings' => 'array',
	];
	
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = $model->uuid ? $model->uuid : (string) str()->uuid();
        });
    }
}
