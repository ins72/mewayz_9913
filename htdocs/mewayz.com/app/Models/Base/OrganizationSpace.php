<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OrganizationSpace
 * 
 * @property int $id
 * @property int|null $_org
 * @property int $default
 * @property string $name
 * @property string|null $slug
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class OrganizationSpace extends Model
{
	protected $table = 'organization_spaces';

	protected $casts = [
		'_org' => 'int',
		'default' => 'int'
	];
}
