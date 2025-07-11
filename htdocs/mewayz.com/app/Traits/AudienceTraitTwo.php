<?php

namespace App\Traits;

use App\Models\User;
use Carbon\Carbon;
use App\Models\Audience;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

trait AudienceTraitTwo{
    public $data = [];

    public function set($audience = false){
        if (is_numeric($audience)) {
            if ($data = Audience::find($audience)) {
                $this->data = $data;
            }
        } else {
            $this->data = $audience;
        }
    
        return $this;
    }

    public function avatar($is_html = true){
        if(!$data = $this->data) return false;
        $name = ao($data->contact, 'name');

        $avatar = $this->data->getAvatar();

        if($user = $data->_user) $avatar = $user->getAvatar();

        return $avatar;
    }

    public function has_page(){
        if(!$data = $this->data) return false;
        if($user = User::find($data->user)) return $user;

        return false;
    }

    public function info($key = null){

        if(!$data = $this->data) return;

        $array = $data->contact ?? [];
        $array['avatar'] = $data->avatar;
        

        if($user = User::find($data->user_id)) $array = $user->toArray();
        return ao($array, $key);
    }
}
