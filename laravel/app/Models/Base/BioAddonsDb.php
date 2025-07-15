<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BioAddonsDb
 * 
 * @property int $id
 * @property string $uuid
 * @property int $site_id
 * @property string|null $addon
 * @property string|null $email
 * @property string|null $database
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class BioAddonsDb extends Model
{
	protected $table = 'bio_addons_db';

	protected $casts = [
		'site_id' => 'int'
	];
}
