<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProductOrderTimeline
 * 
 * @property int $id
 * @property int $user_id
 * @property int $tid
 * @property string|null $type
 * @property string|null $data
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class ProductOrderTimeline extends Model
{
	protected $table = 'product_order_timeline';

	protected $casts = [
		'user_id' => 'int',
		'tid' => 'int'
	];
}
