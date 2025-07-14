<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OrganizationDomain
 * 
 * @property int $id
 * @property int|null $_org
 * @property int $is_active
 * @property string|null $scheme
 * @property string|null $host
 * @property string|null $settings
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class OrganizationDomain extends Model
{
	protected $table = 'organization_domains';

	protected $casts = [
		'_org' => 'int',
		'is_active' => 'int'
	];
}
