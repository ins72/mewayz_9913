<?php
use function Laravel\Folio\name;

name('console-workspace-index');
?>
<x-layouts.app>
   <x-slot:title>{{ __('Workspace Management') }}</x-slot>
   
   <div>
      <livewire:components.console.workspace.page lazy :key="uukey('workspace', 'workspace-page')"/>
   </div>
</x-layouts.app>