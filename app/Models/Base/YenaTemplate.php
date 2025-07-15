<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class YenaTemplate
 * 
 * @property int $id
 * @property string $uuid
 * @property int|null $site_id
 * @property int|null $created_by
 * @property string|null $name
 * @property float|null $price
 * @property string|null $extra
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class YenaTemplate extends Model
{
	protected $table = 'yena_templates';

	protected $casts = [
		'site_id' => 'int',
		'created_by' => 'int',
		'price' => 'float'
	];
}
