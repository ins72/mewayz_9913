<?php
   use function Laravel\Folio\name;
    
   name('console-create-index');
?>
<x-layouts.app>
   <x-slot:title>{{ __('Create') }}</x-slot>
   
   <style>
      .yena-topbar, .yena-sidebar, .mobile-header-toolbar{
         display: none !important;
      }

      .yena-root-main, .yena-container{
         padding: 0 !important;
         margin: 0 !important;
         max-width: 100% !important;
      }
   </style>
   
   <div class="ai-create-wrapper !block">
      <div class="fixed z-0 pointer-events-none top-0 w-screen min-h-screen [background:var(--bg-img)_center_center_repeat] animate-[180s_linear_0s_infinite_normal_none_running_animation-1w9onv1] [mask-image:linear-gradient(to_left,_rgba(0,_0,_0,_0.75),_transparent,_rgba(0,_0,_0,_0.75))] [mask-repeat:repeat] [mask-size:140px]" style="--bg-img:url({{ gs('assets/image/others/Stars-2.svg') }})"></div>

      
      <livewire:components.console.create.page  :key="uukey('sites', 'site-console-create')"/>
   </div>
</x-layouts.app>
