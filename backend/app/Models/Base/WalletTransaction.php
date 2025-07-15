<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class WalletTransaction
 * 
 * @property int $id
 * @property int $user_id
 * @property string|null $method
 * @property int|null $spv_id
 * @property string|null $type
 * @property float|null $amount
 * @property float|null $amount_settled
 * @property string|null $currency
 * @property string|null $transaction
 * @property string|null $payload
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class WalletTransaction extends Model
{
	protected $table = 'wallet_transactions';

	protected $casts = [
		'user_id' => 'int',
		'spv_id' => 'int',
		'amount' => 'float',
		'amount_settled' => 'float'
	];
}
