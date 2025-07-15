<?php
use function Laravel\Folio\name;

name('dashboard-social-index');
?>
<x-layouts.app>
   <x-slot:title>{{ __('Social Media Management') }}</x-slot>
   
   <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
      <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
         <div class="px-4 py-6 sm:px-0">
            <div class="mb-8">
               <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Social Media Management</h1>
               <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                  Manage all your social media accounts and content from one place
               </p>
            </div>
            
            <livewire:components.dashboard.social.page lazy :key="uukey('social', 'social-page')"/>
         </div>
      </div>
   </div>
</x-layouts.app>