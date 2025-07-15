<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProjectPixel
 * 
 * @property int $id
 * @property int $user_id
 * @property string|null $name
 * @property string|null $domain
 * @property string|null $pixel
 * @property string|null $logo
 * @property string|null $_cta
 * @property string|null $_colors
 * @property string|null $settings
 * @property int $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class ProjectPixel extends Model
{
	protected $table = 'project_pixel';

	protected $casts = [
		'user_id' => 'int',
		'status' => 'int'
	];
}
