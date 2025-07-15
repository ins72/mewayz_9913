<?php

namespace App\Yena;

use App\Models\YenaTeam;
use App\Models\YenaTeamsUserTable;

class Teams{
    public static function checkState(){
        if(\App\Yena\Teams::is_set_team()){
            $team_user = \App\Yena\Teams::get_team_user_table();
            if(!$team_user) return self::logout();
        }
    }

    public static function get_original_user(){
        
        $auth = session()->get('impersonated_by');

        if(empty($auth)){
            $auth = auth()->user()->id;
        }
        $auth = \App\Models\User::find($auth);

        return $auth;
    }

    public static function get_team(){

        // Check if i'm in a team

        $auth = self::get_original_user();
        $get = YenaTeam::where('owner_id', $auth->id)->first();
        
        if(session()->has('set_team')){
            $get = YenaTeam::find(session()->get('set_team'));
        }

        return $get;
    }

    public static function logout(){
        session()->forget('set_team');
        \Auth::user()->leaveImpersonation();
        return redirect()->route('user-mix');
    }

    public static function is_set_team(){
        $manager = app('impersonate');

        if(session()->has('set_team') && $manager->isImpersonating()){
            return true;
        }

        return false;
    }

    public static function get_other_teams(){
        $auth = self::get_original_user();
        
        $check = YenaTeamsUserTable::where('user_id', $auth->id)->get();

        return $check;
    }
    
    public static function can_create(){
        
        // Team
        $auth = self::get_original_user();

        if(!$team = self::get_team()){
            return true;
        }

        $check = YenaTeamsUserTable::where('team_id', $team->id)->where('user_id', $auth->id)->first();

        if($team->owner_id == $auth->id){
            return true;
        }

        return $check ? $check->can_create : false;
    }

    public static function get_team_user_table(){
        $auth = self::get_original_user();

        if(!$team = self::get_team()) return false;

        $check = YenaTeamsUserTable::where('team_id', $team->id)->where('user_id', $auth->id)->first();

        if($team->owner_id == $auth->id) return false;

        return $check;
    }
    
    public static function permission($what = 'can_create'){
        
        // Team
        $auth = self::get_original_user();

        if(!$team = self::get_team()){
            return true;
        }

        $check = YenaTeamsUserTable::where('team_id', $team->id)->where('user_id', $auth->id)->first();

        if($team->owner_id == $auth->id){
            return true;
        }

        if($what == 'can_edit') $what = 'can_update';

        if($what == 'ce') $what = 'can_update';
        if($what == 'cd') $what = 'can_delete';
        if($what == 'cc') $what = 'can_create';

        return $check ? $check->{$what} : false;
    }

    public static function has_team($user, $team_id){
        
        
        $check = YenaTeamsUserTable::where('user_id', $user)->where('team_id', $team_id)->first();

        return $check;
    }

    public static function set_team($team_id){
        session()->put('set_team', $team_id);
        $get = YenaTeam::find($team_id);

        $auth = self::get_original_user();
        $auth->impersonate(\App\Models\User::find($get->owner_id));

        return true;
    }

    public static function add_to_team($user_id, $team_id, $is_accepted = 0, $permissions = []){
        if(YenaTeamsUserTable::where('user_id', $user_id)->where('team_id', $team_id)->first()) return false;

        $tu = new YenaTeamsUserTable;
        $tu->user_id = $user_id;
        $tu->team_id = $team_id;
        $tu->can_create = ao($permissions, 'can_create');
        $tu->can_update = ao($permissions, 'can_edit');
        $tu->can_delete = ao($permissions, 'can_delete');
        $tu->is_accepted = $is_accepted;

        $tu->save();
    }
    

	public static function init(){
		$user = \Auth::user();
        if($team = YenaTeam::where('owner_id', $user->id)->first()) return $team;

        $t = new YenaTeam;
		$t->slug = str()->random(20);
        $t->owner_id = $user->id;
        $t->name = "$user->name workspace";
        $t->save();

        return $t;
	}
}