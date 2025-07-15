<?php
   
   use Carbon\Carbon;
   use function Laravel\Folio\name;
    
   name('console-index');
?>
<x-layouts.app>
   <x-slot:title>{{ __('Console') }}</x-slot>
   
   <div>
      <livewire:components.console.index  :key="uukey('sites', 'components.console.index')"/>
   </div>
</x-layouts.app>
