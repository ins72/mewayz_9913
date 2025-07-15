<?php

  use Illuminate\View\View;
  use App\Models\YenaTeam;

  use function Laravel\Folio\render;
  use function Laravel\Folio\name;
    
  name('console-install');
  
  render(function (View $view) {
    if(config('app.INSTALLED')){
      abort(404);
    }

  });
?>
<x-layouts.base>
   <x-slot:title>{{ __('Installation') }}</x-slot>
  <div>
      <div x-data>
        <div class="flex min-h-screen flex-col md:!flex-row">
          <div class="flex items-center justify-center bg-[var(--yena-colors-white)] flex-1 md:max-w-[var(--yena-sizes-container-sm)]">
              <livewire:components.install.page zzlazy :key="uukey('app-install', 'install')"/>
          </div>
          
          <div style="--bg-url: url({{ login_image() }})" class="[background:var(--bg-url)_center_bottom_/_cover_no-repeat] flex-1 relative overflow-hidden  hidden md:!flex">
          </div>
      </div>
    </div>

  </div>

</x-layouts.base>