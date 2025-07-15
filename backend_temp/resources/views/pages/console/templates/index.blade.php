<?php
   use function Laravel\Folio\name;
    
   name('console-templates-index');
?>
<x-layouts.app>
   <x-slot:title>{{ __('Templates') }}</x-slot>
   
   
   <div>
      <livewire:components.console.templates.page :key="uukey('sites', 'site-temples')"/>
   </div>
</x-layouts.app>
