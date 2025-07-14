<?php

namespace App\Payments\click\Services;
use Illuminate\Support\ServiceProvider;

class Services extends ServiceProvider{
    public function boot(){
    }

    public function register(){
        $config = config('yena.gateway');

        $config['click'] = [  
            'requestClass' => \App\Payments\click\Controllers\ClickController::class,
            'requestFunction' => 'request',
            'cancelFunction' => 'cancel',
        ];

        config(['yena.gateway' => $config]);
    }



    public function config(){

    }
}