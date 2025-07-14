<?php

namespace App\Bio\Sections\Features;

use Route;
use Livewire\Livewire;
use Illuminate\Support\ServiceProvider;

class Index extends ServiceProvider{
    public function boot(){

        $this->getConfig();
    }


    public function getConfig(){
        $template = config('bio.sections.features') ?: [];
        $templates = config('bio.sections');
        
        $array = [
            'position' => 1,

            'components' => [
                'editComponent' => 'sections/features/editComponent',
                'viewComponent' => 'sections/features/viewComponent',
                'alpineView' => 'sections/features/alpineView',
            ],
    
            'function' => [
                'create' => [
                    'content' => [
                        'title' => __('Features'),
                    ],

                    'settings' => [
                        'style' => 'default'
                    ],

                    'items' => [
                        [
                            'content' => [
                                'image' => null,
                                'subtitle' => __('A design wizard voyaging into the metaverse.'),
                                'title' => __('My Feature')
                            ],

                            'settings' => [
                                'random' => 'silence is golden'
                            ]
                        ]
                    ],
                ],
            ],
        ];
        $templates['features'] = array_merge($template, $array);

        config(['bio.sections' => $templates]);
    }

    public function register(){
    }
}