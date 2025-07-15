<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AudienceActivity
 * 
 * @property int $id
 * @property int $user_id
 * @property int $audience_id
 * @property string|null $type
 * @property string|null $message
 * @property string|null $ip
 * @property string|null $os
 * @property string|null $browser
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class AudienceActivity extends Model
{
	protected $table = 'audience_activity';

	protected $casts = [
		'user_id' => 'int',
		'audience_id' => 'int'
	];
}
