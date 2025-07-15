<?php
   use function Laravel\Folio\name;
   name('console-store-index');
?>
<x-layouts.app>
   <x-slot:title>{{ __('Store') }}</x-slot>

   <div>
    <livewire:components.console.store.page zzlazy :key="uukey('app', 'store-page')"/>
   </div>
</x-layouts.app>