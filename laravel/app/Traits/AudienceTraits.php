<?php

namespace App\Traits;

use App\Models\User;
use Carbon\Carbon;
use App\Models\Audience;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

trait AudienceTraits{
    public $contact = [];

    // public function __construct() {
    //     $this->contact = [];
    // }

    public function set($audience = false){
        if (is_numeric($audience)) {
            if ($contact = Audience::find($audience)) {
                $this->contact = $contact;
            }
        } else {
            $this->contact = $audience;
        }
    
        return $this;
    }

    public function avatar($is_html = true){
        if(!$contact = $this->contact) return false;
        $name = ao($contact->contact, 'name');

        $avatar = $this->contact->getAvatar();

        if($user = $contact->_user) $avatar = $user->getAvatar();

        return $avatar;
    }

    public function has_page(){
        if(!$contact = $this->contact) return false;
        if($user = User::find($contact->user)) return $user;

        return false;
    }

    public function info($key = null){

        try {
            if(!$contact = $this->contact) return;

            $array = $contact->contact ?? [];
            $array['avatar'] = $contact->avatar;
            
    
            if($user = User::find($contact->user_id)) $array = $user->toArray();
            return ao($array, $key);
        } catch (\Throwable $th) {
            return null;
        }
    }
}
