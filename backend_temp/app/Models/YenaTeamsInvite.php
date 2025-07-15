<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\Base\YenaTeamsInvite as BaseYenaTeamsInvite;

class YenaTeamsInvite extends BaseYenaTeamsInvite
{
	protected $hidden = [
		'accept_token',
		'deny_token'
	];

	protected $fillable = [
		// 'user_id',
		'team_id',
		'email',
		'accept_token',
		'deny_token',
		'settings'
	];

	protected $casts = [
		'settings' => 'array'
	];

	protected $appends = [
		'date',
	];

    protected function date(): Attribute
    {
		$data = \Carbon\Carbon::parse($this->updated_at)->format('M d, Y H:m A');

        return new Attribute(
            get: fn () => $data,
        );
    }
	
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = $model->uuid ? $model->uuid : (string) str()->uuid();
        });
    }
}
