<?php

namespace App\Sections\List;

use Illuminate\Support\ServiceProvider;

class Index extends ServiceProvider{
    public function boot(){

        $this->getConfig();
    }


    public function getConfig(){
        $templates = config('yena.sections');
        
        $templates['list'] = [
            'name' => 'List',
            'description' => 'Card previews with images & link.',
            'position' => 5,
    
            'icons' => [
                'showBanner' => 'sections/list/icon/-icon',
            ],
    
            'components' => [
                'editComponent' => 'sections/list/editComponent',
                'alpineView' => 'sections/list/alpineView',
            ],
    
            'function' => [
                'create' => [
                    'content' => [
                        'title' => __('Heading'),
                    ],

                    'settings' => [
                        'style' => '1',
                        'align' => 'left',
                        'layout' => 'left',
                        'display' => 'grid',
                        
                        'desktop_grid' => 3,
                        'mobile_grid' => 1,

                        'text' => 'm',
                        'background' => true,
                        'icon' => true,
                        'type' => 'stars',
                        'shape' => 'square',
                        'desktop_height' => '50',
                    ],

                    'form' => [],

                    'items' => [
                        [
                            'content' => [
                                'title' => __('List 1'),
                            ],

                            'settings' => [
                                'animation' => '-'
                            ]
                        ],
                        [
                            'content' => [
                                'title' => __('List 2'),
                            ],

                            'settings' => [
                                'animation' => '-'
                            ]
                        ],
                        [
                            'content' => [
                                'title' => __('List 3'),
                            ],

                            'settings' => [
                                'animation' => '-'
                            ]
                        ],
                        [
                            'content' => [
                                'title' => __('List 4'),
                            ],

                            'settings' => [
                                'animation' => '-'
                            ]
                        ],
                        [
                            'content' => [
                                'title' => __('List 5'),
                            ],

                            'settings' => [
                                'animation' => '-'
                            ]
                        ],
                        [
                            'content' => [
                                'title' => __('List 6'),
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