<?php

namespace App\Bio\Sections\Youtube;

use Illuminate\Support\ServiceProvider;

class Index extends ServiceProvider{
    public function boot(){

        $this->getConfig();
    }


    public function getConfig(){
        $template = config('bio.sections.youtube') ?: [];
        $templates = config('bio.sections');
        
        $array = [
            'position' => 1,

            'components' => [
                'editComponent' => 'sections/youtube/-editComponent',
                'viewComponent' => 'sections/youtube/viewComponent',
                'alpineView' => 'sections/youtube/alpineView',
            ],
    
            'function' => [
                'create' => [
                    'content' => [
                        'title' => __('Youtube'),
                    ],

                    'settings' => [
                        'random' => 'silence is golden'
                    ],

                    'items' => [
                        [
                            'content' => [
                                'link' => 'https://www.youtube.com/watch?v=yci475Vwc10',
                            ],

                            'settings' => [
                                'random' => 'silence is golden'
                            ]
                        ]
                    ],
                ],
            ],
        ];
        $templates['youtube'] = array_merge($template, $array);

        config(['bio.sections' => $templates]);
    }

    public function register(){
    }
}