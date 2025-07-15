<?php

return [
    /*'banner' => [
        'name' => 'Banner',
        'description' => 'Attract and engage with visitors',

        'icons' => [
            'showBanner' => 'assets/image/sections/hero-light.svg',
        ],

        'components' => [
            'editComponent' => 'sections/banner/editComponent',
            'viewComponent' => 'sections/banner/viewComponent',
        ],

        'functions' => [
            'create' => function($site){
                // Section 
                return $site;
                $section = new \App\Models\Section;
                $section->site_id = $site->id;
                $section->page_id = $site->currentPage()->id;
                $section->save();

                return $section;
            },
        ]
    ]*/
];
