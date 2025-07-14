<?php

namespace App\Models;

use App\Models\Base\BookingService as BaseBookingService;

class BookingService extends BaseBookingService
{
	protected $fillable = [
		'user',
		'name',
		'thumbnail',
		'price',
		'duration',
		'settings',
		'status',
		'position'
	];

	protected $casts = [
		'settings' => 'array',
        'booking_workhours' => 'array',
		'gallery' => 'array',
	];

	public function user(){
		return $this->belongsTo(User::class, 'user_id', 'id');
	}

	public function getPrice(){
		$price = $this->user()->first()->price($this->price);

		return $price;
	}
}
