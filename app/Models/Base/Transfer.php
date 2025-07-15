<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Transfer
 * 
 * @property int $id
 * @property string $from_type
 * @property int $from_id
 * @property string $to_type
 * @property int $to_id
 * @property string $status
 * @property string|null $status_last
 * @property int $deposit_id
 * @property int $withdraw_id
 * @property float $discount
 * @property float $fee
 * @property string $uuid
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class Transfer extends Model
{
	protected $table = 'transfers';

	protected $casts = [
		'from_id' => 'int',
		'to_id' => 'int',
		'deposit_id' => 'int',
		'withdraw_id' => 'int',
		'discount' => 'float',
		'fee' => 'float'
	];
}
