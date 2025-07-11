<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Transaction
 * 
 * @property int $id
 * @property string $payable_type
 * @property int $payable_id
 * @property int $wallet_id
 * @property string $type
 * @property float $amount
 * @property bool $confirmed
 * @property array|null $meta
 * @property string $uuid
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class Transaction extends Model
{
	protected $table = 'transactions';

	protected $casts = [
		'payable_id' => 'int',
		'wallet_id' => 'int',
		'amount' => 'float',
		'confirmed' => 'bool',
		'meta' => 'json'
	];
}
