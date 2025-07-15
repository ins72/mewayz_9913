<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserConversation
 * 
 * @property int $id
 * @property int $user_1
 * @property int $user_2
 * @property int $status
 * @property string|null $extra
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class UserConversation extends Model
{
	protected $table = 'user_conversations';

	protected $casts = [
		'user_1' => 'int',
		'user_2' => 'int',
		'status' => 'int'
	];
}
