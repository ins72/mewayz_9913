<?php

namespace App\Models;

use App\Models\Base\Invoice as BaseInvoice;

class Invoice extends BaseInvoice
{
	protected $fillable = [
		'user_id',
		'slug',
		'price',
		'draft',
		'paid',
		'due',
		'data',
		'payer',
		'settings'
	];

	protected $casts = [
		'data' => 'array',
		'payer' => 'array',
		'settings' => 'array',
	];
	
	public function addTimeline($type = '', $data = ''){
		if($this->timelines()->where('type', $type)->first()) return;
		

		$insert = new InvoicesTimeline;
		$insert->invoice_id = $this->id;
		$insert->user_id = $this->user_id;
		$insert->type = $type;
		$insert->data = $data;
		$insert->save();

		return true;
	}


	public function user(){
		return $this->belongsTo(User::class, 'user_id', 'id');
	}

	public function timelines()
	{
		return $this->hasMany(InvoicesTimeline::class, 'invoice_id', 'id');
	}
}
