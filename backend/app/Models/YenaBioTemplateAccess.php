<?php

namespace App\Models;

use App\Models\Base\YenaBioTemplateAccess as BaseYenaBioTemplateAccess;

class YenaBioTemplateAccess extends BaseYenaBioTemplateAccess
{
	protected $fillable = [
		'uuid',
		'template_id',
		'user_id',
		'site_id',
		'extra'
	];

	protected $casts = [
		'extra' => 'array',
	];
	
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = $model->uuid ? $model->uuid : (string) str()->uuid();
        });
    }
}
