<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OrganizationCategoryDraft
 * 
 * @property int $id
 * @property int|null $_org
 * @property int $published
 * @property string|null $title
 * @property string|null $slug
 * @property string|null $content
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class OrganizationCategoryDraft extends Model
{
	protected $table = 'organization_category_draft';

	protected $casts = [
		'_org' => 'int',
		'published' => 'int'
	];
}
