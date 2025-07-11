<?php
   
   use Carbon\Carbon;
   use function Laravel\Folio\name;
    
   name('console-qrcode-index');
?>
<x-layouts.app>
   <x-slot:title>{{ __('QrCode') }}</x-slot>
   
   <div>
      <livewire:components.console.qrcode.page :key="uukey('app', 'qrcode-page')"/>
   </div>
</x-layouts.app>
