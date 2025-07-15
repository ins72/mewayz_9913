<?php
use function Laravel\Folio\name;

name('dashboard-crm-index');
?>
<x-layouts.app>
   <x-slot:title>{{ __('CRM Management') }}</x-slot>
   
   <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
      <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
         <div class="px-4 py-6 sm:px-0">
            <div class="mb-8">
               <h1 class="text-3xl font-bold text-gray-900 dark:text-white">CRM Management</h1>
               <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                  Manage your customer relationships and sales pipeline
               </p>
            </div>
            
            <livewire:components.dashboard.crm.page lazy :key="uukey('crm', 'crm-page')"/>
         </div>
      </div>
   </div>
</x-layouts.app>