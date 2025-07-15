<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
 
use function Laravel\Folio\render;
 
render(function (View $view, $site) {
  
});


?>
<x-layouts.site>
   <x-slot:title>{{ __('Build') }}</x-slot>
   

   <div>
        <livewire:site.generate :$site :key="uukey('site-page', 'buildout')" />
    </div>

    @push('scripts')
    <script>

        // console.log(window.yenaImport)
        
        // var routes = window._navigo('/', {
        //     hash: true,
        //     linksSelector: ".yena-site-link"
        // });

    </script>
    @endpush
</x-layouts.site>