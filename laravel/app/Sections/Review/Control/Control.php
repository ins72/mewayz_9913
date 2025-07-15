<?php

namespace App\Sections\Logos\Control;

use App\Models\Section;
use App\Models\SectionItem;

class Control{
    public $site;

    public function setSite($site){
        $this->site = $site;

        return $this;
    }

    public function createSection(){
        $content = [
            'title' => 'Heading',
        ];
        $settings = [
            'align' => 'left',
            'display' => 'grid',
            'desktop_grid' => 4,
            'mobile_grid' => 3,
            'desktop_height' => 50,
            'mobile_height' => 100,

            'desktop_width' => 100,
            'mobile_width' => 200,
            'background' => true,
        ];
        $section_settings = [
            'color' => 'transparent',
            'image' => null,
        ];


        $section = new Section;
        $section->site_id = $this->site->id;
        $section->section = 'logos';
        $section->content = $content;
        $section->settings = $settings;
        $section->section_settings = $section_settings;
        $section->page_id = $this->site->currentPage()->id;
        $section->save();


        for ($i=0; $i < 4; $i++) { 
            $content = [
                'image' => null,
                'link' => '',
                'desktop_size' => 1,
                'mobile_size' => 1,
            ];
            $_item = new SectionItem;
            $_item->section_id = $section->id;
            $_item->content = $content;
            $_item->save();
        }

        return $section->toArray();
    }
}