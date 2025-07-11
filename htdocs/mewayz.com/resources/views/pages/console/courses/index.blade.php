<?php
   use function Laravel\Folio\name;
   name('console-courses-index');
?>
<x-layouts.app>
   <x-slot:title>{{ __('Courses') }}</x-slot>

   <div>
    <livewire:components.console.courses.page :key="uukey('app', 'courses-page')"/>
   </div>
</x-layouts.app>