<?php

namespace App\Bio\Sections\Image;

use Route;
use Livewire\Livewire;
use Illuminate\Support\ServiceProvider;

class Index extends ServiceProvider{
    public function boot(){

        $this->getConfig();
    }


    public function getConfig(){
        $template = config('bio.sections.image') ?: [];
        $templates = config('bio.sections');
        
        $array = [
            'position' => 1,

            'components' => [
                'editComponent' => 'sections/image/editComponent',
                'viewComponent' => 'sections/image/viewComponent',
                'alpineView' => 'sections/image/alpineView',
            ],
    
            'function' => [
                'create' => [
                    'content' => [
                        'title' => __('Image'),
                    ],

                    'items' => [
                        [
                            'content' => [
                                'image' => null,
                                'link' => '',
                                'title' => 'New Image'
                            ],

                            'settings' => [
                                'animation' => '-'
                            ]
                        ]
                    ],

                    // 'items' => [
                    //     [
                    //         ''
                    //     ]
                    // ]
                ],
                // 'create' => 'function create($this) {

                //     var $item = {
                //         uuid: $this.$store.builder.generateUUID(),
                //         section: "image",
                //         settings: {
                //             create: true,
                //         },
                //         content: {
                //             title: "Image"
                //         },
                //     };
                //     $this.sections.push($item);

                //     $this.$wire.createSection($item);
                // }'
            ],
        ];
        $templates['image'] = array_merge($template, $array);

        config(['bio.sections' => $templates]);
    }

    public function register(){
    }
}