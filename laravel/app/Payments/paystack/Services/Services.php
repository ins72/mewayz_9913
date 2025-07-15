<?php

namespace App\Payments\paystack\Services;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Route;

class Services extends ServiceProvider{

    public function boot(){
    }

    public function register(){

        $config = config('yena.gateway');

        $config['paystack'] = [  
            'requestClass' => \App\Payments\paystack\Controllers\PaystackController::class,
            'requestFunction' => 'request',
            'cancelFunction' => 'cancel',
        ];

        config(['yena.gateway' => $config]);
    }
}