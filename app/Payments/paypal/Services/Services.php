<?php

namespace App\Payments\paypal\Services;
use Illuminate\Support\ServiceProvider;

class Services extends ServiceProvider{

    public function boot(){
    }

    public function register(){
        $config = config('yena.gateway');

        $config['paypal'] = [  
            'requestClass' => \App\Payments\paypal\Controllers\PayPalController::class,
            'requestFunction' => 'request',
            'cancelFunction' => 'cancel',
        ];

        config(['yena.gateway' => $config]);
    }
}