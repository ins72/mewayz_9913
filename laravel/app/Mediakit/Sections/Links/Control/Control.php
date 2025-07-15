<?php

namespace App\Bio\Sections\Links\Control;

class Control{
    public $site;

    public function setSite($site){
        $this->site = $site;

        return $this;
    }

    public function createSection(){
        $content = [
            'title' => 'Heading',
            'subtitle' => 'Add a brief description of this section',
            'label' => '',
        ];

        $settings = [
            'banner_style' => 2,
            'shape_avatar' => 100,
            'enable_image' => true,
        ];

        $form = [
            'email' => 'Email',
            'button_name' => 'Signup',
        ];


        $section = new \App\Models\Section;
        $section->site_id = $this->site->id;
        $section->section = 'banner';
        $section->content = $content;
        $section->settings = $settings;
        $section->form = $form;
        $section->page_id = $this->site->currentPage()->id;
        $section->save();

        return $section;
    }
}