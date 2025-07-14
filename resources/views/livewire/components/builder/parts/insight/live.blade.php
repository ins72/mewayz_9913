<?php

   use App\Models\SitesLinkerTrack;
   use App\Models\MySession;
   use function Livewire\Volt\{state, mount, placeholder};

   placeholder('
   <div class="p-5 w-full mt-1">
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)]"></div>
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
      <div class="--placeholder-skeleton w-full h-[70px] rounded-[var(--yena-radii-sm)] mt-1"></div>
      <div class="--placeholder-skeleton w-full h-[70px] rounded-[var(--yena-radii-sm)] mt-1"></div>
      <div class="--placeholder-skeleton w-full h-[200px] rounded-[var(--yena-radii-sm)] mt-1"></div>
   </div>');

   state(['site']);

   state([
      'visitors' => fn() => (new MySession)->getInsight($this->site),
   ]);
?>

<div>
    <div x-data="{_pop_open: null}">


        <div class="website-section" x-cloak x-show="_pop_open == null">
            <div class="design-navbar">
                <ul >
                    <li class="close-header !flex">
                      <a @click="__page='-'">
                        <span>
                            {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                        </span>
                      </a>
                   </li>
                   <li class="!pl-0">{{ __('Live') }}</li>
                   <li></li>
                </ul>
             </div>
             <div class="container-small p-[var(--s-2)] pb-[150px]">
                <div>
                    <div class="details-item w-full p-3 md:p-5 z[box-shadow:0_4px_6px_-1px_rgba(0,_0,_0,_0.1),0_2px_4px_-1px_rgba(0,_0,_0,_0.06)] mb-2 rounded-md !bg-white [border:1px_solid_var(--c-mix-1)] mt-2">
                        <div class="details-head">
                            <div class="flex items-center justify-center flex-shrink-0 flex-grow-0 w-8 h-8 rounded-full text-[var(--yena-colors-gray-800)] text-center bg-[var(--yena-colors-gray-200)] opacity-100 mr-2">
                                {!! __i('Business, Products', 'Business, Chart.24', 'w-5 h-5') !!}
                            </div>
                            <div class="details-text caption-sm text-xs md:text-base">{{ __('Current Views') }}</div>
                        </div>
                        <div class="details-counter text-2xl md:text-4xl truncate font-bold truncate">{{ number_format(ao($visitors, 'getviews.unique')) }}</div>
                    </div>
                </div>
                <div class="top-stats-c">
                   <div class="top-stats-c-block">
                      <p class="!justify-start mb-1 gap-1 items-center">
                            <span class="flex items-center justify-center flex-shrink-0 flex-grow-0 w-8 h-8 rounded-full text-[var(--yena-colors-gray-800)] text-center bg-[var(--yena-colors-gray-200)] opacity-100 mr-2">
                                {!! __i('Maps, Navigation', 'Earth, Home, World.3', 'w-5 h-5') !!}
                            </span>
                            
                            {{ __('Countries') }}
                        </p>
                      <p>
                        <span >{{ count(ao($visitors, 'countries') ?? []) }}</span>
                        <span >{{ __('Count') }}</span>
                      </p>
                   </div>
                   <ul >
                    @foreach ((ao($visitors, 'countries') ?? []) as $key => $item)
                    <li>
                        <span >{{ ao($item, 'name') }}</span>
                        <span >{{ nr(ao($item, 'visits')) }}</span>
                    </li>
                    @endforeach
                   </ul>
                </div>
                <div class="top-stats-c">
                   <div class="top-stats-c-block">
                      <p class="!justify-start mb-1 gap-1 items-center">
                            <span class="flex items-center justify-center flex-shrink-0 flex-grow-0 w-8 h-8 rounded-full text-[var(--yena-colors-gray-800)] text-center bg-[var(--yena-colors-gray-200)] opacity-100 mr-2">
                                {!! __i('Maps, Navigation', 'Direction, Arrow, Road', 'w-5 h-5') !!}
                            </span>
                            
                            {{ __('Cities') }}
                        </p>
                      <p>
                        <span >{{ count(ao($visitors, 'cities') ?? []) }}</span>
                        <span >{{ __('Count') }}</span>
                      </p>
                   </div>
                   <ul >
                    @foreach ((ao($visitors, 'cities') ?? []) as $key => $item)
                    <li>
                        <span >{{ $key }}</span>
                        <span >{{ nr(ao($item, 'visits')) }}</span>
                    </li>
                    @endforeach
                   </ul>
                </div>
                <div class="top-stats-c">
                   <div class="top-stats-c-block">
                      <p class="!justify-start mb-1 gap-1 items-center">
                            <span class="flex items-center justify-center flex-shrink-0 flex-grow-0 w-8 h-8 rounded-full text-[var(--yena-colors-gray-800)] text-center bg-[var(--yena-colors-gray-200)] opacity-100 mr-2">
                                {!! __i('Computers Devices Electronics', 'Iphone, Mobile, Phone', 'w-5 h-5') !!}
                            </span>
                            
                            {{ __('Device') }}
                        </p>
                      <p>
                        <span >{{ count(ao($visitors, 'devices') ?? []) }}</span>
                        <span >{{ __('Count') }}</span>
                      </p>
                   </div>
                   <ul >
                    @foreach ((ao($visitors, 'devices') ?? []) as $key => $item)
                    <li>
                        <span >{{ ao($item, 'name') }}</span>
                        <span >{{ nr(ao($item, 'visits')) }}</span>
                    </li>
                    @endforeach
                   </ul>
                </div>
                <div class="top-stats-c">
                   <div class="top-stats-c-block">
                      <p class="!justify-start mb-1 gap-1 items-center">
                            <span class="flex items-center justify-center flex-shrink-0 flex-grow-0 w-8 h-8 rounded-full text-[var(--yena-colors-gray-800)] text-center bg-[var(--yena-colors-gray-200)] opacity-100 mr-2">
                                {!! __i('Computers Devices Electronics', 'display-monitor-2', 'w-5 h-5') !!}
                            </span>
                            
                            {{ __('Browser') }}
                        </p>
                      <p>
                        <span >{{ count(ao($visitors, 'browsers') ?? []) }}</span>
                        <span >{{ __('Count') }}</span>
                      </p>
                   </div>
                   <ul >
                    @foreach ((ao($visitors, 'browsers') ?? []) as $key => $item)
                    <li>
                        <span >{{ ao($item, 'name') }}</span>
                        <span >{{ nr(ao($item, 'visits')) }}</span>
                    </li>
                    @endforeach
                   </ul>
                </div>
             </div>
        </div>
    </div>

</div>