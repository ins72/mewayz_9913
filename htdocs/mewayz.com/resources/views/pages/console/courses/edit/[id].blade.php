<?php
   use function Laravel\Folio\name;
   name('console-courses-editor');
?>
<x-layouts.app>
   <x-slot:title>{{ __('Edit') }}</x-slot>

   <div>
    <livewire:components.console.courses.edit.index :$id :key="uukey('app', 'console.courses.edit.index')"/>
   </div>
</x-layouts.app>