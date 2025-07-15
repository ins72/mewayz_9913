<?php
use function Laravel\Folio\name;

name('dashboard-analytics-index');
?>
<x-layouts.app>
   <x-slot:title>{{ __('Analytics Dashboard') }}</x-slot>
   
   <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
      <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
         <div class="px-4 py-6 sm:px-0">
            <div class="mb-8">
               <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Analytics Dashboard</h1>
               <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                  Monitor your business performance and track key metrics
               </p>
            </div>
            
            <livewire:components.dashboard.analytics.page lazy :key="uukey('analytics', 'analytics-page')"/>
         </div>
      </div>
   </div>
</x-layouts.app>