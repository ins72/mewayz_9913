<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PlansHistory
 * 
 * @property int $id
 * @property int $plan_id
 * @property int $user_id
 * @property int|null $trial
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class PlansHistory extends Model
{
	protected $table = 'plans_history';

	protected $casts = [
		'plan_id' => 'int',
		'user_id' => 'int',
		'trial' => 'int'
	];
}
