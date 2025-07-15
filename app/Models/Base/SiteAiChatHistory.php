<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SiteAiChatHistory
 * 
 * @property int $id
 * @property string $uuid
 * @property int|null $site_id
 * @property string|null $session_id
 * @property string|null $role
 * @property string|null $human
 * @property string|null $ai
 * @property string|null $response
 * @property string|null $extra
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class SiteAiChatHistory extends Model
{
	protected $table = 'site_ai_chat_history';

	protected $casts = [
		'site_id' => 'int'
	];
}
