<?php

namespace App\Bio\Sections\Timer;

use Illuminate\Support\ServiceProvider;

class Index extends ServiceProvider{
    public function boot(){

        $this->getConfig();
    }


    public function getConfig(){
        $template = config('bio.sections.timer') ?: [];
        $templates = config('bio.sections');
        
        $array = [
            'position' => 1,

            'components' => [
                'editComponent' => 'sections/timer/editComponent',
                'viewComponent' => 'sections/timer/viewComponent',
                'alpineView' => 'sections/timer/alpineView',
            ],
    
            'function' => [
                'create' => [
                    'content' => [
                        'heading' => __('Timer'),
                        'date' => '',
                    ],

                    'settings' => [
                        'random' => 'silence is golden',
                        'style' => 'popup',
                    ],

                    'items' => [],
                ],
            ],
        ];
        $templates['timer'] = array_merge($template, $array);

        config(['bio.sections' => $templates]);
    }

    public function register(){
    }
}