<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CoursesPerformanceExam
 * 
 * @property int $id
 * @property int $user_id
 * @property string|null $name
 * @property string|null $level
 * @property string|null $description
 * @property string|null $settings
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class CoursesPerformanceExam extends Model
{
	protected $table = 'courses_performance_exam';

	protected $casts = [
		'user_id' => 'int'
	];
}
