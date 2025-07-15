<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Draftable
 * 
 * @property int $id
 * @property string $draftable_data
 * @property string $draftable_model
 * @property int|null $draftable_id
 * @property int|null $user_id
 * @property string|null $data
 * @property Carbon|null $published_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class Draftable extends Model
{
	protected $table = 'draftables';

	protected $casts = [
		'draftable_id' => 'int',
		'user_id' => 'int',
		'published_at' => 'datetime'
	];
}
