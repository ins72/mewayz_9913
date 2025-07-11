<?php
   
   use Carbon\Carbon;
   use function Laravel\Folio\name;
    
   name('console-audience-index');
?>
<x-layouts.app>
   <x-slot:title>{{ __('Audience') }}</x-slot>
   
   <div>
      <livewire:components.console.audience.page :key="uukey('app', 'crm-page')"/>
   </div>
</x-layouts.app>
