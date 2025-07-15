<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CoursesIntro
 * 
 * @property int $id
 * @property int $user_id
 * @property int|null $course_id
 * @property string|null $name
 * @property string|null $file
 * @property string|null $settings
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class CoursesIntro extends Model
{
	protected $table = 'courses_intro';

	protected $casts = [
		'user_id' => 'int',
		'course_id' => 'int'
	];
}
