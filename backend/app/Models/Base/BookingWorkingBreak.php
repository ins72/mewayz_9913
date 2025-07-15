<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BookingWorkingBreak
 * 
 * @property int $id
 * @property int $user_id
 * @property Carbon|null $date
 * @property string|null $time
 * @property string|null $settings
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class BookingWorkingBreak extends Model
{
	protected $table = 'booking_working_breaks';

	protected $casts = [
		'user_id' => 'int',
		'date' => 'datetime'
	];
}
