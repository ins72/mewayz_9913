<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Setting
 * 
 * @property int $id
 * @property string $key
 * @property string $value
 *
 * @package App\Models\Base
 */
class Setting extends Model
{
	protected $table = 'settings';
	public $timestamps = false;
}
