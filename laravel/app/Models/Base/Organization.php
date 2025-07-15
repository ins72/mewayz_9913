<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Organization
 * 
 * @property int $id
 * @property int $user_id
 * @property string|null $name
 * @property string $address
 * @property string|null $logo
 * @property string|null $favicon
 * @property string|null $_cta
 * @property string|null $_colors
 * @property string|null $settings
 * @property int $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class Organization extends Model
{
	protected $table = 'organization';

	protected $casts = [
		'user_id' => 'int',
		'status' => 'int'
	];
}
