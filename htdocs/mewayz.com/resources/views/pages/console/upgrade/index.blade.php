<?php
   use function Laravel\Folio\name;
    
   name('console-upgrade-index');
?>
<x-layouts.app>
   <x-slot:title>{{ __('Upgrade') }}</x-slot>
   
   
   <div class="pt-0">
      <livewire:components.upgrade.page zzlazy :key="uukey('app', 'upgrade-page')"/>
   </div>
</x-layouts.app>
