<?php

namespace App\Bio\Addons\Textarea;

use Route;
use Livewire\Livewire;
use Illuminate\Support\ServiceProvider;

class Index extends ServiceProvider{
    public function boot(){

        $this->getConfig();
    }


    public function getConfig(){
        $templates = config('bio.addons');
        
        $templates['textarea'] = [
            'name' => 'Textarea',
            'description' => 'A simple textarea you can add to your page',
            'about' => 'Simply write texts to be visible publicly &amp; read by whoever visits the page.',

            'icon' => [
                'svg'       => 'Content Edit---notes-book-pen',
                'color'     => '#009fff',
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