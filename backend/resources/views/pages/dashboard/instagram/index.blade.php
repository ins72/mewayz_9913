<?php
use function Laravel\Folio\name;

name('console-instagram-index');
?>
<x-layouts.app>
   <x-slot:title>{{ __('Instagram Management') }}</x-slot>
   
   <div>
      <livewire:components.console.instagram.page lazy :key="uukey('instagram', 'instagram-page')"/>
   </div>
</x-layouts.app>