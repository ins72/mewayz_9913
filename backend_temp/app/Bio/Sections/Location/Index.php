<?php

namespace App\Bio\Sections\Location;

use Illuminate\Support\ServiceProvider;

class Index extends ServiceProvider{
    public function boot(){

        $this->getConfig();
    }


    public function getConfig(){
        $template = config('bio.sections.location') ?: [];
        $templates = config('bio.sections');
        
        $array = [
            'position' => 1,

            'components' => [
                'editComponent' => 'sections/location/editComponent',
                'viewComponent' => 'sections/location/viewComponent',
                'alpineView' => 'sections/location/alpineView',
            ],
    
            'function' => [
                'create' => [
                    'content' => [
                        'title' => __('Location'),
                    ],

                    'settings' => [
                        'random' => 'silence is golden'
                    ],

                    'items' => [
                        [
                            'content' => [
                                'location' => 'Alabama, Usa',
                            ],

                            'settings' => [
                                'random' => 'silence is golden'
                            ]
                        ]
                    ],
                ],
            ],
        ];
        $templates['location'] = array_merge($template, $array);

        config(['bio.sections' => $templates]);
    }

    public function register(){
    }
}