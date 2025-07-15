<?php
use function Laravel\Folio\name;

name('console-email-index');
?>
<x-layouts.app>
   <x-slot:title>{{ __('Email Marketing') }}</x-slot>
   
   <div>
      <livewire:components.console.email.page lazy :key="uukey('email', 'email-page')"/>
   </div>
</x-layouts.app>