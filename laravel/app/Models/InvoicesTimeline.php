<?php

namespace App\Models;

use App\Models\Base\InvoicesTimeline as BaseInvoicesTimeline;

class InvoicesTimeline extends BaseInvoicesTimeline
{
	protected $fillable = [
		'user_id',
		'invoice_id',
		'type',
		'data'
	];
}
