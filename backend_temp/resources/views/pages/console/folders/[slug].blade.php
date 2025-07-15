<?php
   use function Laravel\Folio\name;
   name('console-folders-index');
?>
<x-layouts.app>
   <x-slot:title>{{ __('Folders') }}</x-slot>

   <livewire:components.console.sites.page :$slug :isFolder="true" lazy/>
</x-layouts.app>
