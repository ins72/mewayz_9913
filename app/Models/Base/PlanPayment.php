<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PlanPayment
 * 
 * @property int $id
 * @property int $user
 * @property string|null $name
 * @property string|null $plan
 * @property string|null $plan_name
 * @property string|null $duration
 * @property string|null $email
 * @property string|null $ref
 * @property string|null $currency
 * @property float|null $price
 * @property string|null $gateway
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class PlanPayment extends Model
{
	protected $table = 'plan_payments';

	protected $casts = [
		'user' => 'int',
		'price' => 'float'
	];
}
