<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CoursesPerformanceTakeDb
 * 
 * @property int $id
 * @property int $page_id
 * @property int $user_id
 * @property int|null $course_id
 * @property int|null $exam_id
 * @property int|null $question_id
 * @property int|null $selected_answer
 * @property string|null $selected_answer_name
 * @property int $is_passed
 * @property string|null $settings
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class CoursesPerformanceTakeDb extends Model
{
	protected $table = 'courses_performance_take_db';

	protected $casts = [
		'page_id' => 'int',
		'user_id' => 'int',
		'course_id' => 'int',
		'exam_id' => 'int',
		'question_id' => 'int',
		'selected_answer' => 'int',
		'is_passed' => 'int'
	];
}
