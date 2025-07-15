<?php

namespace App\Models;

use App\Models\Base\SiteAiChatSession as BaseSiteAiChatSession;

class SiteAiChatSession extends BaseSiteAiChatSession
{
	protected $fillable = [
		'uuid',
		'site_id',
		'started_by',
		'session',
		'extra'
	];
	
	public function history(){
		return $this->hasMany(SiteAiChatHistory::class, 'session_id', 'uuid');
	}
	
    protected static function boot(){
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = $model->uuid ? $model->uuid : (string) str()->uuid();
        });
    }
}
