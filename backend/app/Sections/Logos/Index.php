<?php

namespace App\Sections\Logos;

use Route;
use Livewire\Livewire;
use Illuminate\Support\ServiceProvider;

class Index extends ServiceProvider{
    public function boot(){

        $this->getConfig();
    }


    public function getConfig(){
        $templates = config('yena.sections');
        
        $templates['logos'] = [
            'name' => 'Logos',
            'description' => 'Logos in grid and carousel',
            'position' => 6,
    
            'icons' => [
                'showBanner' => 'sections/logos/icon/-icon',
            ],
    
            'components' => [
                'editComponent' => 'sections/logos/editComponent',
                'viewComponent' => 'sections/logos/viewComponent',
                'alpineView' => 'sections/logos/alpineView',
            ],
    
            'function' => [
                'create' => [
                    'content' => [
                        'title' => __('Heading'),
                        'subtitle' => __('Add a brief description of this section'),
                    ],

                    'settings' => [
                        'align' => 'left',
                        'display' => 'grid',
                        'desktop_grid' => 4,
                        'mobile_grid' => 3,
                        'desktop_height' => 50,
                        'mobile_height' => 100,
            
                        'desktop_width' => 100,
                        'mobile_width' => 200,
                        'background' => true,
                        'speed' => 28.4,
                    ],

                    'form' => [],

                    'items' => [
                        [
                            'content' => [
                                'image' => null,
                                'link' => '',
                                'desktop_size' => 1,
                                'mobile_size' => 1,
                            ],

                            'settings' => [
                                'animation' => '-'
                            ]
                        ],
                        [
                            'content' => [
                                'image' => null,
                                'link' => '',
                                'desktop_size' => 1,
                                'mobile_size' => 1,
                            ],

                            'settings' => [
                                'animation' => '-'
                            ]
                        ],
                        [
                            'content' => [
                                'image' => null,
                                'link' => '',
                                'desktop_size' => 1,
                                'mobile_size' => 1,
                            ],

                            'settings' => [
                                'animation' => '-'
                            ]
                        ],
                        [
                            'content' => [
                                'image' => null,
                                'link' => '',
                                'desktop_size' => 1,
                                'mobile_size' => 1,
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