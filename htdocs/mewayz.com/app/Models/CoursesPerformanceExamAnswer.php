<?php

namespace App\Models;

use App\Models\Base\CoursesPerformanceExamAnswer as BaseCoursesPerformanceExamAnswer;

class CoursesPerformanceExamAnswer extends BaseCoursesPerformanceExamAnswer
{
	protected $fillable = [
		'user_id',
		'exam_id',
		'question_id',
		'name',
		'is_correct',
		'settings'
	];
}
