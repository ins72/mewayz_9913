<?php

namespace App\Bio\Sections\Twitch;

use Illuminate\Support\ServiceProvider;

class Index extends ServiceProvider{
    public function boot(){

        $this->getConfig();
    }


    public function getConfig(){
        $template = config('bio.sections.twitch') ?: [];
        $templates = config('bio.sections');
        
        $array = [
            'position' => 1,

            'components' => [
                'editComponent' => 'sections/twitch/-editComponent',
                'viewComponent' => 'sections/twitch/viewComponent',
                'alpineView' => 'sections/twitch/alpineView',
            ],
    
            'function' => [
                'create' => [
                    'content' => [
                        'title' => __('Twitch'),
                    ],

                    'settings' => [
                        'random' => 'silence is golden'
                    ],

                    'items' => [
                        [
                            'content' => [
                                'link' => 'https://www.twitch.tv/kaicenat',
                            ],

                            'settings' => [
                                'random' => 'silence is golden'
                            ]
                        ]
                    ],
                ],
            ],
        ];
        $templates['twitch'] = array_merge($template, $array);

        config(['bio.sections' => $templates]);
    }

    public function register(){
    }
}