<?php
   use function Laravel\Folio\name;
   name('dashboard-shortener-edit');
?>
<x-layouts.app>
   <x-slot:title>{{ __('Link Shortener') }}</x-slot>

   <div>
      <livewire:components.console.shortener.edit.index :$slug zzlazy :key="uukey('app', 'console.shortener.edit')"/>
   </div>
</x-layouts.app>