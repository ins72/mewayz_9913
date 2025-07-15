<?php
   use function Laravel\Folio\name;
    
   name('dashboard-templates-success');
?>
<x-layouts.app>
   <x-slot:title>{{ __('Success') }}</x-slot>
   
   
   <div>

      <x-empty-state :title="__('You are set!')" :desc="__('Your template has been successfully prepared and is now ready for you to start using.')" image="13.png">
      
      <div class="flex flex-row gap-4 mt-4 lg:flex-row">
         
         <a href="{{ route('dashboard-sites-index') }}" @navigate class="cursor-pointer yena-button-stack">
            <div class="--icon">
               {!! __icon('interface-essential', 'browser-internet-web-network-window-app-icon', 'w-6 h-6') !!}
            </div>

            {{ __('Sites') }}
         </a>
      </div>
   </x-empty-state>
   </div>
</x-layouts.app>