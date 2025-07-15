<?php

namespace App\Models;

use App\Models\Base\UserDonation as BaseUserDonation;

class UserDonation extends BaseUserDonation
{
	protected $fillable = [
		'user_id',
		'payee_user_id',
		'is_private',
		'amount',
		'currency',
		'email',
		'source',
		'info',
		'is_recurring',
		'recurring_id'
	];
	
	protected $casts = [
		'info' => 'array'
	];

	public function payee(){
		return $this->belongsTo(User::class, 'payee_user_id', 'id');
	}

	public function page(){
		return $this->belongsTo(BioSite::class, 'bio_id', 'id');
	}

	public function getRecurring(){
		// if(!$recurring = UserDonationsRecurring::find($this->recurring_id))

		return UserDonationsRecurring::find($this->recurring_id);
	}
}
