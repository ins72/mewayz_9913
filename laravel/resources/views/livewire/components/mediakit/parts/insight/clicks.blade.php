<?php

   use App\Models\SitesLinkerTrack;
   use App\Models\MySession;
   use function Livewire\Volt\{state, mount, placeholder};

   placeholder('
   <div class="p-5 w-[100%] mt-1">
      <div class="--placeholder-skeleton w-[100%] h-[30px] rounded-[var(--yena-radii-sm)]"></div>
      <div class="--placeholder-skeleton w-[100%] h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
      <div class="--placeholder-skeleton w-[100%] h-[70px] rounded-[var(--yena-radii-sm)] mt-1"></div>
      <div class="--placeholder-skeleton w-[100%] h-[70px] rounded-[var(--yena-radii-sm)] mt-1"></div>
      <div class="--placeholder-skeleton w-[100%] h-[200px] rounded-[var(--yena-radii-sm)] mt-1"></div>
   </div>');


   state(['site']);

   state([
      'linksVisit',
      'links' => fn() => (new SitesLinkerTrack)->topLink($this->site, null),

      'popup_show' => null,
      'slug' => null,
   ]);
?>

<div>


    <div x-data="{_pop_open: null, 'slug': @entangle('slug').live}">

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
                   <li class="!pl-0">{{ __('Clicks') }}</li>
                   <li></li>
                </ul>
             </div>
             <div class="container-small p-[var(--s-2)] pb-[150px]">
                <div>
                    <div class="details-item w-[100%] p-3 md:p-5 z[box-shadow:0_4px_6px_-1px_rgba(0,_0,_0,_0.1),0_2px_4px_-1px_rgba(0,_0,_0,_0.06)] mb-2 rounded-md !bg-white [border:1px_solid_var(--c-mix-1)] !w-full">
                        <div class="details-head">
                            <div class="flex items-center justify-center flex-shrink-0 flex-grow-0 w-8 h-8 rounded-full text-[var(--yena-colors-gray-800)] text-center bg-[var(--yena-colors-gray-200)] opacity-100 mr-2">
                                {!! __i('SEO', 'link-search-loap', 'w-5 h-5') !!}
                            </div>
                            <div class="details-text caption-sm text-xs md:text-base">{{ __('Total Links') }}</div>
                        </div>
                        <div class="details-counter text-2xl md:text-4xl truncate font-bold truncate">{{ number_format(count($links)) }}</div>
                    </div>
                    <div class="details-item w-[100%] p-3 md:p-5 z[box-shadow:0_4px_6px_-1px_rgba(0,_0,_0,_0.1),0_2px_4px_-1px_rgba(0,_0,_0,_0.06)] mb-2 rounded-md !bg-white [border:1px_solid_var(--c-mix-1)] mt-2 !w-full">
                        <div class="details-head">
                            <div class="flex items-center justify-center flex-shrink-0 flex-grow-0 w-8 h-8 rounded-full text-[var(--yena-colors-gray-800)] text-center bg-[var(--yena-colors-gray-200)] opacity-100 mr-2">
                                {!! __i('Design Tools', 'Cursor, Click', 'w-5 h-5') !!}
                            </div>
                            <div class="details-text caption-sm text-xs md:text-base">{{ __('Total Clicks') }}</div>
                        </div>
                        <div class="details-counter text-2xl md:text-4xl truncate font-bold truncate">{{ number_format(ao($linksVisit, 'visits')) }}</div>
                    </div>
                </div>
                <div class="top-stats-c">
                   <div class="top-stats-c-block">
                      <p class="!justify-start mb-1 gap-1 items-center">
                            <span class="flex items-center justify-center flex-shrink-0 flex-grow-0 w-8 h-8 rounded-full text-[var(--yena-colors-gray-800)] text-center bg-[var(--yena-colors-gray-200)] opacity-100 mr-2">
                                {!! __i('--ie', 'attachment-link-1.1', 'w-5 h-5') !!}
                            </span>
                            
                            {{ __('Links') }}
                        </p>
                      {{-- <p>
                        <span >-</span>
                        <span >{{ __('Count') }}</span>
                      </p> --}}
                   </div>
                   <ul >
                    @foreach ($links as $key => $value)
                    <li>
                        <span class="truncate">{{ ao($value, 'link') }}</span>
                        <span class="flex gap-1">{{ nr(ao($value, 'visits')) }}
                            

                            <div class="menu--icon !bg-[var(--yena-colors-gray-200)] !text-[10px] !text-black !w-auto !px-2 !h-5 !cursor-pointer !flex !items-center !justify-center ml-auto gap-1" @click='_pop_open="link"; slug="{{ $key }}";'>
                                {!! __i('interface-essential', 'eye.5', 'w-3 h-3 hidden') !!}
                                {{ __('View') }}
                            </div>
                        </span>
                    </li>
                    @endforeach
                   </ul>
                </div>
             </div>
        </div>


        <template x-if="_pop_open == 'link'">
            <div>
                    
                <livewire:components.bio.parts.insight.link lazy :$site :$slug :key="uukey('builder', 'insight-page-links')">
            </div>
        </template>

    </div>

</div>