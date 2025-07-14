<?php

namespace App\Bio\Sections\SendWhatsapp;

use Illuminate\Support\ServiceProvider;

class Index extends ServiceProvider{
    public function boot(){

        $this->getConfig();
    }


    public function getConfig(){
        $template = config('bio.sections.sendwhatsapp') ?: [];
        $templates = config('bio.sections');
        
        $array = [
            'position' => 1,

            'components' => [
                'editComponent' => 'sections/sendwhatsapp/editComponent',
                'viewComponent' => 'sections/sendwhatsapp/viewComponent',
                'alpineView' => 'sections/sendwhatsapp/alpineView',
                'alpinePost' => 'sections/sendwhatsapp/post',
            ],
    
            'function' => [
                'create' => [
                    'content' => [
                        'heading' => __('Send to Whatsapp'),
                        'send_to_whatsapp_button' => 'Send Message'
                    ],

                    'settings' => [
                        'random' => 'silence is golden',
                        'style' => 'popup',
                    ],

                    'items' => [],
                ],
            ],
        ];
        $templates['sendwhatsapp'] = array_merge($template, $array);

        config(['bio.sections' => $templates]);
    }

    public function register(){
    }
}