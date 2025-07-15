<?php

namespace App\Models;

use App\Models\Base\SiteAiChatHistory as BaseSiteAiChatHistory;

class SiteAiChatHistory extends BaseSiteAiChatHistory
{
	protected $fillable = [
		'uuid',
		// 'site_id',
		'session_id',
		'role',
		'human',
		'ai',
		'response',
		'extra'
	];

	protected $casts = [
		'response' => 'array'
	];

    protected static function boot(){
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = $model->uuid ? $model->uuid : (string) str()->uuid();
        });
    }
}
