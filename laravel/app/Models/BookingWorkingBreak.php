<?php

namespace App\Models;

use App\Models\Base\BookingWorkingBreak as BaseBookingWorkingBreak;

class BookingWorkingBreak extends BaseBookingWorkingBreak
{
	protected $fillable = [
		'user',
		'date',
		'time',
		'settings'
	];
}
