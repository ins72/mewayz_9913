<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CoursesExamDb
 * 
 * @property int $id
 * @property int $page_id
 * @property int $user_id
 * @property int|null $exam_id
 * @property int $is_passed
 * @property string|null $settings
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class CoursesExamDb extends Model
{
	protected $table = 'courses_exam_db';

	protected $casts = [
		'page_id' => 'int',
		'user_id' => 'int',
		'exam_id' => 'int',
		'is_passed' => 'int'
	];
}
