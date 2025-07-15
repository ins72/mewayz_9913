<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Course
 * 
 * @property int $id
 * @property int $user_id
 * @property string|null $name
 * @property string|null $slug
 * @property int $status
 * @property int $price_type
 * @property float|null $price
 * @property string|null $price_pwyw
 * @property string|null $compare_price
 * @property string|null $course_level
 * @property string|null $settings
 * @property string|null $course_includes
 * @property string|null $course_duration
 * @property int $course_expiry_type
 * @property string|null $course_expiry
 * @property string|null $tags
 * @property string|null $banner
 * @property string|null $description
 * @property int $position
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class Course extends Model
{
	protected $table = 'courses';

	protected $casts = [
		'user_id' => 'int',
		'status' => 'int',
		'price_type' => 'int',
		'price' => 'float',
		'course_expiry_type' => 'int',
		'position' => 'int'
	];
}
