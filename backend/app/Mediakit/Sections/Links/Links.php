<?php

namespace App\Bio\Sections\Links;

use Route;
use Livewire\Livewire;
use Illuminate\Support\ServiceProvider;

class Links extends ServiceProvider{
    public function boot(){

        $this->getConfig();
    }


    public function getConfig(){
        $template = config('bio.sections.links') ?: [];
        $templates = config('bio.sections');
        
        $array = [
            'position' => 1,

            'components' => [
                'editComponent' => 'sections/links/editComponent',
                'viewComponent' => 'sections/links/viewComponent',
                'alpineView' => 'sections/links/alpineView',
            ],
    
            'function' => [
                'create' => [
                    'content' => [
                        'title' => __('Links'),
                    ],

                    'settings' => [
                        'style' => '-'
                    ],

                    'items' => [
                        [
                            'content' => [
                                'image' => null,
                                // 'description' => __('A design wizard voyaging into the metaverse.'),
                                'title' => __('Link 1')
                            ],

                            'settings' => [
                                'random' => 'silence is golden'
                            ]
                        ]
                    ],
                ],
            ],
        ];
        $templates['links'] = array_merge($template, $array);

        config(['bio.sections' => $templates]);
    }

    public function register(){
    }
}