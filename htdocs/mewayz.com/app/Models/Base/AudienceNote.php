<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AudienceNote
 * 
 * @property int $id
 * @property int $user_id
 * @property int $audience_id
 * @property string|null $note
 * @property string|null $extra
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class AudienceNote extends Model
{
	protected $table = 'audience_notes';

	protected $casts = [
		'user_id' => 'int',
		'audience_id' => 'int'
	];
}
