<?php

namespace App\Bio\Sections\Skillbar;

use Route;
use Livewire\Livewire;
use Illuminate\Support\ServiceProvider;

class Index extends ServiceProvider{
    public function boot(){

        $this->getConfig();
    }


    public function getConfig(){
        $template = config('bio.sections.skillbar') ?: [];
        $templates = config('bio.sections');
        
        $array = [
            'position' => 1,

            'components' => [
                'editComponent' => 'sections/skillbar/editComponent',
                'viewComponent' => 'sections/skillbar/viewComponent',
                'alpineView' => 'sections/skillbar/alpineView',
            ],
    
            'function' => [
                'create' => [
                    'content' => [
                        'title' => __('Skillbar'),
                    ],

                    'items' => [
                        [
                            'content' => [
                                'skillbar' => 20,
                                'title' => 'Javascript'
                            ],

                            'settings' => [
                                'random' => 'silence is golden'
                            ]
                        ]
                    ],
                ],
            ],
        ];
        $templates['skillbar'] = array_merge($template, $array);

        config(['bio.sections' => $templates]);
    }

    public function register(){
    }
}