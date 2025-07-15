<?php

namespace App\Models;

use App\Models\Base\SiteForm as BaseSiteForm;

class SiteForm extends BaseSiteForm
{
	protected $fillable = [
		'email',
		'content',
		'settings'
	];

	protected $casts = [
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
