<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Invoice
 * 
 * @property int $id
 * @property int|null $user_id
 * @property string|null $slug
 * @property float|null $price
 * @property int $draft
 * @property int $paid
 * @property string|null $due
 * @property string|null $data
 * @property string|null $payer
 * @property string|null $settings
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class Invoice extends Model
{
	protected $table = 'invoices';

	protected $casts = [
		'user_id' => 'int',
		'price' => 'float',
		'draft' => 'int',
		'paid' => 'int'
	];
}
