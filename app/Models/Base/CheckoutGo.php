<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CheckoutGo
 * 
 * @property int $id
 * @property string|null $uref
 * @property string|null $email
 * @property string|null $currency
 * @property string|null $payment_subscription_id
 * @property string $payment_type
 * @property string $frequency
 * @property float|null $price
 * @property int $paid
 * @property string|null $method
 * @property string|null $callback
 * @property string|null $call_function
 * @property string|null $keys
 * @property string|null $meta
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class CheckoutGo extends Model
{
	protected $table = 'checkout_go';

	protected $casts = [
		'price' => 'float',
		'paid' => 'int'
	];
}
