<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProductOrderNote
 * 
 * @property int $id
 * @property int $user_id
 * @property int $tid
 * @property string|null $note
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class ProductOrderNote extends Model
{
	protected $table = 'product_order_note';

	protected $casts = [
		'user_id' => 'int',
		'tid' => 'int'
	];
}
