<?php

namespace App\Models;

use App\Models\Base\SitesVisitor as BaseSitesVisitor;

class SitesVisitor extends BaseSitesVisitor
{
	protected $fillable = [
		'site_id',
		'slug',
		'session',
		'ip',
		'tracking',
		'views'
	];
	
	protected $casts = [
		'tracking' => 'array'
	];

    // public function scopeTopUsers(Builder $query){
    //     // Get Model
    //     $visitors = SitesVisitor::get();

    //     // Empty array of visits
    //     $returned = [];

    //     // Loop Visitors
    //     foreach ($visitors as $item) {
    //         $id = $item->user;
    //         $id = user('username', $id);
    //         $id = (string) $id;
    //         if (!empty($id) && !array_key_exists($id, $returned)) {
    //             $returned[$id] = [
    //                 'visits' => 0,
    //                 'unique' => 0,
    //                 'user' => $item->user
    //             ];
    //         }

    //         if (array_key_exists($id, $returned)) {
    //             $returned[$id]['unique']++;
    //             $returned[$id]['visits'] += $item->views;
    //         }
    //     }

    //     usort($returned, function ($a, $b) {
    //         return $a['visits'] - $b['visits'];
    //     });
    //     $returned = array_reverse($returned);

    //     return $returned;
    // }
}
