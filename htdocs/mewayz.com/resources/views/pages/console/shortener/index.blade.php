<?php
   use function Laravel\Folio\name;
   name('console-shortener-index');
?>
<x-layouts.app>
   <x-slot:title>{{ __('Link Shortener') }}</x-slot>

   <div>
        <livewire:components.console.shortener.page zzlazy :key="uukey('app', 'console.shortener.page')"/>
   </div>
</x-layouts.app>