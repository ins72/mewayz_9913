<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserDonation
 * 
 * @property int $id
 * @property int $user_id
 * @property int|null $bio_id
 * @property int|null $payee_user_id
 * @property int $is_private
 * @property float|null $amount
 * @property string|null $currency
 * @property string|null $email
 * @property string|null $source
 * @property string|null $info
 * @property int $is_recurring
 * @property int|null $recurring_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class UserDonation extends Model
{
	protected $table = 'user_donations';

	protected $casts = [
		'user_id' => 'int',
		'bio_id' => 'int',
		'payee_user_id' => 'int',
		'is_private' => 'int',
		'amount' => 'float',
		'is_recurring' => 'int',
		'recurring_id' => 'int'
	];
}
