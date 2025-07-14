<?php

namespace App\Models;

use App\Models\Base\CoursesExamDb as BaseCoursesExamDb;

class CoursesExamDb extends BaseCoursesExamDb
{
	protected $fillable = [
		'page_id',
		'user_id',
		'exam_id',
		'is_passed',
		'settings'
	];
}
