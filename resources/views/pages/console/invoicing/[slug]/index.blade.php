<?php
   use function Laravel\Folio\name;
   name('console-invoicing-edit');
?>
<x-layouts.app>
   <x-slot:title>{{ __('Invoicing') }}</x-slot>

   <div>
      <livewire:components.console.invoicing.edit.index :$slug zzlazy :key="uukey('app', 'console.invoicing.edit')"/>
   </div>
</x-layouts.app>