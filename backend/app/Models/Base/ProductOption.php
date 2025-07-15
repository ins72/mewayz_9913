<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProductOption
 * 
 * @property int $id
 * @property int $user_id
 * @property int $product_id
 * @property string|null $name
 * @property float|null $price
 * @property int|null $stock
 * @property string|null $description
 * @property string|null $files
 * @property int $position
 * @property string|null $variation_value
 * @property string|null $extra
 * @property string|null $image
 * @property string|null $type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class ProductOption extends Model
{
	protected $table = 'product_options';

	protected $casts = [
		'user_id' => 'int',
		'product_id' => 'int',
		'price' => 'float',
		'stock' => 'int',
		'position' => 'int'
	];
}
