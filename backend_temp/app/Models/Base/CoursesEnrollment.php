<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CoursesEnrollment
 * 
 * @property int $id
 * @property int $user_id
 * @property int|null $payee_user_id
 * @property int|null $course_id
 * @property string|null $lesson_taken
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class CoursesEnrollment extends Model
{
	protected $table = 'courses_enrollments';

	protected $casts = [
		'user_id' => 'int',
		'payee_user_id' => 'int',
		'course_id' => 'int'
	];
}
