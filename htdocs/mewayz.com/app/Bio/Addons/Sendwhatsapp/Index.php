<?php

namespace App\Bio\Addons\Sendwhatsapp;

use Route;
use Livewire\Livewire;
use Illuminate\Support\ServiceProvider;

class Index extends ServiceProvider{
    public function boot(){

        $this->getConfig();
    }


    public function getConfig(){
        $templates = config('bio.addons');
        
        $templates['sendwhatsapp'] = [
            'name' => 'Send to Whatsapp',
            'description' => 'Let people to send you a message to your whatsapp',
            'about' => 'Allow people or visitors to send you a message to your WhatsApp while keeping your contact hidden. <br /><br />You can also check if you want people to send you a message once to prevent spamming.<br /><br />Usage :&nbsp;<br />\r\n<ol>\r\n<li>Add your caption</li>\r\n<li>Add your WhatsApp phone number with country code</li>\r\n<li>Check if you want user to submit only once</li>\r\n</ol>',

            'icon' => [
                'svg'       => 'Social Media---Whatsup',
                'color'     => '#227e22',
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