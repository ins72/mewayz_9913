<?php
   use function Laravel\Folio\name;
    
   name('console-trash-index');
?>
<x-layouts.app>
   <x-slot:title>{{ __('Trash') }}</x-slot>
   
   
   <div>
      <livewire:components.console.trash.page zzlazy  :key="uukey('sites', 'site-console-trash')"/>
   </div>
</x-layouts.app>
