<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OrganizationCategory
 * 
 * @property int $id
 * @property int|null $_org
 * @property int $published
 * @property string|null $title
 * @property string|null $slug
 * @property string|null $content
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $uuid
 * @property Carbon|null $published_at
 * @property bool $is_published
 * @property bool $is_current
 * @property string|null $publisher_type
 * @property int|null $publisher_id
 *
 * @package App\Models\Base
 */
class OrganizationCategory extends Model
{
	protected $table = 'organization_category';

	protected $casts = [
		'_org' => 'int',
		'published' => 'int',
		'published_at' => 'datetime',
		'is_published' => 'bool',
		'is_current' => 'bool',
		'publisher_id' => 'int'
	];
}
