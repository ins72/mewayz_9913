<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\Base\YenaTeamsUserTable as BaseYenaTeamsUserTable;

class YenaTeamsUserTable extends BaseYenaTeamsUserTable
{
	protected $fillable = [
		'user_id',
		'team_id',
		'can_update',
		'can_delete',
		'can_create',
		'settings',
		'is_accepted'
	];
	
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = $model->uuid ? $model->uuid : (string) str()->uuid();
        });
    }
	protected $appends = [
		'created_at_json',
	];

    protected function createdAtJson(): Attribute
    {
		$data = \Carbon\Carbon::parse($this->created_at)->format('M d, Y H:m A');

        return new Attribute(
            get: fn () => $data,
        );
    }
	

    public function team()
    {
        return $this->belongsTo(YenaTeam::class, 'team_id', 'id'); // links this->id to events.course_id
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id'); // links this->id to events.course_id
    }
}
