<?php
   use function Laravel\Folio\name;
   name('dashboard-booking-settings');
?>
<x-layouts.app>
   <x-slot:title>{{ __('Booking - Settings') }}</x-slot>

   <div>
        <livewire:components.console.booking.settings zzlazy :key="uukey('app', 'booking-settings')"/>
   </div>
</x-layouts.app>