<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProjectSuggestion
 * 
 * @property int $id
 * @property int $project_id
 * @property string|null $response
 * @property string|null $settings
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class ProjectSuggestion extends Model
{
	protected $table = 'project_suggestions';

	protected $casts = [
		'project_id' => 'int'
	];
}
