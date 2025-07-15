<?php
   use function Laravel\Folio\name;
   name('console-donations-index');
?>
<x-layouts.app>
   <x-slot:title>{{ __('Donations') }}</x-slot>

   <div>
    <livewire:components.console.donations.page :key="uukey('app', 'donations-page')"/>
   </div>
</x-layouts.app>