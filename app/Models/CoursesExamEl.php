<?php

namespace App\Models;

use App\Models\Base\CoursesExamEl as BaseCoursesExamEl;

class CoursesExamEl extends BaseCoursesExamEl
{
	protected $fillable = [
		'user_id',
		'exam_id',
		'name',
		'is_correct',
		'settings'
	];
}
