<?php
   use function Laravel\Folio\name;
    
   name('general-success');
?>
<x-layouts.base>
   <x-slot:title>{{ __('Success') }}</x-slot>
  <div>
      <div x-data>
        <div class="flex min-h-screen flex-col md:!flex-row">
          <div class="flex items-center justify-center bg-[var(--yena-colors-white)] flex-1 md:max-w-[var(--yena-sizes-container-sm)]">

               <div  class="flex flex-col">
                  <div class="flex-grow duration-100">
                     <div class="mx-auto w-full flex items-center">
                     <div class="w-full bg-white rounded-lg px-0 pt-12 lg:pt-20 lg:pt-20">
                           <div class="mx-auto max-w-lg lg:mx-0 pb-5">
                              <div class="flex items-center gap-4 mb-5">
                                 <i class="fi fi-rr-badge-check text-5xl"></i>
                              </div>
                              <div class="font-heading mb-2 px-0 font--12 font-extrabold upper-case tracking-wider flex items-center mb-2">
                                 <h1 class="text-2xl font-bold leading-normal sm:leading-normal whitespace-nowrap font--caveat">{{ __('Yay!') }}</h1>
                                 {{-- <div class="border-b border-solid border-gray-300 w-full ml-2 flex"></div> --}}
                              </div>
                              <p class="mt-2 text-sm text-gray-600">{{ __('Your last purchase was processed & successful, you would receive an update soon.') }}</p>
                              <a class="yena-button-stack mt-2 px-10 w-32" href="{{ request()->get('redirect') ? request()->get('redirect') : route('dashboard-index') }}">
                                 <span class="text-xs">{{ __('Back') }}</span>
                              </a>
                           </div>
                        </div>
                     </div>
                  </div>
            </div>
          </div>
          
          <div style="--bg-url: url({{ login_image() }})" class="[background:var(--bg-url)_center_bottom_/_cover_no-repeat] flex-1 relative overflow-hidden  hidden md:!flex">
          </div>
      </div>
    </div>

  </div>

</x-layouts.base>