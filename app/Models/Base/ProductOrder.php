<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProductOrder
 * 
 * @property int $id
 * @property int $user_id
 * @property int|null $payee_user_id
 * @property string|null $details
 * @property string|null $currency
 * @property string|null $email
 * @property string|null $ref
 * @property float|null $price
 * @property int $is_deal
 * @property int|null $deal_discount
 * @property string|null $products
 * @property string|null $extra
 * @property int $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class ProductOrder extends Model
{
	protected $table = 'product_order';

	protected $casts = [
		'user_id' => 'int',
		'payee_user_id' => 'int',
		'price' => 'float',
		'is_deal' => 'int',
		'deal_discount' => 'int',
		'status' => 'int'
	];
}
