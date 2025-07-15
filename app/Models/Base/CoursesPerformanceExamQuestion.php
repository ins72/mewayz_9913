<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CoursesPerformanceExamQuestion
 * 
 * @property int $id
 * @property int $user_id
 * @property int|null $exam_id
 * @property string|null $name
 * @property string|null $settings
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class CoursesPerformanceExamQuestion extends Model
{
	protected $table = 'courses_performance_exam_questions';

	protected $casts = [
		'user_id' => 'int',
		'exam_id' => 'int'
	];
}
