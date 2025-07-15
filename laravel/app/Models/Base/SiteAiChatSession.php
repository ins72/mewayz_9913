<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SiteAiChatSession
 * 
 * @property int $id
 * @property string $uuid
 * @property int|null $site_id
 * @property int|null $started_by
 * @property string|null $session
 * @property string|null $extra
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class SiteAiChatSession extends Model
{
	protected $table = 'site_ai_chat_session';

	protected $casts = [
		'site_id' => 'int',
		'started_by' => 'int'
	];
}
