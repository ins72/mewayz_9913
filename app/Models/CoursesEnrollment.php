<?php

namespace App\Models;

use App\Models\Base\CoursesEnrollment as BaseCoursesEnrollment;

class CoursesEnrollment extends BaseCoursesEnrollment
{
	protected $fillable = [
		'user',
		'payee_user_id',
		'course_id',
		'lesson_taken'
	];
}
