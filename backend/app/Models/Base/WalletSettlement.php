<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class WalletSettlement
 * 
 * @property int $id
 * @property int $user_id
 * @property int|null $settlement_id
 * @property string|null $settlement
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class WalletSettlement extends Model
{
	protected $table = 'wallet_settlements';

	protected $casts = [
		'user_id' => 'int',
		'settlement_id' => 'int'
	];
}
