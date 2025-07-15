<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserDonationsRecurring
 * 
 * @property int $id
 * @property int $user_id
 * @property int $is_active
 * @property string|null $last_subscription_uref
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class UserDonationsRecurring extends Model
{
	protected $table = 'user_donations_recurring';

	protected $casts = [
		'user_id' => 'int',
		'is_active' => 'int'
	];
}
