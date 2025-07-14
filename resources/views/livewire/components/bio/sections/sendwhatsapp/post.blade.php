<?php
   use function Livewire\Volt\{state, mount, rules};

   state([
      'message' => '',
   ]);


   $sendMessage = function($section, $message){
      // $this->validate([
      //    'message' => 'required',
      // ]);

      $phone = ao($section, 'content.phone');
      $redirect = "https://wa.me/$phone?text=$message";
      return redirect($redirect);
      
   };
?>

<div wire:ignore class="livewire-comp"></div>