<?php
use function Laravel\Folio\name;

name('console-social-index');
?>
<x-layouts.app>
   <x-slot:title>{{ __('Social Media Management') }}</x-slot>
   
   <div>
      <livewire:components.console.social.page lazy :key="uukey('social', 'social-page')"/>
   </div>
</x-layouts.app>