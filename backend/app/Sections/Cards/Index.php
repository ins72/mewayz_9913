<?php

namespace App\Sections\Cards;

use Illuminate\Support\ServiceProvider;

class Index extends ServiceProvider{
    public function boot(){

        $this->getConfig();
    }


    public function getConfig(){
        $templates = config('yena.sections');
        
        $templates['cards'] = [
            'name' => 'Cards',
            'description' => 'Card previews with images & link.',
            'position' => 3,
    
            'icons' => [
                'showBanner' => 'sections/cards/icon/-icon',
            ],
    
            'components' => [
                'editComponent' => 'sections/cards/editComponent',
                'alpineView' => 'sections/cards/alpineView',
            ],
    
            'function' => [
                'create' => [
                    'content' => [
                        'title' => __('Heading'),
                    ],

                    'settings' => [
                        'style' => '1',
                        'align' => 'left',
                        'layout_align' => 'bottom',
                        'desktop_grid' => 3,
                        'mobile_grid' => 1,

                        'desktop_height' => 250,
                        'mobile_height' => 250,
                        'text' => 's',
                        'background' => true,
                        'enable_image' => true,
                    ],

                    'form' => [],

                    'items' => [
                        [
                            'content' => [
                                'title' => __('Card 1'),
                                'text' => __('Add text here'),
                                'image' => null,
                                'button' => '',
                                'color' => 'accent',
                            ],

                            'settings' => [
                                'animation' => '-'
                            ]
                        ],
                        [
                            'content' => [
                                'title' => __('Card 2'),
                                'text' => __('Add text here'),
                                'image' => null,
                                'button' => '',
                                'color' => 'accent',
                            ],

                            'settings' => [
                                'animation' => '-'
                            ]
                        ],
                        [
                            'content' => [
                                'title' => __('Card 3'),
                                'text' => __('Add text here'),
                                'image' => null,
                                'button' => '',
                                'color' => 'accent',
                            ],

                            'settings' => [
                                'animation' => '-'
                            ]
                        ],
                    ],
                ],
            ],
        ];

        config(['yena.sections' => $templates]);
    }

    public function register(){
    }
}