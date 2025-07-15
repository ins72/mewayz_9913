<?php

namespace App\Bio\Addons\Annoymous;

use Route;
use Livewire\Livewire;
use Illuminate\Support\ServiceProvider;

class Index extends ServiceProvider{
    public function boot(){

        $this->getConfig();
    }


    public function getConfig(){
        $templates = config('bio.addons');
        
        $templates['annoymous'] = [
            'name' => 'Annoymous Message',
            'description' => 'Let your audence send you an Annoymous Message',
            'about' => 'Give your friends or followers a way to send you a secret message without knowing who.',

            'icon' => [
                'svg'       => 'Romance Wedding---Archery, Love, Marriage',
                'color'     => '#b03969',
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