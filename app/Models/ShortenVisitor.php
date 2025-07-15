<?php

namespace App\Models;

use App\Models\Base\ShortenVisitor as BaseShortenVisitor;

class ShortenVisitor extends BaseShortenVisitor
{
	protected $fillable = [
		'link_id',
		'slug',
		'session',
		'ip',
		'tracking',
		'link',
		'views'
	];
}
