<?php
   
   use Carbon\Carbon;
   use function Laravel\Folio\name;
    
   name('dashboard-index');
?>
<x-layouts.app>
   <x-slot:title>{{ __('Dashboard') }}</x-slot>
   
   <div>
      <livewire:components.dashboard.index  :key="uukey('sites', 'components.dashboard.index')"/>
   </div>
</x-layouts.app>
