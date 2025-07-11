<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProjectPixelKeyword
 * 
 * @property int $id
 * @property int $project_id
 * @property int|null $feedback_id
 * @property string|null $keyword
 * @property string|null $settings
 * @property int $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class ProjectPixelKeyword extends Model
{
	protected $table = 'project_pixel_keywords';

	protected $casts = [
		'project_id' => 'int',
		'feedback_id' => 'int',
		'status' => 'int'
	];
}
