<?php

namespace App\Models;

use App\Models\Base\CoursesExam as BaseCoursesExam;

class CoursesExam extends BaseCoursesExam
{
	protected $fillable = [
		'user_id',
		'course_id',
		'name',
		'description',
		'settings'
	];
}
