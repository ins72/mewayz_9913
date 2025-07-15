<?php

namespace App\Bio\Addons\Contact;

use Route;
use Livewire\Livewire;
use Illuminate\Support\ServiceProvider;

class Index extends ServiceProvider{
    public function boot(){

        $this->getConfig();
    }


    public function getConfig(){
        $templates = config('bio.addons');
        
        $templates['contact'] = [
            'name' => 'Contact Me',
            'description' => 'Allow your visitors send you a message to your mail',
            'about' => 'This element has been built for your visitors to send you a message directly to your mail. <br /><br />It\'s as easy as adding your description on what type of message your visitor can send.<br /><br /><em>Ps: this uses captcha to avoid spamming.&nbsp;</em>',

            'icon' => [
                'svg'       => 'emails---Address book',
                'color'     => '#ffd67e',
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