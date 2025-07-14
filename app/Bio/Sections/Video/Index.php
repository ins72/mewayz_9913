<?php

namespace App\Bio\Sections\Video;

use Illuminate\Support\ServiceProvider;

class Index extends ServiceProvider{
    public function boot(){

        $this->getConfig();
    }


    public function getConfig(){
        $template = config('bio.sections.video') ?: [];
        $templates = config('bio.sections');
        
        $array = [
            'position' => 1,

            'components' => [
                'editComponent' => 'sections/video/-editComponent',
                'viewComponent' => 'sections/video/viewComponent',
                'alpineView' => 'sections/video/alpineView',
            ],
    
            'function' => [
                'create' => [
                    'content' => [
                        'title' => __('Video'),
                    ],

                    'settings' => [
                        'random' => 'silence is golden'
                    ],

                    'items' => [
                        [
                            'content' => [
                                'type' => 'youtube',
                                'link' => 'https://youtu.be/Qcf8NzbxX2k',
                            ],

                            'settings' => [
                                'random' => 'silence is golden'
                            ]
                        ]
                    ],
                ],
            ],
        ];
        $templates['video'] = array_merge($template, $array);

        config(['bio.sections' => $templates]);
    }

    public function register(){
    }
}