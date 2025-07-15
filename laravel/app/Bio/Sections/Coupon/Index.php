<?php

namespace App\Bio\Sections\Coupon;

use Route;
use Livewire\Livewire;
use Illuminate\Support\ServiceProvider;

class Index extends ServiceProvider{
    public function boot(){

        $this->getConfig();
    }


    public function getConfig(){
        $template = config('bio.sections.coupon') ?: [];
        $templates = config('bio.sections');
        
        $array = [
            'position' => 1,

            'components' => [
                'editComponent' => 'sections/coupon/editComponent',
                'viewComponent' => 'sections/coupon/viewComponent',
                'alpineView' => 'sections/coupon/alpineView',
            ],
    
            'function' => [
                'create' => [
                    'content' => [
                        'title' => __('Check out this coupon'),
                        'coupon_title' => __('45% Off All Bath Product'),
                        'coupon_desc' => __('All my recommendations in one place. Enjoy, love you all!'),
                        'coupon_code' => __('BATHSALES'),
                        'show_coupon_button' => true,
                        'link_button_link' => config('app.url'),
                        'link_button_text' => __('Shop'),
                        'image' => null,
                    ],

                    'settings' => [
                        'style' => 'card'
                    ],

                    'items' => [],
                ],
            ],
        ];
        $templates['coupon'] = array_merge($template, $array);

        config(['bio.sections' => $templates]);
    }

    public function register(){
    }
}