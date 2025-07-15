<?php

namespace App\Models;

use App\Models\Base\CoursesPerformanceExam as BaseCoursesPerformanceExam;

class CoursesPerformanceExam extends BaseCoursesPerformanceExam
{
	protected $fillable = [
		'user_id',
		'name',
		'level',
		'description',
		'settings'
	];
	
	public function questions(){
		return $this->hasMany(CoursesPerformanceExamQuestion::class, 'exam_id', 'id');
	}
}
