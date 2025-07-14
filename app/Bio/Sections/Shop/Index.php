<?php

namespace App\Bio\Sections\Shop;

use Illuminate\Support\ServiceProvider;

class Index extends ServiceProvider{
    public function boot(){

        $this->getConfig();
    }


    public function getConfig(){
        $template = config('bio.sections.shop') ?: [];
        $templates = config('bio.sections');
        
        $array = [
            'position' => 1,

            'components' => [
                'editComponent' => 'sections/shop/-editComponent',
                'viewComponent' => 'sections/shop/viewComponent',
                'alpineView' => 'sections/shop/alpineView',
            ],
    
            'function' => [
                'create' => [
                    'content' => [
                        'title' => __('Shop'),
                    ],

                    'settings' => [
                        'style' => '1',
                        'align' => 'left',
                        'layout' => 'left',
                        'display' => 'grid',
                        
                        'desktop_grid' => 1,
                        'mobile_grid' => 1,

                        'text' => 'm',
                        'desktop_height' => '410',
                        'mobile_width' => '300',
                        'desktop_width' => '350',
                        'style' => 'bn-1'
                    ],

                    'items' => [],
                ],
            ],
        ];
        $templates['shop'] = array_merge($template, $array);

        config(['bio.sections' => $templates]);
    }

    public function register(){
    }
}