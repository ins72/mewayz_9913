<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BookingAppointment
 * 
 * @property int $id
 * @property int $user_id
 * @property int|null $payee_user_id
 * @property string|null $service_ids
 * @property Carbon|null $date
 * @property string|null $time
 * @property string|null $settings
 * @property string|null $info
 * @property int $appointment_status
 * @property float|null $price
 * @property int $is_paid
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class BookingAppointment extends Model
{
	protected $table = 'booking_appointments';

	protected $casts = [
		'user_id' => 'int',
		'payee_user_id' => 'int',
		'date' => 'datetime',
		'appointment_status' => 'int',
		'price' => 'float',
		'is_paid' => 'int'
	];
}
