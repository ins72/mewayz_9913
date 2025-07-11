<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BookingOrder
 * 
 * @property int $id
 * @property int $user_id
 * @property int|null $payee_user_id
 * @property int|null $appointment_id
 * @property string|null $method
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
class BookingOrder extends Model
{
	protected $table = 'booking_orders';

	protected $casts = [
		'user_id' => 'int',
		'payee_user_id' => 'int',
		'appointment_id' => 'int',
		'price' => 'float',
		'status' => 'int'
	];
}
