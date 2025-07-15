<?php
use function Laravel\Folio\name;

name('dashboard-invoicing-index');
?>
<x-layouts.app>
   <x-slot:title>{{ __('Invoicing Management') }}</x-slot>
   
   <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
      <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
         <div class="px-4 py-6 sm:px-0">
            <div class="mb-8">
               <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Invoicing Management</h1>
               <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                  Create and manage invoices for your clients and customers
               </p>
            </div>
            
            <livewire:components.dashboard.invoicing.page lazy :key="uukey('invoicing', 'invoicing-page')"/>
         </div>
      </div>
   </div>
</x-layouts.app>