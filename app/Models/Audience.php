<?php

namespace App\Models;

use App\Models\Base\Audience as BaseAudience;
use App\Traits\AudienceTraitTwo;

class Audience extends BaseAudience
{
	use AudienceTraitTwo;

	protected $fillable = [
		'user_id',
		'workspace_id',
		'page_id',
		'name',
		'email',
		'phone',
		'company',
		'position',
		'type',
		'status',
		'source',
		'notes',
		'tags',
		'contact',
		'extra'
	];

	protected $casts = [
		'tags' => 'array',
		'contact' => 'array',
		'extra' => 'array',
	];

	public function getAvatar(){
        $avatar = $this->avatar;
        $default = _randAvatar(ao($this->contact, 'name'));
        $check = mediaExists('media/audience/avatar', $avatar);
        $path = getStorage('media/audience/avatar', $avatar);

        $avatar = (!empty($avatar) && $check) ? $path : $default;

        if(validate_url($this->avatar)) $avatar = $this->avatar;

        return $avatar;
	}

	/**
	 * Get the user that owns the Audience
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function user()
	{
		return $this->belongsTo(User::class, 'user_id', 'id');
	}

	public function _set(){
		return $this->set($this);
	}
}
