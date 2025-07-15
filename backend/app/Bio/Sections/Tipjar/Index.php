<?php

namespace App\Bio\Sections\Tipjar;

use Illuminate\Support\ServiceProvider;

class Index extends ServiceProvider{
    public function boot(){

        $this->getConfig();
    }


    public function getConfig(){
        $template = config('bio.sections.tipjar') ?: [];
        $templates = config('bio.sections');
        
        $array = [
            'position' => 1,

            'components' => [
                'editComponent' => 'sections/tipjar/editComponent',
                'viewComponent' => 'sections/tipjar/viewComponent',
                'alpineView' => 'sections/tipjar/alpineView',
                'alpinePost'    => 'sections/tipjar/post',
            ],
    
            'function' => [
                'create' => [
                    'content' => [
                        'heading' => __('Tipjar'),
                        'text' => 'A design wizard voyaging into the metaverse. I tell the story through my design and illustrations. I spent most of my time designing for brands and creating open-source design resources.',
                        'prices' => [
                            [
                                'name' => 240,
                            ],
                            [
                                'name' => 850,
                            ]
                        ]
                    ],

                    'settings' => [
                        'random' => 'silence is golden',
                        'style' => 'card',
                    ],

                    'items' => [],
                ],
            ],
        ];
        $templates['tipjar'] = array_merge($template, $array);

        config(['bio.sections' => $templates]);
    }

    public function register(){
    }
}