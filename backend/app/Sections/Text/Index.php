<?php

namespace App\Sections\Text;

use Route;
use Livewire\Livewire;
use Illuminate\Support\ServiceProvider;

class Index extends ServiceProvider{
    public function boot(){

        $this->getConfig();
    }


    public function getConfig(){
        $templates = config('yena.sections');
        
        $templates['text'] = [
            'name' => 'Text',
            'description' => 'Write text content',
            'position' => 9,
    
            'icons' => [
                'showBanner' => 'sections/text/icon/-icon',
            ],
    
            'components' => [
                'editComponent' => 'sections/text/editComponent',
                'alpineView' => 'sections/text/alpineView',
            ],
    

            'function' => [
                'create' => [
                    'content' => [
                        'title' => __('Heading'),
                        'subtitle' => __('Markdown solves this problem in many ways, plus Markdown can be rendered natively on more platforms than HTML.')
                    ],

                    'settings' => [
                        'split' => '1',
                        'align' => 'left',
                    ],

                    'items' => [],
                ],
            ],
        ];

        config(['yena.sections' => $templates]);
    }

    public function register(){
    }
}