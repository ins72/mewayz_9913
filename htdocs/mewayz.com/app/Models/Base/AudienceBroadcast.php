<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AudienceBroadcast
 * 
 * @property int $id
 * @property int|null $user_id
 * @property string|null $subject
 * @property string|null $name
 * @property string|null $email
 * @property string|null $content
 * @property int|null $folder_id
 * @property int $schedule
 * @property Carbon|null $schedule_on
 * @property string|null $thumbnail
 * @property string|null $settings
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class AudienceBroadcast extends Model
{
	protected $table = 'audience_broadcast';

	protected $casts = [
		'user_id' => 'int',
		'folder_id' => 'int',
		'schedule' => 'int',
		'schedule_on' => 'datetime'
	];
}
