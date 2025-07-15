<?php

namespace App\Models;

use App\Models\Base\LinkShortenerVisitor as BaseLinkShortenerVisitor;

class LinkShortenerVisitor extends BaseLinkShortenerVisitor
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

	protected $casts = [
		'tracking' => 'array'
	];
}
