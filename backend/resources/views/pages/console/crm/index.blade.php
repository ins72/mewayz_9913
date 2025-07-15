<?php
use function Laravel\Folio\name;

name('console-crm-index');
?>
<x-layouts.app>
   <x-slot:title>{{ __('CRM Management') }}</x-slot>
   
   <div>
      <livewire:components.console.crm.page lazy :key="uukey('crm', 'crm-page')"/>
   </div>
</x-layouts.app>