<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AudienceBroadcastSent
 * 
 * @property int $id
 * @property int|null $broadcast_id
 * @property int|null $broadcast_user_id
 * @property int $status
 * @property string|null $settings
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class AudienceBroadcastSent extends Model
{
	protected $table = 'audience_broadcast_sent';

	protected $casts = [
		'broadcast_id' => 'int',
		'broadcast_user_id' => 'int',
		'status' => 'int'
	];
}
