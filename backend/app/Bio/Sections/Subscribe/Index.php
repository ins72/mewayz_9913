<?php

namespace App\Bio\Sections\Subscribe;

use Illuminate\Support\ServiceProvider;

class Index extends ServiceProvider{
    public function boot(){

        $this->getConfig();
    }


    public function getConfig(){
        $template = config('bio.sections.subscribe') ?: [];
        $templates = config('bio.sections');
        
        $array = [
            'position' => 1,

            'components' => [
                'editComponent' => 'sections/subscribe/editComponent',
                'viewComponent' => 'sections/subscribe/viewComponent',
                'alpineView'    => 'sections/subscribe/alpineView',
                'alpinePost'    => 'sections/sendwhatsapp/post',
            ],
    
            'function' => [
                'create' => [
                    'content' => [
                        'heading' => __('Subscribe'),
                        'text' => 'A design wizard voyaging into the metaverse. I tell the story through my design and illustrations. I spent most of my time designing for brands and creating open-source design resources.',
                    ],

                    'settings' => [
                        'random' => 'silence is golden',
                        'style' => 'card',
                    ],

                    'items' => [],
                ],
            ],
        ];
        $templates['subscribe'] = array_merge($template, $array);

        config(['bio.sections' => $templates]);
    }

    public function register(){
    }
}