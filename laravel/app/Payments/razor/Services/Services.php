<?php

namespace App\Payments\razor\Services;
use Illuminate\Support\ServiceProvider;

class Services extends ServiceProvider{

    public function boot(){
    }

    public function register(){
        $config = config('yena.gateway');

        $config['razor'] = [
            'requestClass' => \App\Payments\razor\Controllers\RazorController::class,
            'requestFunction' => 'request',
            'cancelFunction' => 'cancel',
        ];

        config(['yena.gateway' => $config]);
    }
}