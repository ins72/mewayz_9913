<?php

namespace App\Bio\Addons\Email;

use Route;
use Livewire\Livewire;
use Illuminate\Support\ServiceProvider;

class Index extends ServiceProvider{
    public function boot(){

        $this->getConfig();
    }


    public function getConfig(){
        $templates = config('bio.addons');
        
        $templates['email'] = [
            'name' => 'Email Collection',
            'description' => 'Create & collect your audience email',
            'about' => 'Build up your email list by collecting your visitors email.<br /><br />Emails can be exported to .<strong>csv</strong> if you want to use them in mailing platform like mailchimp.',

            'icon' => [
                'svg'       => 'interface-essential---@-email-mail.1',
                'color'     => '#ffd67e',
            ],

            'components' => [
                'databaseView' => 'databaseView',
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