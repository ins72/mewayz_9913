<?php

namespace App\Sections\Gallery;

use Illuminate\Support\ServiceProvider;

class Index extends ServiceProvider{
    public function boot(){

        $this->getConfig();
    }


    public function getConfig(){
        $templates = config('yena.sections');
        
        $templates['gallery'] = [
            'name' => 'Gallery',
            'description' => 'Add images in grids and carousel',
            'position' => 4,
    
            'icons' => [
                'showBanner' => 'sections/gallery/icon/-icon',
            ],
    
            'components' => [
                'editComponent' => 'sections/gallery/editComponent',
                'alpineView' => 'sections/gallery/alpineView',
            ],
    
            'function' => [
                'create' => [
                    'content' => [
                        'title' => __('Heading'),
                        'subtitle' => __('Add a brief description of this section'),
                    ],

                    'settings' => [
                        'desktop_grid' => 4,
                        'mobile_grid' => 3,
                        'desktop_height' => 250,
                        'mobile_height' => 250,
                        'desktop_width' => 250,
                        'mobile_width' => 250,
                        'display' => 'grid',
                        'speed' => 28.4,
                    ],

                    'form' => [],

                    'items' => [
                        [
                            'content' => [
                                'image' => null,
                            ],

                            'settings' => [
                                'animation' => '-'
                            ]
                        ],
                        [
                            'content' => [
                                'image' => null,
                            ],

                            'settings' => [
                                'animation' => '-'
                            ]
                        ],
                        [
                            'content' => [
                                'image' => null,
                            ],

                            'settings' => [
                                'animation' => '-'
                            ]
                        ],
                        [
                            'content' => [
                                'image' => null,
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