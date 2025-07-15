<?php

namespace App\Bio\Sections\Contact;

use Illuminate\Support\ServiceProvider;

class Index extends ServiceProvider{
    public function boot(){

        $this->getConfig();
    }


    public function getConfig(){
        $template = config('bio.sections.contact') ?: [];
        $templates = config('bio.sections');
        
        $array = [
            'position' => 1,

            'components' => [
                'editComponent' => 'sections/contact/editComponent',
                'viewComponent' => 'sections/contact/viewComponent',
                'alpineView' => 'sections/contact/alpineView',
            ],
    
            'function' => [
                'create' => [
                    'content' => [
                        'heading' => __('Send me an email!'),
                        'contact_me_button' => __('Send Message'),
                        'show_tnk' => false,
                        'custom_thank_you' => '',
                    ],

                    'settings' => [
                        'random' => 'silence is golden',
                        'style' => 'card',
                    ],

                    'items' => [],
                ],
            ],
        ];
        $templates['contact'] = array_merge($template, $array);

        // config(['bio.sections' => $templates]);
    }

    public function register(){
    }
}