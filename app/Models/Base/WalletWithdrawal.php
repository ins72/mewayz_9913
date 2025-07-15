<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class WalletWithdrawal
 * 
 * @property int $id
 * @property int|null $user_id
 * @property float|null $amount
 * @property string|null $note
 * @property int|null $is_paid
 * @property string|null $transaction
 * @property string|null $extra
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class WalletWithdrawal extends Model
{
	protected $table = 'wallet_withdrawals';

	protected $casts = [
		'user_id' => 'int',
		'amount' => 'float',
		'is_paid' => 'int'
	];
}
