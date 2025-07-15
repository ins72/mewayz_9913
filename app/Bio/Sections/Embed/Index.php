<?php

namespace App\Bio\Sections\Embed;

use Illuminate\Support\ServiceProvider;

class Index extends ServiceProvider{
    public function boot(){

        $this->getConfig();
    }


    public function getConfig(){
        $template = config('bio.sections.embed') ?: [];
        $templates = config('bio.sections');
        
        $array = [
            'position' => 1,

            'components' => [
                'editComponent' => 'sections/embed/-editComponent',
                'viewComponent' => 'sections/embed/viewComponent',
                'alpineView' => 'sections/embed/alpineView',
            ],
    
            'function' => [
                'create' => [
                    'content' => [
                        'title' => __('Embed'),
                    ],

                    'settings' => [
                        'random' => 'silence is golden'
                    ],

                    'items' => [
                        [
                            'content' => [
                                'link' => 'https://www.youtube.com',
                            ],

                            'settings' => [
                                'random' => 'silence is golden'
                            ]
                        ]
                    ],
                ],
            ],
        ];
        $templates['embed'] = array_merge($template, $array);

        config(['bio.sections' => $templates]);
    }

    public function register(){
    }
}