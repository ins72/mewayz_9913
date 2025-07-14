<?php

namespace App\Bio\Sections\Booking;

use Illuminate\Support\ServiceProvider;

class Index extends ServiceProvider{
    public function boot(){

        $this->getConfig();
    }


    public function getConfig(){
        $template = config('bio.sections.booking') ?: [];
        $templates = config('bio.sections');
        
        $array = [
            'position' => 1,

            'components' => [
                'editComponent' => 'sections/booking/-editComponent',
                'viewComponent' => 'sections/booking/viewComponent',
                'alpineView' => 'sections/booking/alpineView',
            ],
    
            'function' => [
                'create' => [
                    'content' => [
                        'title' => __('Booking'),
                    ],

                    'settings' => [
                        'style' => '1',
                        'align' => 'left',
                        'layout' => 'left',
                        'display' => 'grid',
                        
                        'desktop_grid' => 1,
                        'mobile_grid' => 1,

                        'text' => 'm',
                        'desktop_height' => '250',
                        'mobile_width' => '300',
                        'desktop_width' => '350',
                    ],

                    'items' => [],
                ],
            ],
        ];
        $templates['booking'] = array_merge($template, $array);

        config(['bio.sections' => $templates]);
    }

    public function register(){
    }
}