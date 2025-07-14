<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AudienceFolder
 * 
 * @property int $id
 * @property int|null $user_id
 * @property string|null $name
 * @property string|null $thumbnail
 * @property string|null $settings
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class AudienceFolder extends Model
{
	protected $table = 'audience_folders';

	protected $casts = [
		'user_id' => 'int'
	];
}
