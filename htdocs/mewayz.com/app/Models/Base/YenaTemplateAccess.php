<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class YenaTemplateAccess
 * 
 * @property int $id
 * @property string $uuid
 * @property int|null $template_id
 * @property int|null $user_id
 * @property int|null $site_id
 * @property string|null $extra
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class YenaTemplateAccess extends Model
{
	protected $table = 'yena_template_access';

	protected $casts = [
		'template_id' => 'int',
		'user_id' => 'int',
		'site_id' => 'int'
	];
}
