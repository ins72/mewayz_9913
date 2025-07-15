<?php

namespace App\Payments\stripe\Services;
use Illuminate\Support\ServiceProvider;

class Services extends ServiceProvider{
    public function boot(){
    }

    public function register(){
        $config = config('yena.gateway');

        $config['stripe'] = [  
            'requestClass' => \App\Payments\stripe\Controllers\StripeController::class,
            'requestFunction' => 'request',
            'cancelFunction' => 'cancel',
        ];

        config(['yena.gateway' => $config]);
    }



    public function config(){

    }
}