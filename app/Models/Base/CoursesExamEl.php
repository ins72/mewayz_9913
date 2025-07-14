<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CoursesExamEl
 * 
 * @property int $id
 * @property int $user_id
 * @property int|null $exam_id
 * @property string|null $name
 * @property int $is_correct
 * @property string|null $settings
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class CoursesExamEl extends Model
{
	protected $table = 'courses_exam_el';

	protected $casts = [
		'user_id' => 'int',
		'exam_id' => 'int',
		'is_correct' => 'int'
	];
}
