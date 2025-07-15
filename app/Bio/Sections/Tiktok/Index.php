<?php

namespace App\Bio\Sections\Tiktok;

use Illuminate\Support\ServiceProvider;

class Index extends ServiceProvider{
    public function boot(){

        $this->getConfig();
    }


    public function getConfig(){
        $template = config('bio.sections.tiktok') ?: [];
        $templates = config('bio.sections');
        
        $array = [
            'position' => 1,

            'components' => [
                'editComponent' => 'sections/tiktok/-editComponent',
                'viewComponent' => 'sections/tiktok/viewComponent',
                'alpineView' => 'sections/tiktok/alpineView',
            ],
    
            'function' => [
                'create' => [
                    'content' => [
                        'title' => __('Tiktok'),
                    ],

                    'settings' => [
                        'random' => 'silence is golden'
                    ],

                    'items' => [
                        [
                            'content' => [
                                'link' => 'https://www.tiktok.com/@andsea_miyakoisland/video/7358077798319131912',
                            ],

                            'settings' => [
                                'random' => 'silence is golden'
                            ]
                        ]
                    ],
                ],
            ],
        ];
        $templates['tiktok'] = array_merge($template, $array);

        config(['bio.sections' => $templates]);
    }

    public function register(){
    }
}