<?php
   use function Laravel\Folio\name;
   name('console-settings-index');
?>
<x-layouts.app>
   <x-slot:title>{{ __('Settings') }}</x-slot>

   <div>
    <livewire:components.console.settings.page zzlazy :key="uukey('app', 'settings-page')"/>
   </div>
</x-layouts.app>