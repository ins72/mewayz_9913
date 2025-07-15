<?php

namespace App\Bio\Sections\Course;

use Illuminate\Support\ServiceProvider;

class Index extends ServiceProvider{
    public function boot(){

        $this->getConfig();
    }


    public function getConfig(){
        $template = config('bio.sections.course') ?: [];
        $templates = config('bio.sections');
        
        $array = [
            'position' => 1,

            'components' => [
                'editComponent' => 'sections/course/-editComponent',
                'viewComponent' => 'sections/course/viewComponent',
                'alpineView' => 'sections/course/alpineView',
            ],
    
            'function' => [
                'create' => [
                    'content' => [
                        'title' => __('Course'),
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
                        'style' => 'bn-1'
                    ],

                    'items' => [],
                ],
            ],
        ];
        $templates['course'] = array_merge($template, $array);

        config(['bio.sections' => $templates]);
    }

    public function register(){
    }
}