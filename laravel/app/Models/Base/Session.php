<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Session
 * 
 * @property string $id
 * @property int|null $user_id
 * @property int|null $site_id
 * @property int|null $page_id
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string $payload
 * @property string|null $tracking
 * @property int $last_activity
 *
 * @package App\Models\Base
 */
class Session extends Model
{
	protected $table = 'sessions';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'user_id' => 'int',
		'site_id' => 'int',
		'page_id' => 'int',
		'last_activity' => 'int'
	];
}
