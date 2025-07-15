<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class InvoicesTimeline
 * 
 * @property int $id
 * @property int $user_id
 * @property int $invoice_id
 * @property string|null $type
 * @property string|null $data
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class InvoicesTimeline extends Model
{
	protected $table = 'invoices_timeline';

	protected $casts = [
		'user_id' => 'int',
		'invoice_id' => 'int'
	];
}
