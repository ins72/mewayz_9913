<?php

namespace App\Models;

use App\Models\Base\CoursesOrder as BaseCoursesOrder;

class CoursesOrder extends BaseCoursesOrder
{
	protected $fillable = [
		'user',
		'payee_user_id',
		'course_id',
		'details',
		'currency',
		'email',
		'ref',
		'price',
		'extra',
		'status'
	];
}
