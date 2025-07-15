<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CoursesOrder
 * 
 * @property int $id
 * @property int $user_id
 * @property int|null $payee_user_id
 * @property int|null $course_id
 * @property string|null $details
 * @property string|null $currency
 * @property string|null $email
 * @property string|null $ref
 * @property float|null $price
 * @property string|null $extra
 * @property int $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class CoursesOrder extends Model
{
	protected $table = 'courses_order';

	protected $casts = [
		'user_id' => 'int',
		'payee_user_id' => 'int',
		'course_id' => 'int',
		'price' => 'float',
		'status' => 'int'
	];
}
