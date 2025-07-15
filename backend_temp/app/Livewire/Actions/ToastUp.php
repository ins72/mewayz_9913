<?php

namespace App\Livewire\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

trait ToastUp{
    
    function flashToast($type, $message){
        
      $this->js("window.runToast('$type', '$message')");
    }
    function _f($type, $message){
        
      $this->js("window.runToast('$type', '$message')");
    }
}
