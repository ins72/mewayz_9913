<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PlansSubscription
 * 
 * @property int $id
 * @property int|null $plan_id
 * @property int|null $model_id
 * @property string|null $model_type
 * @property string|null $payment_method
 * @property bool $is_paid
 * @property float|null $charging_price
 * @property string|null $charging_currency
 * @property bool $is_recurring
 * @property int $recurring_each_days
 * @property Carbon|null $starts_on
 * @property Carbon|null $expires_on
 * @property Carbon|null $cancelled_on
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class PlansSubscription extends Model
{
	protected $table = 'plans_subscriptions';

	protected $casts = [
		'plan_id' => 'int',
		'model_id' => 'int',
		'is_paid' => 'bool',
		'charging_price' => 'float',
		'is_recurring' => 'bool',
		'recurring_each_days' => 'int',
		'starts_on' => 'datetime',
		'expires_on' => 'datetime',
		'cancelled_on' => 'datetime'
	];
}
