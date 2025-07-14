<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProjectPixelDatum
 * 
 * @property int $id
 * @property int $project_id
 * @property string|null $email
 * @property string|null $feedback
 * @property string|null $reaction
 * @property string|null $_tracking
 * @property string|null $_tags
 * @property string|null $settings
 * @property int $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class ProjectPixelDatum extends Model
{
	protected $table = 'project_pixel_data';

	protected $casts = [
		'project_id' => 'int',
		'status' => 'int'
	];
}
