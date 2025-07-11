<?php
   use function Laravel\Folio\name;
    
   name('console-sites-index');
?>
<x-layouts.app>
   <x-slot:title>{{ __('Sites') }}</x-slot>
   
   
   <div>
      <livewire:components.console.sites.page lazy :key="uukey('sites', 'site-page')"/>
   </div>
</x-layouts.app>
