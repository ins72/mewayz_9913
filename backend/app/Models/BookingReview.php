<?php

namespace App\Models;

use App\Models\Base\BookingReview as BaseBookingReview;

class BookingReview extends BaseBookingReview
{
	protected $fillable = [
		'user',
		'reviewer_id',
		'rating',
		'review'
	];
}
