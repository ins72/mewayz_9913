<?php

namespace App\Bio\Sections\Twitter;

use Illuminate\Support\ServiceProvider;

class Index extends ServiceProvider{
    public function boot(){

        $this->getConfig();
    }


    public function getConfig(){
        $template = config('bio.sections.twitter') ?: [];
        $templates = config('bio.sections');
        
        $array = [
            'position' => 1,

            'components' => [
                'editComponent' => 'sections/twitter/-editComponent',
                'viewComponent' => 'sections/twitter/viewComponent',
                'alpineView' => 'sections/twitter/alpineView',
            ],
    
            'function' => [
                'create' => [
                    'content' => [
                        'title' => __('Twitter'),
                    ],

                    'settings' => [
                        'random' => 'silence is golden'
                    ],

                    'items' => [
                        [
                            'content' => [
                                'link' => 'https://twitter.com/elonmusk',
                            ],

                            'settings' => [
                                'random' => 'silence is golden'
                            ]
                        ]
                    ],
                ],
            ],
        ];
        // $templates['twitter'] = array_merge($template, $array);

        // config(['bio.sections' => $templates]);
    }

    public function register(){
    }
}