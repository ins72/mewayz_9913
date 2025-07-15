<?php

namespace App\Bio\Sections\Slider;

use Illuminate\Support\ServiceProvider;

class Index extends ServiceProvider{
    public function boot(){

        $this->getConfig();
    }


    public function getConfig(){
        $template = config('bio.sections.slider') ?: [];
        $templates = config('bio.sections');
        
        $array = [
            'position' => 1,

            'components' => [
                'editComponent' => 'sections/slider/-editComponent',
                'alpineView' => 'sections/slider/alpineView',
            ],
    
            'function' => [
                'create' => [
                    'content' => [
                        'title' => __('Slider'),
                    ],

                    'settings' => [
                        'desktop_grid' => 4,
                        'mobile_grid' => 3,
                        'desktop_height' => 250,
                        'mobile_height' => 250,
                        'desktop_width' => 250,
                        'mobile_width' => 250,
                        'display' => 'carousel',
                        'speed' => 28.4,
                    ],

                    'items' => [
                        [
                            'content' => [
                                'image' => null,
                            ],

                            'settings' => [
                                'animation' => '-'
                            ]
                        ]
                    ],
                ],
            ],
        ];
        $templates['slider'] = array_merge($template, $array);

        config(['bio.sections' => $templates]);
    }

    public function register(){
    }
}