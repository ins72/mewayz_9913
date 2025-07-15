<?php

namespace App\Models;

use App\Models\Base\YenaFavorite as BaseYenaFavorite;

class YenaFavorite extends BaseYenaFavorite
{
	protected $fillable = [
		// 'uuid',
		// 'owner_id',
		// 'user_id',
		// 'site_id'
	];
	
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = $model->uuid ? $model->uuid : (string) str()->uuid();
        });
    }
}
