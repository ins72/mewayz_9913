<?php

namespace App\Models;

use App\Models\Base\CoursesPerformanceExamQuestion as BaseCoursesPerformanceExamQuestion;

class CoursesPerformanceExamQuestion extends BaseCoursesPerformanceExamQuestion
{
	protected $fillable = [
		'user_id',
		'exam_id',
		'name',
		'settings'
	];

	public function answers(){
		return $this->hasMany(CoursesPerformanceExamAnswer::class, 'question_id', 'id');
	}
}
