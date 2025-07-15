<?php

namespace App\Bio\Addons\Qrcode;

use Route;
use Livewire\Livewire;
use Illuminate\Support\ServiceProvider;

class Index extends ServiceProvider{
    public function boot(){

        $this->getConfig();
    }


    public function getConfig(){
        $templates = config('bio.addons');
        
        $templates['qrcode'] = [
            'name' => 'Qrcode',
            'description' => 'Add a QrCode of any link or page',
            'about' => 'Share add a link to generate a qrCode whereby your visitors can easily scan or download it.',

            'icon' => [
                'svg'       => 'shopping-ecommerce---Qr code',
                'color'     => '#ff6398',
            ],

            'components' => [
                // 'databaseView' => 'databaseView',
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