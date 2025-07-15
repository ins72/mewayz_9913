<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Audience
 * 
 * @property int $id
 * @property int|null $user_id
 * @property int $owner_id
 * @property int|null $is_collab
 * @property string|null $name
 * @property string|null $avatar
 * @property string|null $tags
 * @property string|null $contact
 * @property string|null $extra
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class Audience extends Model
{
	protected $table = 'audience';

	protected $casts = [
		'user_id' => 'int',
		'owner_id' => 'int',
		'is_collab' => 'int'
	];
}
