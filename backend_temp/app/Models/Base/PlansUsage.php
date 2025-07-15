<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PlansUsage
 * 
 * @property int $id
 * @property int|null $subscription_id
 * @property string|null $code
 * @property float $used
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class PlansUsage extends Model
{
	protected $table = 'plans_usages';

	protected $casts = [
		'subscription_id' => 'int',
		'used' => 'float'
	];
}
