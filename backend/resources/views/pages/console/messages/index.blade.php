<?php
   use function Laravel\Folio\name;
   name('console-messages-index');
?>
<x-layouts.app>
   <x-slot:title>{{ __('Message') }}</x-slot>

   <div>
    <livewire:components.console.message.page :key="uukey('app', 'messaage-page')"/>
   </div>
</x-layouts.app>