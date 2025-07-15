<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CoursesLesson
 * 
 * @property int $id
 * @property int $user_id
 * @property int|null $course_id
 * @property string|null $name
 * @property string|null $description
 * @property string $lesson_type
 * @property int $status
 * @property string|null $lesson_duration
 * @property string|null $lesson_data
 * @property string|null $settings
 * @property int $position
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class CoursesLesson extends Model
{
	protected $table = 'courses_lesson';

	protected $casts = [
		'user_id' => 'int',
		'course_id' => 'int',
		'status' => 'int',
		'position' => 'int'
	];
}
