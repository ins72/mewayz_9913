<?php

namespace App\Sections\Banner;

use Route;
use Livewire\Livewire;
use Illuminate\Support\ServiceProvider;

class Banner extends ServiceProvider{
    public function boot(){
        $this->getConfig();
    }


    public function getConfig(){
        $templates = config('yena.sections');
        
        $templates['banner'] = [
            'name' => 'Banner',
            'description' => 'Section with image, text and forms',
            'position' => 1,
            'aiClass' => \App\Sections\Banner\Control\Prompt::class,
            'ai' => \App\Sections\Banner\Control\Ai::class,
    
            'icons' => [
                'showBanner' => 'sections/banner/icon/-icon',
            ],
    
            'components' => [
                'editComponent' => 'sections/banner/editComponent',
                'viewComponent' => 'sections/banner/viewComponent',
                'alpineView' => 'sections/banner/alpineView',
            ],

            'function' => [
                'create' => [
                    'content' => [
                        'title' => __('Heading'),
                        'subtitle' => __('Add a brief description of this section'),
                    ],

                    'settings' => [
                        "banner_style" => "3", 
                        "shape_avatar" => 100, 
                        "enable_image" => true, 
                        "actiontype" => "button", 
                        'align' => "left",
                        "height" => "320",
                        'width' => '75',
                        'title' => 's',
                        'image_type' => 'fill', 
                        'enable_action' => true,

                        'button_one_text' => __('Button 1'),
                        'button_two_text' => __('Button 2'),
                    ],

                    'form' => [
                        'email' => 'Email',
                        'button_name' => 'Signup',
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