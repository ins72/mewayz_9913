<?php
   use function Laravel\Folio\name;
    
   name('console-upgrade-success');
?>
<x-layouts.app>
   <x-slot:title>{{ __('Success') }}</x-slot>
   
   
   <div>

    <div  class="flex flex-col">
        <div class="flex-grow duration-100">
           <div class="max-w-[464px] mx-auto w-full flex items-center">
            <div class="w-full bg-white rounded-lg [box-shadow:var(--yena-shadows-base)] px-0 pt-12 lg:pt-20 lg:pt-20">
                 <div class="mx-auto max-w-xl px-8 lg:mx-0 pb-5">
                    <div class="flex items-center gap-4 mb-5">
                        <i class="fi fi-rr-badge-check text-3xl"></i>
                    </div>
                    <div class="font-heading mb-2 px-0 font--12 font-extrabold upper-case tracking-wider flex items-center mb-2">
                       <h1 class="text-2xl font-bold leading-normal sm:leading-normal whitespace-nowrap font--caveat">{{ __('Success') }}</h1>
                       {{-- <div class="border-b border-solid border-gray-300 w-full ml-2 flex"></div> --}}
                    </div>
                    <p class="mt-2 text-sm text-gray-600">{{ __('Your plan has been activated successfully. ') }}</p>
                    <a class="yena-button-stack mt-2 px-10 w-32" href="{{ route('console-index') }}">
                        <span class="text-xs">{{ __('Home') }}</span>
                    </a>
                 </div>
              </div>
           </div>
        </div>
    </div>
   </div>
</x-layouts.app>