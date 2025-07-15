<?php
   use function Laravel\Folio\name;
   name('console-wallet-index');
?>
<x-layouts.app>
   <x-slot:title>{{ __('Wallet') }}</x-slot>

   <div>
        <livewire:components.console.wallet.page zzlazy :key="uukey('app', 'console.wallet.page')"/>
   </div>
</x-layouts.app>