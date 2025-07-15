<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Plan
 * 
 * @property int $id
 * @property string|null $name
 * @property string|null $description
 * @property float|null $price
 * @property float|null $annual_price
 * @property int|null $is_free
 * @property int|null $has_trial
 * @property string|null $trial_days
 * @property string|null $currency
 * @property int $duration
 * @property string|null $metadata
 * @property string $status
 * @property string|null $slug
 * @property int $position
 * @property int $defaults
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class Plan extends Model
{
	protected $table = 'plans';

	protected $casts = [
		'price' => 'float',
		'annual_price' => 'float',
		'is_free' => 'int',
		'has_trial' => 'int',
		'duration' => 'int',
		'position' => 'int',
		'defaults' => 'int'
	];
}
