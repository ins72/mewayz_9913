<?php

namespace App\Bio\Addons\Skillbar;

use Route;
use Livewire\Livewire;
use Illuminate\Support\ServiceProvider;

class Index extends ServiceProvider{
    public function boot(){

        $this->getConfig();
    }


    public function getConfig(){
        $templates = config('bio.addons');
        
        $templates['skillbar'] = [
            'name' => 'Skill Bar',
            'description' => 'Let your visitors know your skillset',
            'about' => 'Show your visitors what you can do by outlining what skill(s) you\'re good at with a percentage.<br /><br />List the number of skill you possess and add the skill level from 1-100 to better show your visitors on what you can do.',

            'icon' => [
                'svg'       => 'Business, Products---Business, Chart.5',
                'color'     => '#8bbaf4',
            ],

            'components' => [
                'editComponent' => 'editAddon',
                'alpineView' => 'addonView',
            ],

            'gallery' => [
                "tia5837682070736802297_0o.png",
                "tia341477437355250893_ba.png",
            ],
        ];

        config(['bio.addons' => $templates]);
    }

    public function register(){
    }
}