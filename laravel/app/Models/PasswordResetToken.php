<?php

namespace App\Models;

use App\Models\Base\PasswordResetToken as BasePasswordResetToken;

class PasswordResetToken extends BasePasswordResetToken
{
	protected $hidden = [
		'token'
	];

	protected $fillable = [
		'token'
	];
}
