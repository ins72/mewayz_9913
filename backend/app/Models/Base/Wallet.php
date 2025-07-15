<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Wallet
 * 
 * @property int $id
 * @property string $holder_type
 * @property int $holder_id
 * @property string $name
 * @property string $slug
 * @property string $uuid
 * @property string|null $description
 * @property array|null $meta
 * @property float $balance
 * @property int $decimal_places
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class Wallet extends Model
{
	protected $table = 'wallets';

	protected $casts = [
		'holder_id' => 'int',
		'meta' => 'json',
		'balance' => 'float',
		'decimal_places' => 'int'
	];
}
