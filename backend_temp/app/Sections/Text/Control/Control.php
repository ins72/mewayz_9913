<?php

namespace App\Sections\Accordion\Control;

class Control{
    public $site;

    public function setSite($site){
        $this->site = $site;

        return $this;
    }

    public function createSection(){
        $content = [
            'title' => 'Heading',
            'subtitle' => 'Add a brief description of this section'
        ];
        $settings = [
            'banner_style' => 1
        ];
        $form = [

        ];


        $section = new \App\Models\Section;
        $section->site_id = $this->site->id;
        $section->section = 'accordion';
        $section->content = $content;
        $section->settings = $settings;
        $section->form = $form;
        $section->page_id = $this->site->currentPage()->id;
        $section->save();

        return $section;
    }
}