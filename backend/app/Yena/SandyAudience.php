<?php

namespace App\Yena;

use App\Models\Audience;
use Jenssegers\Agent\Agent;
use App\Models\AudienceActivity;
use App\Models\User;

class SandyAudience{
    
    public static function subscribe_audience($owner = null, $page, $email = null){
        $extra = [
            'created_by' => 0,
        ];

        $name = explode('@', $email)[0];
        $contact = [
            'name' => $name,
            'email' => $email,
        ];
        
        $a = new Audience;
        $a->owner_id = $owner;
        $a->contact = $contact;
        $a->extra = $extra;
        $a->save();

        // Send Email

        // Create Activity
        self::create_activity($page->id, $a->id, __('Created'), __('Audience subscribed from :page.', ['page' => $page->id]));

        return true;
    }
    
    public static function create_audience($page_id = null, $user_id = null){
        if(Audience::where('owner_id', $page_id)->where('user_id', $user_id)->first()) return false;
        if(!$page = User::find($page_id)) return false;
        // if(!$user = User::find($user_id)) return false;
        $extra = [
            'created_by' => 0
        ];
        
        $a = new Audience;
        $a->owner_id = $page_id;
        $a->user_id = $user_id;
        $a->extra = $extra;
        $a->save();

        // Send Email

        // Create Activity
        self::create_activity($page->id, $a->id, __('Created'), __('Audience created successfully.'));

        return true;
    }

    public static function create_activity($user_id, $audience_id, $type = 'Email', $message){
        $agent = new Agent;


        $a = new AudienceActivity;
        $a->user_id = $user_id;
        $a->audience_id = $audience_id;
        $a->type = $type;
        $a->message = $message;
        $a->ip = getIp();
        $a->os = $agent->platform();

        $a->save();
    }

    public static function remove_audience($page_id, $user_id){

    }
}
