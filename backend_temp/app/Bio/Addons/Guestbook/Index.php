<?php

namespace App\Bio\Addons\Guestbook;

use Route;
use Livewire\Livewire;
use Illuminate\Support\ServiceProvider;

class Index extends ServiceProvider{
    public function boot(){

        $this->getConfig();
    }


    public function getConfig(){
        $templates = config('bio.addons');
        
        $templates['guestbook'] = [
            'name' => 'GuestBook',
            'description' => 'Collect texts from your friends or followers',
            'about' => 'Give your friends or followers a way to post a short message &amp; display publicly on a feed while keeping their identity hidden.',

            'icon' => [
                'svg'       => 'Construction, Tools---project-book-house',
                'color'     => '#934343',
            ],

            'components' => [
                'databaseView' => 'databaseView',
                'editComponent' => 'editAddon',
                'alpineView' => 'addonView',
            ],

            'gallery' => [
                "1_mu.png",
                "2_di.png",
                "3_qv.png"
            ],
        ];

        config(['bio.addons' => $templates]);
    }

    public function register(){
    }
}