<?php

namespace App\Models;

use App\Models\Base\UserConversation as BaseUserConversation;

class UserConversation extends BaseUserConversation
{
	protected $fillable = [
		'user_1',
		'user_2',
		'status',
		'extra'
	];

	
	public $timestamps = false;

	public function user(){
		  return $this->belongsTo('App\Models\User', 'user_id')->first();
	}
  
	public function last(){
		return $this->hasMany('App\Models\UserMessage','conversation_id')
			//->where('messages.mode', 'active')
			->orderBy('user_messages.updated_at', 'DESC')
			->take(1)
			->first();
	}
  
	public function messages(){
		return $this->hasMany('App\Models\UserMessage','conversation_id')
		  //->where('messages.mode', 'active')
		  ->orderBy('user_messages.updated_at', 'DESC');
	}
  
	public function from(){
		  return $this->belongsTo('App\Models\User', 'from_user_id')->first();
	}
  
	public function to(){
		  return $this->belongsTo('App\Models\User', 'to_user_id')->first();
	}
}
