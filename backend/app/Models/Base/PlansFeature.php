<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PlansFeature
 * 
 * @property int $id
 * @property int|null $plan_id
 * @property int|null $enable
 * @property string|null $name
 * @property string|null $code
 * @property string|null $description
 * @property string $type
 * @property int $limit
 * @property string|null $metadata
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class PlansFeature extends Model
{
	protected $table = 'plans_features';

	protected $casts = [
		'plan_id' => 'int',
		'enable' => 'int',
		'limit' => 'int'
	];
}
