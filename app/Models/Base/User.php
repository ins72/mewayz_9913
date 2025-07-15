<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User
 * 
 * @property int $id
 * @property string|null $provider_id
 * @property string|null $provider_name
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $facebook_id
 * @property string|null $google_id
 * @property string|null $settings
 * @property string|null $booking_gallery
 * @property string|null $booking_workhours
 * @property string $booking_time_interval
 * @property string|null $booking_description
 * @property string|null $booking_title
 * @property string $booking_hour_type
 * @property string|null $wallet_settings
 * @property string|null $title
 * @property string|null $store
 * @property string|null $last_subscription_uref
 * @property string|null $payment_subscription_ids
 * @property int $role
 * @property string|null $avatar
 * @property int|null $_last_project_id
 * @property int $status
 * @property string|null $lastActivity
 * @property string|null $lastAgent
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class User extends Model
{
	protected $table = 'users';

	protected $casts = [
		'email_verified_at' => 'datetime',
		'role' => 'int',
		'_last_project_id' => 'int',
		'status' => 'int'
	];
}
