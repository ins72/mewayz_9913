<?php

use function Livewire\Volt\{state};


state(helloo: '');
//

?>

<div class="w-full">
    <ul class="h-fit w-full list-none pb-20 pt-4 md:pt-10" wire:key="{{ md5(microtime()) }}">
        <livewire:components.build.sidebar.sidebarspaces :key="uukey('sidebarspaces')">
    </ul>
</div>
