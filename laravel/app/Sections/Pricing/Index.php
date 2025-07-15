<?php

namespace App\Sections\Pricing;

use Illuminate\Support\ServiceProvider;

class Index extends ServiceProvider{
    public function boot(){

        $this->getConfig();
    }


    public function getConfig(){
        $templates = config('yena.sections');
        
        $templates['pricing'] = [
            'name' => 'Pricing',
            'description' => 'Display plans and features',
            'position' => 7,
    
            'icons' => [
                'showBanner' => 'sections/pricing/icon/-icon',
            ],
    
            'components' => [
                'editComponent' => 'sections/pricing/editComponent',
                'alpineView' => 'sections/pricing/alpineView',
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

                        'type' => 'plans',
                        'currency' => 'USD',
                        'type' => 'plans',
                    ],

                    'form' => [],

                    'items' => [
                        [
                            'content' => [
                                'title' => __('Pricing 2'),
                                'button' => __('Get started'),
        
                                'single_price' => '0',
                                'month_price' => '0',
                                'year_price' => '0',
                                'features' => [
                                    [
                                        'name' => __('Feature 1')
                                    ],
                                    [
                                        'name' => __('Feature 2')
                                    ],
                                    [
                                        'name' => __('Feature 3')
                                    ],
                                ],
                            ],

                            'settings' => [
                                'animation' => '-'
                            ]
                        ],
                        [
                            'content' => [
                                'title' => __('Pricing 1'),
                                'button' => __('Get started'),
        
                                'single_price' => '0',
                                'month_price' => '0',
                                'year_price' => '0',
                                'features' => [
                                    [
                                        'name' => __('Feature 1')
                                    ],
                                    [
                                        'name' => __('Feature 2')
                                    ],
                                    [
                                        'name' => __('Feature 3')
                                    ],
                                ],
                            ],

                            'settings' => [
                                'animation' => '-'
                            ]
                        ],
                        [
                            'content' => [
                                'title' => __('Pricing 3'),
                                'button' => __('Get started'),
        
                                'single_price' => '0',
                                'month_price' => '0',
                                'year_price' => '0',
                                'features' => [
                                    [
                                        'name' => __('Feature 1')
                                    ],
                                    [
                                        'name' => __('Feature 2')
                                    ],
                                    [
                                        'name' => __('Feature 3')
                                    ],
                                ],
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