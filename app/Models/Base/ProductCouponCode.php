<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProductCouponCode
 * 
 * @property int $id
 * @property int|null $user_id
 * @property int|null $product_id
 * @property string|null $code
 * @property string|null $type
 * @property Carbon|null $start_date
 * @property Carbon|null $end_date
 * @property int $discount
 * @property string|null $settings
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class ProductCouponCode extends Model
{
	protected $table = 'product_coupon_codes';

	protected $casts = [
		'user_id' => 'int',
		'product_id' => 'int',
		'start_date' => 'datetime',
		'end_date' => 'datetime',
		'discount' => 'int'
	];
}
