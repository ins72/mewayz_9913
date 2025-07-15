<?php

namespace App\Sections\Accordion;

use Illuminate\Support\ServiceProvider;

class Index extends ServiceProvider{
    public function boot(){

        $this->getConfig();
    }


    public function getConfig(){
        $templates = config('yena.sections');
        
        $templates['accordion'] = [
            'name' => 'Accordion',
            'description' => 'Expandable sections for FAQs',
            'position' => 2,
            'aiClass' => \App\Sections\Accordion\Control\Prompt::class,
    
            'icons' => [
                'showBanner' => 'sections/accordion/icon/-icon',
            ],
    
            'components' => [
                'editComponent' => 'sections/accordion/editComponent',
                'viewComponent' => 'sections/accordion/viewComponent',
                'alpineView' => 'sections/accordion/alpineView',
            ],
    

            'function' => [
                'create' => [
                    'content' => [
                        'title' => __('Heading'),
                        'subtitle' => __('Add a brief description of this section'),
                    ],

                    'settings' => [
                        'banner_style' => 1,
                    ],

                    'form' => [],

                    'items' => [
                        [
                            'content' => [
                                'title' => __('Add title'),
                                'text' => __('Add description'),
                            ],

                            'settings' => [
                                'animation' => '-'
                            ]
                        ]
                    ],
                ],
            ],
        ];

        config(['yena.sections' => $templates]);
    }

    public function register(){
    }
}