<?php

  use Illuminate\View\View;
  use App\Models\YenaTeam;

  use function Laravel\Folio\render;
  use function Laravel\Folio\name;
    
  name('dashboard-team-join');
  
  render(function (View $view, $slug) {
      if(!$team = YenaTeam::where('slug', $slug)->first()) abort(404);

      return $view->with('team', $team);
  });
?>
<x-layouts.base>
   <x-slot:title>{{ __('Join Team') }}</x-slot>
  <div>
      <div x-data>
        <div class="flex min-h-screen flex-col md:!flex-row">
          <div class="flex items-center justify-center bg-[var(--yena-colors-white)] flex-1 md:max-w-[var(--yena-sizes-container-sm)]">
              <livewire:components.team.join lazy :$team :key="uukey('app-team', 'join')"/>
          </div>
          
          <div style="--bg-url: url({{ login_image() }})" class="[background:var(--bg-url)_center_bottom_/_cover_no-repeat] flex-1 relative overflow-hidden  hidden md:!flex">
          </div>
      </div>
    </div>

  </div>

</x-layouts.base>
