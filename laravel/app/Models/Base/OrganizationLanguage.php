<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OrganizationLanguage
 * 
 * @property int $id
 * @property int|null $_org
 * @property int $default
 * @property string $lang
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class OrganizationLanguage extends Model
{
	protected $table = 'organization_languages';

	protected $casts = [
		'_org' => 'int',
		'default' => 'int'
	];
}
