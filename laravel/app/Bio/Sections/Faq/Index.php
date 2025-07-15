<?php

namespace App\Bio\Sections\Faq;

use Illuminate\Support\ServiceProvider;

class Index extends ServiceProvider{
    public function boot(){

        $this->getConfig();
    }


    public function getConfig(){
        $template = config('bio.sections.faq') ?: [];
        $templates = config('bio.sections');
        
        $array = [
            'position' => 1,

            'components' => [
                'editComponent' => 'sections/faq/editComponent',
                'viewComponent' => 'sections/faq/viewComponent',
                'alpineView' => 'sections/faq/alpineView',
            ],
    
            'function' => [
                'create' => [
                    'content' => [
                        'title' => __('Faq'),
                    ],

                    'settings' => [
                        'random' => 'silence is golden'
                    ],

                    'items' => [
                        [
                            'content' => [
                                'image' => null,
                                'description' => __('A design wizard voyaging into the metaverse.'),
                                'title' => __('Faq')
                            ],

                            'settings' => [
                                'random' => 'silence is golden'
                            ]
                        ]
                    ],
                ],
            ],
        ];
        $templates['faq'] = array_merge($template, $array);

        config(['bio.sections' => $templates]);
    }

    public function register(){
    }
}