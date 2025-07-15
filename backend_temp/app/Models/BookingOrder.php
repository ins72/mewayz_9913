<?php

namespace App\Models;

use App\Models\Base\BookingOrder as BaseBookingOrder;

class BookingOrder extends BaseBookingOrder
{
	protected $fillable = [
		'user',
		'payee_user_id',
		'appointment_id',
		'method',
		'details',
		'currency',
		'email',
		'ref',
		'price',
		'extra',
		'status'
	];
}
