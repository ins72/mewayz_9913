<?php

namespace App\Models;

use App\Models\Base\CoursesLesson as BaseCoursesLesson;

class CoursesLesson extends BaseCoursesLesson
{
	protected $fillable = [
		// 'user',
		// 'course_id',
		'name',
		'description',
		'lesson_type',
		'status',
		'lesson_duration',
		'lesson_data',
		'settings',
		'position'
	];

	protected $casts = [
		'lesson_data' => 'array',
		'settings'	  => 'array'
	];
}
