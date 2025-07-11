<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Qrcode
 * 
 * @property int $id
 * @property int|null $user_id
 * @property string|null $text
 * @property string|null $logo
 * @property string|null $background
 * @property string|null $extra
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class Qrcode extends Model
{
	protected $table = 'qrcodes';

	protected $casts = [
		'user_id' => 'int'
	];
}
