<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProductShipping
 * 
 * @property int $id
 * @property int $user_id
 * @property float|null $price
 * @property string|null $name
 * @property string|null $description
 * @property string|null $country_iso
 * @property string|null $country
 * @property string|null $locations
 * @property string|null $extra
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class ProductShipping extends Model
{
	protected $table = 'product_shipping';

	protected $casts = [
		'user_id' => 'int',
		'price' => 'float'
	];
}
