<?php

namespace App\Models;

use App\Models\Base\YenaTeam as BaseYenaTeam;
use Illuminate\Database\Eloquent\Casts\Attribute;

class YenaTeam extends BaseYenaTeam
{
	protected $fillable = [
		// 'owner_id',
		'name'
	];
	protected $appends = [
		'logo_json',
	];

    protected function logoJson(): Attribute
    {
		$data = $this->getLogo();

        return new Attribute(
            get: fn () => $data,
        );
    }

	public function getLogo(){
        $logo = "https://api.dicebear.com/8.x/initials/svg?seed=$this->name";

		if(!empty($this->logo) && mediaExists('media/team/logo', $this->logo)){
			$logo = gs('media/team/logo', $this->logo);
		}

		return $logo;
	}
	
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = $model->uuid ? $model->uuid : (string) str()->uuid();
        });
    }

	/**
	 * Get the user that owns the YenaTeam
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function user()
	{
		return $this->belongsTo(User::class, 'owner_id', 'id');
	}
}
