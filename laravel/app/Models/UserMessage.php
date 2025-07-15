<?php

namespace App\Models;

use App\Models\Base\UserMessage as BaseUserMessage;

class UserMessage extends BaseUserMessage
{
	protected $fillable = [
		'conversation_id',
		'user_id',
		'from_user_id',
		'to_user_id',
		'message',
		'status',
		'link',
		'image',
		'seen',
		'extra'
	];

	public function user()
	{
	  return $this->belongsTo('App\Models\User', 'from_user_id')->first();
	}
  
	public function from()
	{
	  return $this->belongsTo('App\Models\User', 'from_user_id')->first();
	}
  
	public function to()
	{
	  return $this->belongsTo('App\Models\User', 'to_user_id')->first();
	}
  
	public function markSeen(){
	  $this->timestamps = false;
	  $this->status = 'readed';
	  $this->save();
	}
}
