<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SiteForm
 * 
 * @property int $id
 * @property string $uuid
 * @property int|null $site_id
 * @property string|null $email
 * @property string|null $content
 * @property string|null $settings
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class SiteForm extends Model
{
	protected $table = 'site_forms';

	protected $casts = [
		'site_id' => 'int'
	];
}
