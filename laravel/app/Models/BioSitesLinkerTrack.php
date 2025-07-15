<?php

namespace App\Models;

use App\Models\Base\BioSitesLinkerTrack as BaseBioSitesLinkerTrack;

class BioSitesLinkerTrack extends BaseBioSitesLinkerTrack
{
	protected $fillable = [
		'linker',
		'site_id',
		'session',
		'link',
		'slug',
		'ip',
		'tracking',
		'views'
	];
	protected $casts = [
		'tracking' => 'array',
	];
	
    public function topLink($user, $limit = 5){
    	$model = new BioSitesLinkerTrack;
		
		$log = [];

		$totalVisits = 0;
    	// 

    	foreach ($model->where('site_id', $user->id)->get() as $item) {
            if(!array_key_exists($item->slug, $log)) {
                $log[$item->slug] = [
                    'visits' => 0,
                    'unique' => 0,
                    'link' => $item->link,
                ];
            }

            if (array_key_exists($item->slug, $log)) {
	            $log[$item->slug]['unique']++;
	            $log[$item->slug]['visits'] += $item->views;

	            $totalVisits = ($totalVisits + $item->views);
            }
    	}

    	$col = array_column( $log, "visits" );
    	array_multisort( $col, SORT_DESC, $log );

    	$log = array_slice($log, 0, $limit);

    	return $log;
    }
    public function totalVisits($user){
    	$model = new BioSitesLinkerTrack;
		
		$log = [];

		$visits = 0;
		$unique = 0;
    	// 

    	foreach ($model->where('site_id', $user->id)->get() as $item) {
    		$unique++;
    		$visits += $item->views;
    	}

    	$log = ['visits' => $visits, 'unique' => $unique];

    	return $log;
    }


	public function getLinkInsight($slug, $user){
        // Get Model
        $visitors = BioSitesLinkerTrack::where('slug', $slug)->where('site_id', $user->id)->get();

		// dd($visitors, $slug, $user);

        // Empty array of visits
        $returned = [];


        // Get All Countries
        $countries = [];
        foreach ($visitors as $item) {
            $iso = ao($item->tracking, 'country.iso');
            $name = ao($item->tracking, 'country.name');

            if (!empty($iso) && !array_key_exists($iso, $countries)) {
                $countries[$iso] = [
                    'visits' => 0,
                    'unique' => 0,
                    'name' => $name,
                ];
            }

            if (array_key_exists($iso, $countries)) {
                $countries[$iso]['unique']++;
                $countries[$iso]['visits'] += $item->views;
            }
        }

        // Get ALL State

        $state = [];
        foreach ($visitors as $item) {
            $city = (string) ao($item->tracking, 'country.city');
            $iso = (string) ao($item->tracking, 'country.iso');
            $iso = strtoupper($iso);

            $check = "$city, $iso";

            if (!empty($city) && !array_key_exists($check, $state)) {
                $state[$check] = [
                    'visits' => 0,
                    'unique' => 0,
                    'name' => $city,
                    'iso' => $iso,
                ];
            }

            if (array_key_exists($check, $state)) {
                $state[$check]['unique']++;
                $state[$check]['visits'] += $item->views;
            }
        }

        // Get ALL Devices
        $devices = [];
        foreach ($visitors as $item) {
            $os = (string) ao($item->tracking, 'agent.os');
            if (!empty($os) && !array_key_exists($os, $devices)) {
                $devices[$os] = [
                    'visits' => 0,
                    'unique' => 0,
                    'name' => $os,
                ];
            }

            if (array_key_exists($os, $devices)) {
                $devices[$os]['unique']++;
                $devices[$os]['visits'] += $item->views;
            }
        }

        // Get AlL Browser
        $browsers = [];
        foreach ($visitors as $item) {
            $browser = (string) ao($item->tracking, 'agent.browser');
            if (!empty($browser) && !array_key_exists($browser, $browsers)) {
                $browsers[$browser] = [
                    'visits' => 0,
                    'unique' => 0,
                    'name' => $browser,
                ];
            }

            if (array_key_exists($browser, $browsers)) {
                $browsers[$browser]['unique']++;
                $browsers[$browser]['visits'] += $item->views;
            }
        }

        // Views
        $getviews = [];
        $views = 0;
        $unique = 0;
        foreach ($visitors as $item) {
            $unique++;
            $views += $item->views;
        }

        $getviews = [
            'visits' => $views,
            'unique' => $unique,
        ];
        $returned = ['countries' => $countries, 'cities' => $state, 'devices' => $devices, 'browsers' => $browsers, 'getviews' => $getviews];


        return $returned;
	}

    public function _linker($link, $site_id){
        $link = str_replace(['http://', 'https://'], '', $link);
        $model = new BioSitesLinker;
        $_create_linker = function() use ($model, $link, $site_id){
            $slug = str()->random(5);
            $new = $model;
            $new->url = $link;
            $new->slug = $slug;
            $new->site_id = $site_id;
            $new->save();

            return $new;
        };

        if (!$linker = $model->where('site_id', $site_id)->where('url', $link)->first()) {
            $linker = $_create_linker();
        }


        return $linker;
    }

    public function track($link, $site_id){
        $ip = getIp(); //getIp() or 102.89.2.139 for test

        $tracking = tracking_log();
        $linker = $this->_linker($link, $site_id);


        // Track Visits
        if ($vistor = BioSitesLinkerTrack::where('session', Session::getId())->where('slug', $linker->slug)->first()) {
            $vistor = BioSitesLinkerTrack::find($vistor->id);
            $vistor->views = ($vistor->views + 1);
            $vistor->save();
        }else{
            $new = new BioSitesLinkerTrack;
            $new->site_id = $site_id;
            $new->session = Session::getId();
            $new->linker = $linker->id;
            $new->link = $link;
            $new->slug = $linker->slug;
            $new->ip = $ip;
            $new->tracking = $tracking;
            $new->views = 1;
            $new->save();
        }
    }
}
