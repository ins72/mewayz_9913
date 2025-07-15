<?php

namespace App\Bio\Sections\Text;

use Illuminate\Support\ServiceProvider;

class Index extends ServiceProvider{
    public function boot(){

        $this->getConfig();
    }


    public function getConfig(){
        $template = config('bio.sections.text') ?: [];
        $templates = config('bio.sections');
        
        $array = [
            'position' => 1,

            'components' => [
                'editComponent' => 'sections/text/editComponent',
                'viewComponent' => 'sections/text/viewComponent',
                'alpineView' => 'sections/text/alpineView',
            ],
    
            'function' => [
                'create' => [
                    'content' => [
                        'title' => __('Text'),
                        'text' => 'Click this to edit or any other section.',
                    ],

                    'settings' => [
                        'random' => 'silence is golden'
                    ],

                    'items' => [],
                ],
            ],
        ];
        $templates['text'] = array_merge($template, $array);

        config(['bio.sections' => $templates]);
    }

    public function register(){
    }
}