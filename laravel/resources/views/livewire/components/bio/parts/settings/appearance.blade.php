
<?php

   use function Livewire\Volt\{state, mount, placeholder};
   state([
      '___page' => '-',
      'site',

      'colors' => [
         'ECB159',
         'EEA5A6',
         'BBC3A4',
         'FFF6E9',
         '436850',
         '3B3486',
         '030637',
         '3E3232',
      ]
   ]);
?>
<div class="banner-section !block">
   <div x-data="builder__edit_appearance" wire:ignore>
        <div x-show="___page == 'fonts'">
            <div>
                    
                <div class="settings-section section">
                    <div class="settings-section-content">
            
                    <div class="top-bar">
                        <div class="--navbar">
                            <ul >
                                <li class="close-header !flex">
                                    <a @click="___page='-'">
                                        <span>
                                            {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                                        </span>
                                    </a>
                                </li>
                                <li class="!pl-0">{{ __('Fonts') }}</li>
                                <li class="!flex items-center !justify-center"></li>
                            </ul>
                        </div>
                        <div class="sticky container-small">
                                <div class="tab-link">
                                <ul class="tabs">
                                {{-- <li class="tab !w-[100%]" @click="_tab = 'title'" :class="{'active': _tab == 'title'}">{{ __('Title') }}</li> --}}
                                <li class="tab !w-[100%]" @click="_tab = 'body'" :class="{'active': _tab == 'body'}">{{ __('Body') }}</li>
                                </ul>
                                </div>
                        </div>
                    </div>
                    <div class="container-small tab-content-box">
                        <div class="tab-content">
                            <div x-cloak :class="{'active': _tab == 'body'}" data-tab-content>
                                <div class="w-[100%] py-0 px-[var(--s-2)] mt-2">
                                        <div class="search-font">
                                            <form>
                                                <div class="input-box search-box">
                                                <input type="text" class="input-large search-input" x-model="bodySearch" placeholder="{{ __('Search...') }}">
                                                <div class="input-icon zoom-icon">
                                                    {!! __i('--ie', 'search.1', 'w-5 h-5') !!}
                                                </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="font-list">
                                            <ul>
                                                
                                                <li class="heading-body active" :class="{'!hidden': !site.settings.fontName}">
                                                <span x-text="site.settings.fontName"></span>
                                                <span>
                                                    {!! __i('--ie', 'done-check.3', 'w-5 h-5') !!}
                                                </span>
                                                </li>

                                                <template x-for="(item, index) in filteredBodyFont()" :key="index">
                                                <li class="heading-body" x-text="item[0]" @click="selectBodyFont(item)"></li>
                                                </template>
                                            </ul>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>

        <div x-cloak x-show="___page == '-'">
            <div class="settings-section section">
                <div class="settings-section-content">
            
                    <div class="top-bar">
                
                        <div class="--navbar">
                            <ul >
                                <li class="close-header !flex">
                                    <a @click="__page='-'">
                                        <span>
                                            {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                                        </span>
                                    </a>
                                </li>
                                <li class="!pl-0">{{ __('Appearance') }}</li>
                                <li class="!flex items-center !justify-center"></li>
                            </ul>
                        </div>
                        <div class="sticky container-small">
                            <div class="tab-link">
                                <ul class="tabs">
                                <li class="tab !w-[100%]" @click="__tab = 'themes'" :class="{'active': __tab == 'themes'}">{{ __('Themes') }}</li>
                                <li class="tab !w-[100%]" @click="__tab = 'custom'" :class="{'active': __tab == 'custom'}">{{ __('Custom') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="container-small tab-content-box">
                        <div class="tab-content">
                            <div x-cloak :class="{'active': __tab == 'themes'}" data-tab-content>
                                <div class="w-[100%] py-0 px-[var(--s-2)] mt-[var(--s-2)]">
                                    <div class="theme-list !grid !grid-cols-2">
                                        <template x-for="(item, index) in themes" :key="index">
                                            <div :class="{'active !bg-[var(--c-mix-10)]': site.settings.currentTheme == item.id}" class="!rounded-lg !bg-[#f1f1f1] [box-shadow:0_4px_6px_-1px_rgba(0,_0,_0,_0.1),0_2px_4px_-1px_rgba(0,_0,_0,_0.06)] !w-[100%] !h-[200px] !p-[5px]" @click="setTheme(item)">
                                                <div class="theme-text !p-0 !mb-0 !rounded-lg overflow-hidden [box-shadow:0_0.25em_0.375em_-0.0625em_rgba(0,_0,_0,_0.1),_0_0.125em_0.25em_-0.0625em_rgba(0,_0,_0,_0.06),_0_0_0_0.0625em_rgb(255_255_255_/_64%)] !h-full relative">
                                                    <template x-if="item.background && item.background.image">
                                                       <div :style="{'--bg-image': 'url('+assetGs +'/'+ item.background.image+')'}"  class="absolute left-[0] top-[0] h-full w-[100%] min-w-full min-h-full bg-center [background:var(--bg-image)] bg-no-repeat bg-cover"></div>
                                                    </template>
                                                    <template x-if="item.background && item.background.split_color">
                                                       <div :style="`background: linear-gradient(${item.background.split_angle}deg, ${item.background.color_1} ${item.background.split_offset}%, ${item.background.split_offset}%, ${item.background.color_2} 100%)`"  class="absolute left-[0] top-[0] h-full w-[100%]"></div>
                                                    </template>

                                                    <template x-if="item.background && item.background.gradient">
                                                       <div x-data="{
                                                            backgroundStyle: function(){
                                                                $store.bioBackground.site = item;
                                                                return $store.bioBackground.backgroundStyle();
                                                            }
                                                       }" class="absolute left-[0] top-[0] h-full w-[100%]">
                                                        <div :style="backgroundStyle" class="gradient-color block h-full w-[100%]"></div>
                                                       </div>
                                                    </template>
                                                    {{-- <div class="grid [grid-template:'accent'_minmax(2.5em,_auto)_'body'_/_1fr] overflow-hidden flex-1 pointer-events-none" :style="{
                                                        'background': '#' + item.config.color,
                                                    }"></div> --}}
                                                    <div class="p-[var(--s-1)] flex flex-col justify-center h-full custom-styles-link z-10 relative">
                                                        {{-- <h1 class="!text-[1.6em]" :style="{
                                                            'font-family': item.config.fontName
                                                        }">{{ __('Title') }}</h1> --}}
                                                        
                                                        <p class="overflow-hidden overflow-ellipsis !text-[0.9em] !mt-0 !mb-0 font-normal text-[#3d3838]" :style="{
                                                            'font-family': item.config.fontName
                                                        }">{{ __('Body') }} & <a class="text-[#2D2E34] cursor-pointer font-bold !underline">{{ __('link') }}</a>
                                                        </p>
                
                                                        <button class="btn mt-1" :style="{
                                                        'font-family': item.config.fontName,
                                                        'background': '#' + item.config.color,
                                                        'color': $store.builder.getContrastColor('#' + item.config.color),
                                                        'border-radius': item.config.corners == 'rounded' ? 'var(--r-full)' : (item.config.corners == 'round' ? 'var(--r-small)' : (item.config.corners == 'straight' ? 'var(--r-none)' : '')),
                                                        }" :class="{
                                                            [`--${item.config.__theme}`]: true,
                                                        }">
                                                        {{ __('Link') }}   
                                                        </button>
                                                        <button class="btn !mt-1" :style="{
                                                        'font-family': item.config.fontName,
                                                        'background': '#' + item.config.color,
                                                        'color': $store.builder.getContrastColor('#' + item.config.color),
                                                        'border-radius': item.config.corners == 'rounded' ? 'var(--r-full)' : (item.config.corners == 'round' ? 'var(--r-small)' : (item.config.corners == 'straight' ? 'var(--r-none)' : '')),
                                                        }" :class="{
                                                            [`--${item.config.__theme}`]: true,
                                                        }">
                                                        {{ __('Link') }}   
                                                        </button>
                                                        <button class="btn !mt-1" :style="{
                                                        'font-family': item.config.fontName,
                                                        'background': '#' + item.config.color,
                                                        'color': $store.builder.getContrastColor('#' + item.config.color),
                                                        'border-radius': item.config.corners == 'rounded' ? 'var(--r-full)' : (item.config.corners == 'round' ? 'var(--r-small)' : (item.config.corners == 'straight' ? 'var(--r-none)' : '')),
                                                        }" :class="{
                                                            [`--${item.config.__theme}`]: true,
                                                        }">
                                                        {{ __('Link') }}   
                                                        </button>
                                                    </div>
                                                </div>
                                                {{-- <div class="theme-title">
                                                    <p x-text="item.name"></p>
                                                    <span x-show="site.settings.currentTheme == index" x-cloak>
                                                    {!! __i('--ie', 'done-check.3', 'w-5 h-5') !!}
                                                    </span>
                                                </div> --}}
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            <div x-cloak :class="{'active': __tab == 'custom'}" data-tab-content>
                                <div class="w-[100%] px-[var(--s-2)] py-[0] mt-[var(--s-2)] mt-4">
                                    <div class="colors-container">
                                        <div class="input-box">
                                            <div class="input-label">{{ __('Theme') }}</div>
                                            <div class="input-group">
                                                <div class="color-selector">
                                                    @if(is_array($colors))
                                                    @foreach ($colors as $item)
                                                    <div class="color-box mod" @click="site.settings.color='#{{ $item }}'" style="--c: #{{ $item }};"><span style="--c: #{{ $item }}; background: #{{ $item }}"></span></div>
                                                    @endforeach
                                                    @endif
                                                </div>
                                                <div class="custom-color !block">
                                                    <form onsubmit="return false;">
                                                    <div class="input-box !pb-0">
                                                        <div class="input-group">
                                                            <div type="color" class="input-small input-color pickr-o" :style="{
                                                                'background-color': site.settings.color,
                                                                'color': $store.builder.getContrastColor(site.settings.color)
                                                                }" maxlength="6">
                                                                <div x-pickr="site.settings.color"></div>    
                                                            </div>
                                                                
                                                            <span class="hash"  :style="{
                                                                'color': $store.builder.getContrastColor(site.settings.color)
                                                                }">#</span>
                                                            <span class="color-generator" :style="{
                                                                'background-color': site.settings.color,
                                                                'color': $store.builder.getContrastColor(site.settings.color)
                                                                }"></span>
                                                        </div>
                                                    </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="font-style">
                                    <div class="input-box">
                                        <div class="input-label">{{__('Fonts')}}</div>
                                        <div class="input-group">
                                            <button :style="{'font-family': site.settings.fontName}" type="button" @click="___page='fonts'; _tab='body';">
                                                {{ __('Body') }} & {{ __('link') }}
                                                <span>
                                                {!! __i('Arrows, Diagrams', 'Arrow.5', 'w-5 h-5') !!}
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                    </div>

                                                        
                                    <form onsubmit="return false">
                                        <div class="colors-container">
                                            <div class="input-box">
                                                <div class="input-label">{{ __('Section Color') }}</div>
                                                <div class="input-group">
                                                    <div class="input-group btns !w-full !flex-row !border-0" id="color">
                                                        <button class="btn-nav !w-[50%]" :class="{
                                                            'active' : site.background !== null && site.background.section_color_enable == 'disable',
                                                        }" type="button" @click="site.background.section_color_enable = 'disable'">
                                                            {!! __i('--ie', 'delete-disabled-ross-hexagon.1', 'w-5 h-5') !!}
                                                        </button>
                                                        <button class="btn-nav !w-[50%]" :class="{
                                                            'active' :  site.background !== null && site.background.section_color_enable == 'enable',
                                                        }" type="button" @click="site.background.section_color_enable = 'enable'">
                                                        <span class="w-4 h-4 rounded-full bg-[var(--accent)]"></span>
                                                        </button>
                                                    </div>
                                                    {{-- <div class="color-selector">
                                                        @if(is_array($colors))
                                                        @foreach ($colors as $item)
                                                        <div class="color-box mod" @click="site.settings.color='#{{ $item }}'" style="--c: #{{ $item }};"><span style="--c: #{{ $item }}; background: #{{ $item }}"></span></div>
                                                        @endforeach
                                                        @endif
                                                    </div> --}}

                                                    <template x-if="site.background.section_color_enable == 'enable'">
                                                        <div class="custom-color !block">
                                                            <form onsubmit="return false;">
                                                            <div class="input-box !pb-0">
                                                                <div class="input-group">
                                                                    <div type="color" class="input-small input-color pickr-o" x-model="site.settings.section_color" :style="{
                                                                        'background-color': site.settings.section_color,
                                                                        'color': $store.builder.getContrastColor(site.settings.section_color)
                                                                        }" maxlength="6">

                                                                        <div x-pickr="site.settings.section_color"></div>
                                                                    </div>
                                                                        
                                                                    <span class="hash"  :style="{
                                                                        'color': $store.builder.getContrastColor(site.settings.section_color)
                                                                        }">#</span>
                                                                    <span class="color-generator" :style="{
                                                                        'background-color': site.settings.section_color,
                                                                        'color': $store.builder.getContrastColor(site.settings.section_color)
                                                                        }"></span>
                                                                </div>
                                                            </div>
                                                            </form>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="colors-container">
                                            <div class="input-box">
                                                <div class="input-label">{{ __('Background') }}</div>
                                                <div class="input-group">
                                                    <div class="input-group btns !border-0 !w-full !flex-row">
                                                        <button class="btn-nav" :class="{
                                                            'active' : site.background !== null && site.background.color == 'transparent',
                                                        }" type="button" @click="site.background.color = 'transparent'">
                                                            {!! __i('--ie', 'delete-disabled-ross-hexagon.1', 'w-5 h-5') !!}
                                                        </button>
                                                        <button class="btn-nav" :class="{
                                                            'active' :  site.background !== null && site.background.color == 'default',
                                                        }" type="button" @click="site.background.color = 'default'">
                                                        <span class="w-4 h-4 rounded-full bg-[#ccc]"></span>
                                                        </button>
                                                        <button class="btn-nav" :class="{
                                                            'active' :  site.background !== null && site.background.color == 'accent',
                                                        }" type="button" @click="site.background.color = 'accent'">
                                                        <span class="w-4 h-4 rounded-full bg-[var(--accent)]"></span>
                                                        </button>
                                                    </div>

                                                    <template x-if="site.background.color == 'default'">
                                                        <div class="custom-color !block">
                                                            <form onsubmit="return false;">
                                                            <div class="input-box !pb-0">
                                                                <div class="input-group">
                                                                    <div type="color" class="input-small input-color pickr-o" x-model="site.settings.color_two" :style="{
                                                                        'background-color': site.settings.color_two,
                                                                        'color': $store.builder.getContrastColor(site.settings.color_two)
                                                                        }" maxlength="6">
                                                                        <div x-pickr="site.settings.color_two"></div>
                                                                    </div>
                                                                        
                                                                    <span class="hash"  :style="{
                                                                        'color': $store.builder.getContrastColor(site.settings.color_two)
                                                                        }">#</span>
                                                                    <span class="color-generator" :style="{
                                                                        'background-color': site.settings.color_two,
                                                                        'color': $store.builder.getContrastColor(site.settings.color_two)
                                                                        }"></span>
                                                                </div>
                                                            </div>
                                                            </form>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <template x-if="site.background.color == 'accent' || site.background.color == 'default'">
                                            <div class="input-box">
                                                <div class="input-label">{{ __('Text Color') }}</div>
                                                <div class="input-group btns two-col-btns">
                                                    <button class="btn-nav" :class="{
                                                        'active' : site.settings.text_color == 'white',
                                                    }" type="button" @click="site.settings.text_color = 'white'">
                                                    <span class="w-4 h-4 rounded-full bg-[#eee] mr-1"></span>
                                                        {{__('White')}}
                                                    </button>
                                                    <button class="btn-nav" :class="{
                                                        'active' :  site.settings.text_color == 'black' || !site.settings.text_color,
                                                    }" type="button" @click="site.settings.text_color = 'black'">
                                                    <span class="w-4 h-4 rounded-full bg-[#000] mr-1"></span>
                                                    {{__('Black')}}
                                                    </button>
                                                </div>
                                            </div>
                                        </template>
                                        <div class="relative block h-20 mb-1 text-center border-2 border-dashed rounded-lg cursor-pointer group bg-white- hover:border-solid hover:border-yellow-600" :class="{
                                        'border-gray-200': !site.background.image,
                                        'border-transparent': site.background.image,
                                        }">
                                            <template x-if="site.background.image">
                                                <div class="absolute top-0 bottom-0 left-0 right-0 items-center justify-center hidden w-[100%] h-full group-hover:flex">
                                                    <div class="flex items-center justify-center w-8 h-8 bg-white rounded-full icon-shadow" @click="site.background.image = ''; $dispatch('appearanceBackgroundBg', {
                                                    image: null,
                                                    public: null,
                                                    })">
                                                        <i class="fi fi-rr-trash"></i>
                                                    </div>
                                                </div>
                                            </template>
                                            <template x-if="!site.background.image">
                                                <div class="flex items-center justify-center w-[100%] h-full" @click="openMedia({
                                                    event: 'appearanceBackgroundBg',
                                                    sectionBack:'navigatePage(\'__last_state\')'
                                            });">
                                                    <div>
                                                        <span class="m-0 -mt-2 text-black loader-line-dot-dot font-2"></span>
                                                    </div>
                                                    <i class="fi fi-ss-plus"></i>
                                                </div>
                                            </template>
                                            <template x-if="site.background.image">
                                                <div class="h-full w-[100%]">
                                                    <img :src="$store.builder.getMedia(site.background.image)" class="h-full w-[100%] object-cover rounded-md" alt="">
                                                </div>
                                            </template>
                                        </div>
                                        
                                        <div class="input-box">
                                            <div class="input-label">{{ __('Width') }}</div>
                                            <div class="input-group btns two-col-btns">
                                                <button class="btn" :class="{
                                                    'active' : site.background !== null && site.background.width == 'fill',
                                                }" type="button" @click="site.background.width = 'fill'">{{ __('Fill') }}</button>
                                                <button class="btn" :class="{
                                                    'active' : site.background !== null && site.background.width == 'fit',
                                                }" type="button" @click="site.background.width = 'fit'">{{ __('Fit') }}</button>
                                            </div>
                                        </div>
                                        

                                        <template x-if="site.background !== null && site.background.image && site.background.overlay && site.background.color !== 'transparent'">
                                            <div class="input-box">
                                                <div class="input-label">{{__('Overlay')}}</div>
                                                <div class="input-group btns">
                                                    <button class="btn" type="button" :class="{'active': site.background !== null && site.background.overlay_size == 's'}" @click="site.background.overlay_size = 's'">S</button>
                                                    <button class="btn" type="button" :class="{'active': site.background !== null && site.background.overlay_size == 'm'}" @click="site.background.overlay_size = 'm'">M</button>
                                                    <button class="btn" type="button" :class="{'active': site.background !== null && site.background.overlay_size == 'l'}" @click="site.background.overlay_size = 'l'">L</button>
                                                </div>
                                            </div>
                                        </template>

                                        <template x-if="site.background !== null && site.background.blur && site.background.image && !site.background.parallax">
                                            <div class="input-box">
                                                <div class="input-label">{{__('Blur')}}</div>
                                                <div class="input-group btns">
                                                    <button class="btn" type="button" :class="{'active': site.background !== null && site.background.blur_size == 's'}" @click="site.background.blur_size = 's'">S</button>
                                                    <button class="btn" type="button" :class="{'active': site.background !== null && site.background.blur_size == 'm'}" @click="site.background.blur_size = 'm'">M</button>
                                                    <button class="btn" type="button" :class="{'active': site.background !== null && site.background.blur_size == 'l'}" @click="site.background.blur_size = 'l'">L</button>
                                                </div>
                                            </div>
                                        </template>

                                        <template x-if="site.background !== null && site.background.split_color">
                                            <div x-data="splitColor">
                                                <div wire:ignore="">
                                                    <div class="colors-container">
                                                    <div class="input-box !pb-1">
                                                        <div class="input-group">
                                                            <div class="color-selector !h-[5rem]" :style="`background: linear-gradient(${selectedAngle}deg, ${color_1} ${offset_angle}%, ${offset_angle}%, ${color_2} 100%)`"></div>
                                                            <div class="custom-color !block">
                                                                <form onsubmit="return false;">
                                                                <div class="input-box !pb-0">
                                                                    <div class="p-1 flex items-center gap-2">
                                                                        <div class="input-group !border-0">
                                                                            <div type="color" class="input-small input-color !rounded-lg pickr-o !p-0 overflow-hidden !w-[5rem]" x-model.debounce.200ms="color_1" :style="{
                                                                            'background-color': color_1,
                                                                            }" maxlength="6">
                                                                                <div x-pickr="site.background.color_1"></div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="input-group !border-0">
                                                                            <div type="color" class="input-small input-color !rounded-lg pickr-o !p-0 overflow-hidden !w-[5rem]" x-model.debounce.800ms="color_2" :style="{
                                                                            'background-color': color_2,
                                                                            }" maxlength="6">
                                                                                <div x-pickr="site.background.color_2"></div>
                                                                            </div>
                    
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    </div>
                                                </div>
                                                <div class="input-box">
                                                    <label for="text-size">{{ __('Angle') }}</label>
                                                    <div class="input-group">
                                                    <input type="range" class="input-small range-slider !rounded-l-none" min="0" max="360" step="25" x-model="selectedAngle">
                                                    
                                                    <p class="image-size-value" x-text="selectedAngle  + '%'"></p>
                                                    </div>
                                                </div>
                                                <div class="input-box">
                                                    <label for="text-size">{{ __('Offset') }}</label>
                                                    <div class="input-group">
                                                    <input type="range" class="input-small range-slider !rounded-l-none" min="0" max="100" step="10" x-model="offset_angle">
                                                    
                                                    <p class="image-size-value" x-text="offset_angle  + '%'"></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        <template x-if="site.background !== null && site.background.gradient">
                                            <div x-data="gradientColor">
                                                <div wire:ignore="">
                                                    <div class="colors-container">
                                                    <div class="input-box !pb-1">
                                                        <div class="input-group">
                                                            <div class="color-selector !h-[5rem]" :style="`background: linear-gradient(${selectedAngle}deg, ${color_1}, ${color_2})`"></div>
                                                            <div class="custom-color !block">
                                                                <form onsubmit="return false;">
                                                                <div class="input-box !pb-0">
                                                                    <div class="p-1 flex items-center gap-2">
                                                                        <div class="input-group !border-0">
                                                                            <div type="color" class="input-small input-color !rounded-lg pickr-o !p-0 overflow-hidden !w-[5rem]" x-model.debounce.200ms="color_1" :style="{
                                                                            'background-color': color_1,
                                                                            }" maxlength="6">
                                                                                <div x-pickr="site.background.color_1"></div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="input-group !border-0">
                                                                            <div type="color" class="input-small input-color !rounded-lg pickr-o !p-0 overflow-hidden !w-[5rem]" x-model.debounce.800ms="color_2" :style="{
                                                                            'background-color': color_2,
                                                                            }" maxlength="6">
                                                                                <div x-pickr="site.background.color_2"></div>
                                                                            </div>
                    
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    </div>
                                                </div>
                                                <div class="input-box">
                                                    <label for="text-size">{{ __('Angle') }}</label>
                                                    <div class="input-group">
                                                    <input type="range" class="input-small range-slider !rounded-l-none" min="0" max="360" step="45" x-model="selectedAngle">
                                                    
                                                    <p class="image-size-value" x-text="selectedAngle  + '%'"></p>
                                                    </div>
                                                </div>
                                                <div class="input-box">
                                                    <div class="input-label">{{ __('Animate') }}</div>
                                                    <div class="input-group btns two-col-btns">
                                                        <button class="btn-nav" :class="{
                                                            'active' : !site.background.gradient_animate,
                                                        }" type="button" @click="site.background.gradient_animate = false">
                                                            {{__('No')}}
                                                        </button>
                                                        <button class="btn-nav" :class="{
                                                            'active' :  site.background.gradient_animate,
                                                        }" type="button" @click="site.background.gradient_animate = true">
                                                        {{__('Yes')}}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        <template x-if="site.background.gradient || site.background.split_color">
                                            <div class="input-box mt-1">
                                                <label for="text-size">{{ __('Transparency') }}</label>
                                                <div class="input-group">
                                                <input type="range" class="input-small range-slider !rounded-l-none transparency-slider" min="0" max="100" step="0.1" x-model="site.background.transparency" @input="checkMiddle">
                                                
                                                    <p class="image-size-value left-[12px] !top-[12px]">
                                                        {!! __i('interface-essential', 'moon.1', 'w-4 h-4 text-black') !!}
                                                    </p>
                                                    <p class="image-size-value !top-[12px]">
                                                        {!! __i('weather', 'Sun.1', 'w-4 h-4 text-black') !!}
                                                    </p>
                                                </div>
                                            </div>
                                        </template>
                                    </form>
                                    
                                    <div class="advanced-section-settings">
                                        <form onsubmit="return false">
                                            <div class="input-box open-tab-box">
                                                <div class="input-group">
                                                    <div class="switchWrapper">
                                                        <input id="showGradient-switch" x-model="site.background.gradient" x-on:input="$event.target.checked ? site.background.split_color = false : ''" type="checkbox" class="switchInput">
                                                        
                                                        <label for="showGradient-switch" class="switchLabel">{{ __('Gradient') }}</label>
                                                        <div class="slider"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="input-box open-tab-box">
                                                <div class="input-group">
                                                <div class="switchWrapper">
                                                    <input id="showSplitColor-switch" x-model="site.background.split_color" x-on:input="$event.target.checked ? site.background.gradient = false : ''" type="checkbox" class="switchInput">
                                                    
                                                    <label for="showSplitColor-switch" class="switchLabel">{{ __('Split Color') }}</label>
                                                    <div class="slider"></div>
                                                </div>
                                                </div>
                                            </div>
                                            <div class="input-box open-tab-box" :class="{
                                                '!hidden': site.background.color == 'transparent' || !site.background.image,
                                            }">
                                                <div class="input-group">
                                                <div class="switchWrapper">
                                                    <input id="showOverlay-switch" x-model="site.background.overlay" type="checkbox" class="switchInput">
                                                    
                                                    <label for="showOverlay-switch" class="switchLabel">{{ __('Overlay') }}</label>
                                                    <div class="slider"></div>
                                                </div>
                                                </div>
                                            </div>
                                            <div class="input-box open-tab-box" :class="{'!hidden': site.background.parallax || !site.background.image}">
                                                <div class="input-group">
                                                <div class="switchWrapper">
                                                    <input id="showBlur-switch" x-model="site.background.blur" type="checkbox" class="switchInput">
                                                    
                                                    <label for="showBlur-switch" class="switchLabel">{{ __('Blur') }}</label>
                                                    <div class="slider"></div>
                                                </div>
                                                </div>
                                            </div>
                                            <div class="input-box open-tab-box" :class="{'!hidden': !site.background.image}">
                                                <div class="input-group">
                                                <div class="switchWrapper">
                                                    <input id="showGreyscale-switch" x-model="site.background.greyscale" type="checkbox" class="switchInput">
                                                    
                                                    <label for="showGreyscale-switch" class="switchLabel">{{ __('Greyscale') }}</label>
                                                    <div class="slider"></div>
                                                </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
   </div>
   
   @script
   <script>
      Alpine.data('gradientColor', () => {
            return {
                get selectedAngle(){
                    let angle = this.site.background.gradient_angle;
                    if(!angle) angle = 0;
                    return angle;
                },
                set selectedAngle(value){
                    this.site.background.gradient_angle = value;
                },
                get color_1(){
                    let color = this.site.background.color_1;
                    if(!color) color = '#ffffff';
                    return color;
                },
                set color_1(value){
                    this.site.background.color_1 = value;
                },
                get color_2(){
                    let color = this.site.background.color_2;
                    if(!color) color = '#000000';
                    return color;
                },
                set color_2(value){
                    this.site.background.color_2 = value;
                },

                init(){

                }
            }
        });
   </script>
   @endscript
   @script
   <script>
      Alpine.data('splitColor', () => {
            return {
                get offset_angle(){
                    let angle = this.site.background.split_offset;
                    if(!angle) angle = 50;
                    return angle;
                },
                set offset_angle(value){
                    this.site.background.split_offset = value;
                },
                get selectedAngle(){
                    let angle = this.site.background.split_angle;
                    if(!angle) angle = 45;
                    return angle;
                },
                set selectedAngle(value){
                    this.site.background.split_angle = value;
                },
                get color_1(){
                    let color = this.site.background.color_1;
                    if(!color) color = '#ffffff';
                    return color;
                },
                set color_1(value){
                    this.site.background.color_1 = value;
                },
                get color_2(){
                    let color = this.site.background.color_2;
                    if(!color) color = '#000000';
                    return color;
                },
                set color_2(value){
                    this.site.background.color_2 = value;
                },

                init(){

                }
            }
        });
   </script>
   @endscript

   @script
   <script>
      Alpine.data('builder__edit_appearance', () => {
         return {
               __tab: 'themes',
               _tab: 'body',
               ___page: @entangle('___page'),
               assetGs: '{{ gs('assets/image/others') }}',
               autoSaveTimer: null,
               fonts: [],
               font: [],

               bodyFont: [],
               bodySearch: '',

               headFont: [],
               headSearch: '',

               themes: {!! collect(config('bio.themes')) !!},
               snapThreshold: 5,
               checkMiddle() {
                    const middleValue = (parseInt(this.$root.querySelector('.transparency-slider').min) + parseInt(this.$root.querySelector('.transparency-slider').max)) / 2;
                    if (Math.abs(this.site.background.transparency - middleValue) <= this.snapThreshold) {
                        this.site.background.transparency = middleValue;
                    }
                },
               setTheme(item){
                  this.site.settings.currentTheme = item.id;
                  this.site.settings = {
                     ...this.site.settings,
                     ...item.config,
                     color: '#' + item.config.color
                  }

                  if(item.background){
                    this.site.background = {
                        ...this.site.background,
                        ...item.background,
                    }

                    if(item.background.image){
                        this.site.background.image = this.assetGs +'/'+item.background.image;
                    }
                  }
               },
               selectHeadFont(item){
                  this.site.settings.fontHeadName = item[0];
                  this.site.settings.fontHeadSettings = item[1];
               },
               filteredHeadFont() {
                  return this.headFont.filter((item) => {
                     var name = item[0].toLowerCase();
                     return name.includes(this.headSearch.toLowerCase());
                  });
               },

               selectBodyFont(item){
                  this.site.settings.fontName = item[0];
                  this.site.settings.fontSettings = item[1];

                  //this.$store.builder.generateFont(this.site, index, this.font);
               },
               filteredBodyFont() {
                  return this.bodyFont.filter((item) => {
                     var name = item[0].toLowerCase();
                     return name.includes(this.bodySearch.toLowerCase());
                  });
               },

               styles(){
                  var site = this.site;
                  return this.$store.builder.generateSiteDesign(site);
               },
               generateGoogleFont(fontName, fontSettings){
                  var href = `https://fonts.googleapis.com/css?family=${fontName}:${fontSettings.variants}`;
                  var styles = document.createElement('link');
                  styles.rel = 'stylesheet';
                  styles.type = 'text/css';
                  styles.href = href;
                  document.getElementsByTagName('head')[0].appendChild(styles);
               },
               init(){
                  var $this = this;

                  this.themes.forEach((element, index) => {
                     this.generateGoogleFont(element.config.fontHeadName, element.config.fontHeadSettings);
                     this.generateGoogleFont(element.config.fontName, element.config.fontSettings);
                  });

                  fetch('/assets/googlefonts/api-response-two.json')
                  .then((rsp) => rsp.json())
                  .then((obj) => {
                     this.headFont = Object.entries(obj);
                     this.bodyFont = Object.entries(obj);
                  });
                      
                  window.addEventListener("appearanceBackgroundBg", (event) => {
                      $this.site.background.image = event.detail.image;
                    //   $this._save();
                  });
                  window.addEventListener("pickr:site.settings.color", (event) => {
                    $this.site.settings.color = event.detail;
                  });
                  window.addEventListener("pickr:site.background.color_1", (event) => {
                    $this.site.background.color_1 = event.detail;
                  });
                  window.addEventListener("pickr:site.background.color_2", (event) => {
                    $this.site.background.color_2 = event.detail;
                  });
                  window.addEventListener("pickr:site.settings.color_two", (event) => {
                    $this.site.settings.color_two = event.detail;
                  });
                  window.addEventListener("pickr:site.settings.section_color", (event) => {
                    $this.site.settings.section_color = event.detail;
                  });

                //   let themeColorPickr = Pickr.create({
                //       el: $this.$refs.themeColorPickr,
                //       default: site.settings.color,
                //       ...window.pickrOptions,
                //   });
                //   themeColorPickr.on('changestop', (source, instance) => {
                //     $this.site.settings.color = instance._color.toHEXA().toString();
                //     themeColorPickr.applyColor();
                //   });

                //   this.$watch('site', (value, _v) => {
                //        //$this.$dispatch('builder::updatePage', $this.currentPage);
                //        // clearTimeout($this.autoSaveTimer);

                //        $this.autoSaveTimer = setTimeout(function(){
                //            $this.$store.builder.savingState = 0;
                //            event = new CustomEvent("builder::saveSite");

                //            window.dispatchEvent(event);
                //        }, $this.$store.builder.autoSaveDelay);
                //    });
               }
         }
      });
   </script>
   @endscript
 </div>