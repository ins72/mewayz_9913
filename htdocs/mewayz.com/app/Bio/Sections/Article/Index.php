<?php

namespace App\Bio\Sections\Article;

use Illuminate\Support\ServiceProvider;

class Index extends ServiceProvider{
    public function boot(){

        $this->getConfig();
    }


    public function getConfig(){
        $template = config('bio.sections.article') ?: [];
        $templates = config('bio.sections');
        
        $array = [
            'position' => 1,

            'components' => [
                'editComponent' => 'sections/article/editComponent',
                'viewComponent' => 'sections/article/viewComponent',
                'alpineView' => 'sections/article/alpineView',
            ],
    
            'function' => [
                'create' => [
                    'content' => [
                        'heading' => __('Article'),
                        'text' => 'A design wizard voyaging into the metaverse. I tell the story through my design and illustrations. I spent most of my time designing for brands and creating open-source design resources.',
                    ],

                    'settings' => [
                        'random' => 'silence is golden',
                        'style' => 'popup',
                    ],

                    'items' => [],
                ],
            ],
        ];
        $templates['article'] = array_merge($template, $array);

        config(['bio.sections' => $templates]);
    }

    public function register(){
    }
}