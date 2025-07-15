<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Product
 * 
 * @property int $id
 * @property int $user_id
 * @property string|null $name
 * @property string|null $slug
 * @property int $status
 * @property int $price_type
 * @property float|null $price
 * @property string|null $price_pwyw
 * @property string|null $comparePrice
 * @property int $enableOptions
 * @property int $isDeal
 * @property string|null $dealPrice
 * @property Carbon|null $dealEnds
 * @property int $enableBid
 * @property int|null $stock
 * @property string|null $stock_settings
 * @property string|null $sku
 * @property int $productType
 * @property string|null $banner
 * @property string|null $featured_img
 * @property string|null $media
 * @property string|null $description
 * @property string|null $external_product_link
 * @property string|null $min_quantity
 * @property string|null $ribbon
 * @property string|null $seo
 * @property string|null $api
 * @property string|null $files
 * @property string|null $extra
 * @property int $position
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class Product extends Model
{
	protected $table = 'products';

	protected $casts = [
		'user_id' => 'int',
		'status' => 'int',
		'price_type' => 'int',
		'price' => 'float',
		'enableOptions' => 'int',
		'isDeal' => 'int',
		'dealEnds' => 'datetime',
		'enableBid' => 'int',
		'stock' => 'int',
		'productType' => 'int',
		'position' => 'int'
	];
}
