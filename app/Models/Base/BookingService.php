<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BookingService
 * 
 * @property int $id
 * @property int $user_id
 * @property string|null $name
 * @property string|null $thumbnail
 * @property float $price
 * @property int|null $duration
 * @property string|null $settings
 * @property string|null $booking_workhours
 * @property string $booking_time_interval
 * @property string|null $gallery
 * @property string|null $description
 * @property int $status
 * @property int $position
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class BookingService extends Model
{
	protected $table = 'booking_services';

	protected $casts = [
		'user_id' => 'int',
		'price' => 'float',
		'duration' => 'int',
		'status' => 'int',
		'position' => 'int'
	];
}
