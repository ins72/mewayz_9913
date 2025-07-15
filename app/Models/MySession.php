<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\Visitor;

class MySession extends Model {
    /**
     * {@inheritdoc}
     */
    public $table = 'sessions';

    /**
     * {@inheritdoc}
     */
    public $timestamps = false;

    protected $casts = [
        'id'            => 'string',
        'tracking' => 'array'
    ];

    /**
     * Returns the user that belongs to this entry.
     *
     * @return \App\User
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Returns all the users within the given activity.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $limit
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActivity($query, $limit = 10)
    {
        $lastActivity = strtotime(Carbon::now()->subMinutes($limit));

        return $query->where('last_activity', '>=', $lastActivity);
    }

    public function scopeHasBio($query, $id){
        return $query->where('user_bio', '=', $id);
    }

    public function scopeHasUser($query){
        return $query->where('user_id', '!=', null);
    }

    /**
     * Returns all the guest users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGuests(Builder $query)
    {
        return $query->whereNull('user_id');
    }

    /**
     * Returns all the registered users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRegistered(Builder $query)
    {
        return $query->whereNotNull('user_id')->with('user');
    }

    /**
     * Updates the session of the current user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUpdateCurrent(Builder $query)
    {
        $user = Sentinel::check();

        return $query->where('id', Session::getId())->update([
            'user_id' => $user ? $user->id : null
        ]);
    }

    /**
     * Updates the session of the current Bio.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    // public function scopeUpdateBio(Builder $query, $id){
    //     $check = \App\Models\MySession::where('id', Session::getId())->where('user_bio', $id)->first();


    //     $ip = getIp(); //getIp() 102.89.2.139

    //     $agent = new \Jenssegers\Agent\Agent;
    //     $iso_code = geoCountry($ip, 'country.iso_code');
    //     $iso_code = strtolower($iso_code);
    //     $country = geoCountry($ip, 'country.names.en');
    //     $city = geoCity($ip, 'city.names.en');

    //     $tracking = ['country' => ['iso' => $iso_code, 'name' => $country, 'city' => $city], 'agent' => ['browser' => $agent->browser(), 'os' => $agent->platform()]];


    //     // Track Visits
    //     if ($vistor = Visitor::where('session', Session::getId())->first()) {
    //         $vistor = Visitor::find($vistor->id);
    //         $vistor->views = ($vistor->views + 1);
    //         $vistor->save();
    //     }else{
    //         $new = new Visitor;
    //         $new->user = $id;
    //         $new->session = Session::getId();
    //         $new->ip = $ip;
    //         $new->tracking = $tracking;
    //         $new->views = 1;
    //         $new->save();
    //     }


    //     if ($check) {
    //         return false;
    //     }


    //     // Update session
    //     return $query->where('id', Session::getId())->update([
    //         'user_bio' => $id,
    //         'tracking' => $tracking
    //     ]);
    // }


    public function scopeUpdateUser(Builder $query, $id){
        $check = \App\Models\MySession::where('id', Session::getId())->where('user_id', $id)->first();

        $ip = getIp(); //getIp() 102.89.2.139

        $agent = new \Jenssegers\Agent\Agent;
        $iso_code = geoCountry($ip, 'country.iso_code');
        $iso_code = strtolower($iso_code);
        $country = geoCountry($ip, 'country.names.en');
        $city = geoCity($ip, 'city.names.en');

        $tracking = ['country' => ['iso' => $iso_code, 'name' => $country, 'city' => $city], 'agent' => ['browser' => $agent->browser(), 'os' => $agent->platform()]];


        // Update session
        return $query->where('id', Session::getId())->update([
            'user_id' => $id,
            'tracking' => $tracking
        ]);
    }


    public function getInsight($user){
        // Get Model
        $visitors = \App\Models\MySession::activity(10)->where('site_id', $user->id)->get();

        // Empty array of visits
        $returned = [];


        // Get All Countries
        $countries = [];
        foreach ($visitors as $item) {
            $iso = ao($item->tracking, 'country.iso');
            $name = ao($item->tracking, 'country.name');

            if (!empty($iso) && !array_key_exists($iso, $countries)) {
                $countries[$iso] = [
                    'unique' => 0,
                    'name' => $name,
                ];
            }

            if (array_key_exists($iso, $countries)) {
                $countries[$iso]['unique']++;
            }
        }

        // Get ALL State

        $state = [];
        foreach ($visitors as $item) {
            $city = ao($item->tracking, 'country.city');
            $iso = ao($item->tracking, 'country.iso');
            $iso = strtoupper($iso);

            $check = "$city, $iso";

            if (!empty($city) && !array_key_exists($check, $state)) {
                $state[$check] = [
                    'unique' => 0,
                    'name' => $city,
                    'iso' => $iso,
                ];
            }

            if (array_key_exists($check, $state)) {
                $state[$check]['unique']++;
            }
        }

        // Get ALL Devices
        $devices = [];
        foreach ($visitors as $item) {
            $os = ao($item->tracking, 'agent.os');
            if (!empty($os) && !array_key_exists($os, $devices)) {
                $devices[$os] = [
                    'unique' => 0,
                    'name' => $os,
                ];
            }

            if (array_key_exists($os, $devices)) {
                $devices[$os]['unique']++;
            }
        }

        // Get AlL Browser
        $browsers = [];
        foreach ($visitors as $item) {
            $browser = ao($item->tracking, 'agent.browser');
            if (!empty($browser) && !array_key_exists($browser, $browsers)) {
                $browsers[$browser] = [
                    'unique' => 0,
                    'name' => $browser,
                ];
            }

            if (array_key_exists($browser, $browsers)) {
                $browsers[$browser]['unique']++;
            }
        }

        // Views
        $getviews = [];
        $unique = 0;
        foreach ($visitors as $item) {
            $unique++;
        }

        $getviews = [
            'unique' => $unique,
        ];


        $returned = ['countries' => $countries, 'cities' => $state, 'devices' => $devices, 'browsers' => $browsers, 'getviews' => $getviews];


        return $returned;
    }

    // public static function current_live(){
    //     if (!$user = \Auth::user()) {
    //         return 0;
    //     }


    //     $count = \App\Models\MySession::activity(10)->hasBio($user->id)->count();


    //     $text = sandy_plural($count, __('Visitor'), 'Visitors');
    //     return $count;
    // }
}