<?php

namespace App\Sections\Review;

use Illuminate\Support\ServiceProvider;

class Index extends ServiceProvider{
    public function boot(){

        $this->getConfig();
    }


    public function getConfig(){
        $templates = config('yena.sections');
        
        $templates['review'] = [
            'name' => 'Review',
            'description' => 'Display your customer reviews and trust',
            'position' => 8,
    
            'icons' => [
                'showBanner' => 'sections/review/icon/-icon',
            ],
    
            'components' => [
                'editComponent' => 'sections/review/editComponent',
                'alpineView' => 'sections/review/alpineView',
            ],
    
            'function' => [
                'create' => [
                    'content' => [
                        'title' => __('Heading'),
                    ],

                    'settings' => [
                        'style' => '1',
                        'align' => 'left',

                        'desktop_grid' => 3,
                        'mobile_grid' => 1,

                        'text' => 's',
                        'background' => true,
                        'rating' => true,
                        'avatar' => true,
                        'type' => 'stars',
                        'shape' => 'square',
                        'desktop_width' => 250,
                        'mobile_width' => 250,
                        'display' => 'grid',
                        'speed' => 28.4,
                    ],

                    'form' => [],

                    'items' => [
                        [
                            'content' => [
                                'title' => __('Johnny Doe'),
                                'bio' => __('@johnnydoe'),
                                'text' => __('Add a customer review that describes their experience with your product/service'),
                                'rating' => 2,
                                'image' => null,
                            ],

                            'settings' => [
                                'animation' => '-'
                            ]
                        ],
                        [
                            'content' => [
                                'title' => __('Johnny Doe'),
                                'bio' => __('@johnnydoe'),
                                'text' => __('Add a customer review that describes their experience with your product/service'),
                                'rating' => 2,
                                'image' => null,
                            ],

                            'settings' => [
                                'animation' => '-'
                            ]
                        ],
                        [
                            'content' => [
                                'title' => __('Johnny Doe'),
                                'bio' => __('@johnnydoe'),
                                'text' => __('Add a customer review that describes their experience with your product/service'),
                                'rating' => 2,
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