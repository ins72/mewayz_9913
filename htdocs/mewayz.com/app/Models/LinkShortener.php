<?php

namespace App\Models;

use App\Models\Base\LinkShortener as BaseLinkShortener;

class LinkShortener extends BaseLinkShortener
{
	protected $fillable = [
		'user_id',
		'slug',
		'link',
		'settings'
	];

	protected $casts = [
		'settings' => 'array'	
	];

	public function getIcon(){

		return "https://api.dicebear.com/9.x/glass/svg?seed=$this->slug";
	}
	
	/**
	 * Get all of the items for the Section
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function visitors()
	{
		return $this->hasMany(LinkShortenerVisitor::class, 'link_id', 'id');
	}

	public function getInsight(){
        // Get Model
        $visitors = LinkShortenerVisitor::where('link_id', $this->id)->get();

        // Empty array of visits
        $returned = [];


        // Get All Countries
        $countries = [];
        foreach ($visitors as $item) {
            $iso = (string) ao($item->tracking, 'country.iso');
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
            $iso = ao($item->tracking, 'country.iso');
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


        $start_of_year = \Carbon\Carbon::now()->startOfYear()->toDateString();
        $visitors_this_year = LinkShortenerVisitor::where('link_id', $this->id)->where('created_at', '>=', $start_of_year)->get();


        // Get This Year Views
        $thisyear = [];
        foreach ($visitors_this_year as $item) {
            $date = (string) \Carbon\Carbon::parse($item->created_at)->format('M');
            if (!empty($date) && !array_key_exists($date, $thisyear)) {
                $thisyear[$date] = [
                    'visits' => 0,
                    'unique' => 0,
                ];
            }

            if (array_key_exists($date, $thisyear)) {
                $thisyear[$date]['unique']++;
                $thisyear[$date]['visits'] += $item->views;
            }
        }
        $thisyear = get_chart_data($thisyear);

        $returned = ['countries' => $countries, 'cities' => $state, 'devices' => $devices, 'browsers' => $browsers, 'getviews' => $getviews, 'thisyear' => $thisyear];


        return $returned;
	}
}
