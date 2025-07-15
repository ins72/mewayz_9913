<?php

namespace App\Models;

use App\Models\Base\CoursesIntro as BaseCoursesIntro;

class CoursesIntro extends BaseCoursesIntro
{
	protected $fillable = [
		'user_id',
		'course_id',
		'name',
		'file',
		'settings'
	];
}
