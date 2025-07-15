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

   state(['slug'])->reactive();

    state([
        'linker_track' => [],
        'visitors' => [],
    ]);

    $getInsight = function(){
        $this->linker_track = SitesLinkerTrack::where('site_id', $this->site->id)->where('slug', $this->slug)->first();
        $this->visitors = (new SitesLinkerTrack)->getLinkInsight($this->slug, $this->site);
    };

   mount(function(){
    $this->getInsight();
   });
?>

<div>

    <div>

        @if ($slug)
        <div class="website-section">
            <div class="design-navbar">
                <ul >
                    <li class="close-header !flex">
                      <a @click="_pop_open=null">
                        <span>
                            {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                        </span>
                      </a>
                   </li>
                   <li class="!pl-0 truncate">{{ __('Link') }}</li>
                   <li></li>
                </ul>
             </div>

             <div class="container-small p-[var(--s-2)] pb-[150px]">
                <div class="relative flex w-[100%] isolate mb-2">
                    <input type="text" class="w-[100%] h-[3rem] text-[1rem] pl-4 pr-10 rounded-md min-w-0 outline-[transparent_solid_2px] outline-offset-[2px] relative appearance-none [transition-property:background-color,border-color,color,fill,stroke,opacity,box-shadow,transform] duration-200 border-2 border-solid [border-image:initial] border-transparent bg-[#f7f3f2]" placeholder="link goes here..." value="{{ $linker_track ? $linker_track->link : '' }}" readonly="">
                  </div>
                <div>
                    <div class="details-item w-full p-3 md:p-5 z[box-shadow:0_4px_6px_-1px_rgba(0,_0,_0,_0.1),0_2px_4px_-1px_rgba(0,_0,_0,_0.06)] mb-2 rounded-md !bg-white [border:1px_solid_var(--c-mix-1)]">
                        <div class="details-head">
                            <div class="flex items-center justify-center flex-shrink-0 flex-grow-0 w-8 h-8 rounded-full text-[var(--yena-colors-gray-800)] text-center bg-[var(--yena-colors-gray-200)] opacity-100 mr-2">
                                {!! __i('Business, Products', 'Business, Chart.4', 'w-5 h-5') !!}
                            </div>
                            <div class="details-text caption-sm text-xs md:text-base">{{ __('Total Views') }}</div>
                        </div>
                        <div class="details-counter text-2xl md:text-4xl truncate font-bold truncate">{{ number_format(ao($visitors, 'getviews.visits')) }}</div>
                    </div>
                    <div class="details-item w-full p-3 md:p-5 z[box-shadow:0_4px_6px_-1px_rgba(0,_0,_0,_0.1),0_2px_4px_-1px_rgba(0,_0,_0,_0.06)] mb-2 rounded-md !bg-white [border:1px_solid_var(--c-mix-1)] mt-2">
                        <div class="details-head">
                            <div class="flex items-center justify-center flex-shrink-0 flex-grow-0 w-8 h-8 rounded-full text-[var(--yena-colors-gray-800)] text-center bg-[var(--yena-colors-gray-200)] opacity-100 mr-2">
                                {!! __i('Business, Products', 'Business, Chart.24', 'w-5 h-5') !!}
                            </div>
                            <div class="details-text caption-sm text-xs md:text-base">{{ __('Unique Views') }}</div>
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
        @endif
    </div>

</div>