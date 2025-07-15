<?php

namespace App\Livewire\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

trait BroadcastBuilder {
    
    
    public function broadcastSectionContent(){
        $section_id = $this->section->id;
        $event = "updated-banner-content.$section_id";
        
        $this->dispatch($event, $this->section->content);
    }

    public function broadcastFormContent(){
        $section_id = $this->section->id;
        $event = "updated-banner-form.$section_id";
        
        $this->dispatch($event, $this->section->form);
    }

    public function broadcastSectionSettings(){
        $section_id = $this->section->id;
        $event = "updated-banner-settings.$section_id";
        
        $this->dispatch($event, $this->section->settings);
    }


    public function broadcastSection(){
        $section_id = $this->section->id;
        $event = "updated-banner.$section_id";
        
        $this->dispatch($event);
    }
}
