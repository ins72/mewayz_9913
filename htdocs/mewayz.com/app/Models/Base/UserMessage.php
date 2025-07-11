<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserMessage
 * 
 * @property int $id
 * @property int $conversation_id
 * @property int $user_id
 * @property int|null $from_user_id
 * @property int|null $to_user_id
 * @property string|null $message
 * @property string $status
 * @property string|null $link
 * @property string|null $image
 * @property string $seen
 * @property string|null $extra
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class UserMessage extends Model
{
	protected $table = 'user_messages';

	protected $casts = [
		'conversation_id' => 'int',
		'user_id' => 'int',
		'from_user_id' => 'int',
		'to_user_id' => 'int'
	];
}
