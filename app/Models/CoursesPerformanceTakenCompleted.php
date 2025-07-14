<?php

namespace App\Models;

use App\Models\Base\CoursesPerformanceTakenCompleted as BaseCoursesPerformanceTakenCompleted;

class CoursesPerformanceTakenCompleted extends BaseCoursesPerformanceTakenCompleted
{
	protected $fillable = [
		'page_id',
		'user_id',
		'exam_id',
		'course_id',
		'is_passed',
		'settings'
	];
}
