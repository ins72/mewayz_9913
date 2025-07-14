<?php

namespace App\Bio\Sections\BankCrypto;

use Illuminate\Support\ServiceProvider;

class Index extends ServiceProvider{
    public function boot(){

        $this->getConfig();
    }


    public function getConfig(){
        $template = config('bio.sections.bankcrypto') ?: [];
        $templates = config('bio.sections');
        
        $array = [
            'position' => 1,

            'components' => [
                'editComponent' => 'sections/bankcrypto/editComponent',
                'viewComponent' => 'sections/bankcrypto/viewComponent',
                'alpineView' => 'sections/bankcrypto/alpineView',
            ],
    
            'function' => [
                'create' => [
                    'content' => [
                        'title' => __('Bank & Crypto'),
                    ],

                    'settings' => [
                        'random' => 'silence is golden'
                    ],

                    'items' => [
                        [
                            'content' => [
                                'image' => null,
                                'wallet' => __('xxxxxxxxxxxxx'),
                                'title' => __('USDT')
                            ],

                            'settings' => [
                                'random' => 'silence is golden'
                            ]
                        ]
                    ],
                ],
            ],
        ];
        $templates['bankcrypto'] = array_merge($template, $array);

        config(['bio.sections' => $templates]);
    }

    public function register(){
    }
}