<?php

namespace App\Models;

use App\Models\Base\CoursesPerformanceTakeDb as BaseCoursesPerformanceTakeDb;

class CoursesPerformanceTakeDb extends BaseCoursesPerformanceTakeDb
{
	protected $fillable = [
		'page_id',
		'user_id',
		'course_id',
		'exam_id',
		'question_id',
		'selected_answer',
		'selected_answer_name',
		'is_passed',
		'settings'
	];
}
