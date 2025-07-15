<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProductCouponHistory
 * 
 * @property int $id
 * @property int|null $user_id
 * @property int|null $coupon_id
 * @property string|null $settings
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class ProductCouponHistory extends Model
{
	protected $table = 'product_coupon_history';

	protected $casts = [
		'user_id' => 'int',
		'coupon_id' => 'int'
	];
}
