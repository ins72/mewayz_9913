<?php
   use function Laravel\Folio\name;
   name('console-store-settings');
?>
<x-layouts.app>
   <x-slot:title>{{ __('Store - Settings') }}</x-slot>

   <div>
        <livewire:components.console.store.settings zzlazy :key="uukey('app', 'booking-settings')"/>
   </div>
</x-layouts.app>