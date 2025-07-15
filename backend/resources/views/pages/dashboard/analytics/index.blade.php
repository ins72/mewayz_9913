<?php
use function Laravel\Folio\name;

name('console-analytics-index');
?>
<x-layouts.app>
   <x-slot:title>{{ __('Analytics Dashboard') }}</x-slot>
   
   <div>
      <livewire:components.console.analytics.page lazy :key="uukey('analytics', 'analytics-page')"/>
   </div>
</x-layouts.app>