<?php

namespace App\Sections\Courses;

use Illuminate\Support\ServiceProvider;

class Index extends ServiceProvider{
    public function boot(){

        $this->getConfig();
    }


    public function getConfig(){
        $templates = config('yena.sections');
        
        $templates['courses'] = [
            'name' => 'Courses',
            'description' => 'Card previews with images & link.',
            'position' => 5,
    
            'icons' => [
                'showBanner' => 'sections/list/icon/-icon',
            ],
    
            'components' => [
                'editComponent' => 'sections/courses/editComponent',
                'alpineView' => 'sections/courses/alpineView',
            ],
    
            'function' => [
                'create' => [
                    'content' => [
                        'title' => __('My Courses'),
                    ],

                    'settings' => [
                        'style' => '1',
                        'align' => 'left',
                        'layout' => 'left',
                        'display' => 'grid',
                        
                        'desktop_grid' => 3,
                        'mobile_grid' => 1,

                        'text' => 'm',
                        'desktop_height' => '250',
                        'mobile_width' => '300',
                        'desktop_width' => '350',
                    ],

                    'form' => [],

                    'items' => [],
                ],
            ],
        ];

        config(['yena.sections' => $templates]);
    }

    public function register(){
    }
}