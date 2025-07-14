<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AudienceBroadcastUser
 * 
 * @property int $id
 * @property int|null $broadcast_id
 * @property int|null $audience_id
 * @property string|null $settings
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class AudienceBroadcastUser extends Model
{
	protected $table = 'audience_broadcast_users';

	protected $casts = [
		'broadcast_id' => 'int',
		'audience_id' => 'int'
	];
}
