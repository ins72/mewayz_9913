<?php
   use function Laravel\Folio\name;
   name('console-booking-index');
?>
<x-layouts.app>
   <x-slot:title>{{ __('Booking') }}</x-slot>

   <div>
    <livewire:components.console.booking.page zzlazy :key="uukey('app', 'booking-page')"/>
   </div>
</x-layouts.app>