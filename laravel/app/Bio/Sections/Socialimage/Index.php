<?php

namespace App\Bio\Sections\Socialimage;

use Route;
use Livewire\Livewire;
use Illuminate\Support\ServiceProvider;

class Index extends ServiceProvider{
    public function boot(){

        $this->getConfig();
    }


    public function getConfig(){
        $template = config('bio.sections.socialimage') ?: [];
        $templates = config('bio.sections');
        
        $array = [
            'position' => 1,
            'completed' => true,

            'components' => [
                'editComponent' => 'sections/socialimage/editComponent',
                'viewComponent' => 'sections/socialimage/viewComponent',
                'alpineView' => 'sections/socialimage/alpineView',
            ],
    
            'function' => [
                'create' => [
                    'content' => [
                        'title' => __('Social Image'),
                    ],

                    'items' => [
                        // [
                        //     'content' => [
                        //         'image' => null,
                        //         'link' => '',
                        //         'title' => 'New Image'
                        //     ],

                        //     'settings' => [
                        //         'animation' => '-'
                        //     ]
                        // ]
                    ],
                ],
            ],
        ];
        $templates['socialimage'] = array_merge($template, $array);

        config(['bio.sections' => $templates]);
    }

    public function register(){
    }
}