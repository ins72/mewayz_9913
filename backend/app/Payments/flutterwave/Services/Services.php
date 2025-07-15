<?php

namespace App\Payments\flutterwave\Services;
use Illuminate\Support\ServiceProvider;

class Services extends ServiceProvider{

    public function boot(){
    }

    public function register(){

        $config = config('yena.gateway');

        $config['flutterwave'] = [  
            'requestClass' => \App\Payments\flutterwave\Controllers\FlutterwaveController::class,
            'requestFunction' => 'request',
            'cancelFunction' => 'cancel',
        ];

        config(['yena.gateway' => $config]);
    }
}