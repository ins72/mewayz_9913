<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProductShippingLocation
 * 
 * @property int $id
 * @property int $user
 * @property int|null $shipping_id
 * @property string|null $name
 * @property string|null $description
 * @property float|null $price
 * @property string|null $extra
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class ProductShippingLocation extends Model
{
	protected $table = 'product_shipping_locations';

	protected $casts = [
		'user' => 'int',
		'shipping_id' => 'int',
		'price' => 'float'
	];
}
