<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CoursesPerformanceExamAnswer
 * 
 * @property int $id
 * @property int $user_id
 * @property int|null $exam_id
 * @property int|null $question_id
 * @property string|null $name
 * @property int $is_correct
 * @property string|null $settings
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class CoursesPerformanceExamAnswer extends Model
{
	protected $table = 'courses_performance_exam_answers';

	protected $casts = [
		'user_id' => 'int',
		'exam_id' => 'int',
		'question_id' => 'int',
		'is_correct' => 'int'
	];
}
