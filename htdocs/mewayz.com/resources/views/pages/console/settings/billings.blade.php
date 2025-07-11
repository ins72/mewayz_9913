<?php
   use function Laravel\Folio\name;
   name('console-settings-billings');

   use function Livewire\Volt\{state, mount, rules};
   mount(function(){
   
   });
?>
<x-layouts.app>
   <x-slot:title>{{ __('Billings') }}</x-slot>

   @volt
   <div>

      <div class="mx-auto flex h-full w-full max-w-[1400px]">
         
         @include('_include.settings.settings-menu', ['current' => 'billing'])
         <div class="flex flex-1 py-10 ps-[344px]">
            <div class="flex w-full flex-col">
               <div class="flex w-full flex-row items-center justify-between ">
                  <div class="text-lg font-semibold">{{ __('Billing') }}</div>
               </div>
               <div class="mt-8 flex max-w-[600px] flex-col gap-8">
                  
               </div>
            </div>
         </div>
      </div>
      
   </div>
   @endvolt
</x-layouts.app>
