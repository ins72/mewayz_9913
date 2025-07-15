<?php
   use function Laravel\Folio\name;
   name('console-invoicing-index');
?>
<x-layouts.app>
   <x-slot:title>{{ __('Invoicing') }}</x-slot>

   <div>
        <livewire:components.console.invoicing.page zzlazy :key="uukey('app', 'console.invoicing.page')"/>
   </div>
</x-layouts.app>