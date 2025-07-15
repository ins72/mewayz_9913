
<?php
    
    use App\Models\BioSiteSocial;
    use function Livewire\Volt\{state, mount, placeholder, updated, on};
    state(['site']);
?>
<div class="banner-section !block">
   <div x-data="builder__edit_layout" wire:ignore>

       <div class="banner-navbar">
           <ul >
               <li class="close-header">
                  <a @click="__page='-'">
                     <span>
                        {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                     </span>
                  </a>
               </li>
               <li>{{ __('Edit Layout') }}</li>
               <li></li>
           </ul>
       </div>
       <div class="sticky container-small !hidden"></div>
       <div class="container-small tab-content-box">
           <div class="tab-content">
               <div x-cloak data-tab-content>
                  <div>
                     <div class="mt-2 content">
                         <div class="panel-input mb-1 px-[var(--s-2)]">
                            <div>
                                <div class="flex items-center justify-between normal-case mb-5">
                                    <span class="text-xl font-extrabold tracking-[-1px]">{{ __('Enable Banner') }}</span>
                                    <label class="sandy-switch">
                                        <input class="sandy-switch-input" name="settings[enable_cover]"  x-model="site.settings.enable_cover" value="1" type="checkbox">
                                        <span class="sandy-switch-in"><span class="sandy-switch-box is-white"></span></span>
                                    </label>
                                </div>
                                
                                <div class="relative block h-20 mb-1 text-center border-2 border-dashed rounded-lg cursor-pointer group bg-white- hover:border-solid hover:border-yellow-600 mt-5" :class="{
                                    'border-gray-200': !site.banner,
                                    'border-transparent': site.banner,
                                   }">
                                    <template x-if="site.banner">
                                       <div class="absolute top-0 bottom-0 left-0 right-0 items-center justify-center hidden w-[100%] h-full group-hover:flex">
                                          <div class="flex items-center justify-center w-8 h-8 bg-white rounded-full icon-shadow" @click="site.banner = ''; $dispatch('sectionMediaEvent:banner', {
                                            image: null,
                                            public: null,
                                           })">
                                              <i class="fi fi-rr-trash"></i>
                                          </div>
                                      </div>
                                    </template>
                                    <template x-if="!site.banner">
                                       <div class="flex items-center justify-center w-[100%] h-full" @click="openMedia({
                                            event: 'sectionMediaEvent:banner',
                                            sectionBack:'navigatePage(\'__last_state\')'
                                        });">
                                           <div>
                                               <span class="m-0 -mt-2 text-black loader-line-dot-dot font-2"></span>
                                           </div>
                                           <i class="fi fi-ss-plus"></i>
                                       </div>
                                    </template>
                                    <template x-if="site.banner">
                                       <div class="h-full w-[100%]">
                                           <img :src="$store.builder.getMedia(site.banner)" class="h-full w-[100%] object-cover rounded-md" alt="">
                                       </div>
                                    </template>
                                </div>
                                <div class="flex flex-col justify-between normal-case my-5">
                                    <span class="text-xl font-extrabold tracking-[-1px]">{{ __('Alignment') }}</span>
                                </div>
                                <div class="grid grid-cols-3 gap-4">
                                    @foreach (['left' => 'Left', 'center' => 'Center', 'right' => 'Right'] as $key => $value)
                                    <label class="sandy-big-checkbox is-bio-radius">
                                        <input type="radio" x-model="site.settings.align" class="sandy-input-inner" name="settings[bio_align]"
                                            value="{{ $key }}">
                                        <div class="checkbox-inner !p-3 !h-10 !border-2 !border-dashed !border-color--hover">
                                            <div class="checkbox-wrap">
                                                <div class="content !flex">
                                                    <h1 class="!my-auto">{{ __($value) }}</h1>
                                                </div>
                                                <div class="icon">
                                                    <div class="active-dot rounded-lg w-5 h-5">
                                                    <i class="la la-check text-xs"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                                
                                <div class="flex flex-col justify-between normal-case my-5">
                                    <span class="text-xl font-extrabold tracking-[-1px]">{{ __('Header text size') }}</span>
                                </div>
                                
                            
                                <div class="flex gap-2">
                                    <div tabindex="0" role="button" x-on:click="site.settings.header_fontsize = 's'" :class="{
                                        'border-black text-black': site.settings.header_fontsize == 's',
                                        'border-gray-400 text-gray-400': site.settings.header_fontsize !== 's'
                                        }" class="cursor-pointer box-border font-semibold flex items-center justify-center h-12 w-12 border-[2px] border-solid rounded-8">S</div>
                                    <div tabindex="0" role="button" x-on:click="site.settings.header_fontsize = 'm'" :class="{
                                        'border-black text-black': site.settings.header_fontsize == 'm',
                                        'border-gray-400 text-gray-400': site.settings.header_fontsize !== 'm'
                                        }" class="cursor-pointer box-border font-semibold flex items-center justify-center h-12 w-12 border-[2px] border-solid rounded-8">M</div>
                                    <div tabindex="0" role="button" x-on:click="site.settings.header_fontsize = 'l'" :class="{
                                        'border-black text-black': site.settings.header_fontsize == 'l',
                                        'border-gray-400 text-gray-400': site.settings.header_fontsize !== 'l'
                                        }" class="cursor-pointer box-border font-semibold flex items-center justify-center h-12 w-12 border-[2px] border-solid rounded-8">L</div>
                                </div>
                                <div class="flex flex-col justify-between normal-case my-5">
                                    <span class="text-xl font-extrabold tracking-[-1px]">{{ __('Profile size') }}</span>
                                </div>
                            
                                <div class="input-group flex-[70%] flex relative">
                                    <input type="range" class="input-small range-slider appearance-none h-[calc(.625rem_*_4)] outline-[none] p-0 overflow-hidden rounded-[calc(.625rem_/_2)] text-[14px] w-[100%] block bg-[unset] text-[color:#111] border border-solid border-[#eee] leading-[1.6] relative [box-shadow:none]" min="40" max="200" step="1" x-model="site.settings.avatar_size">
                                    <p class="absolute top-[10px] right-[12px] text-[14px] leading-[1.6] pointer-events-none capitalize" x-text="site.settings.avatar_size ? site.settings.avatar_size + 'px' : ''"></p>
                                </div>
                                <div class="flex flex-col justify-between normal-case my-5">
                                    <span class="text-xl font-extrabold tracking-[-1px]">{{ __('Social spacing') }}</span>
                                </div>
                            
                                <div class="input-group flex-[70%] flex relative">
                                    <input type="range" class="input-small range-slider appearance-none h-[calc(.625rem_*_4)] outline-[none] p-0 overflow-hidden rounded-[calc(.625rem_/_2)] text-[14px] w-[100%] block bg-[unset] text-[color:#111] border border-solid border-[#eee] leading-[1.6] relative [box-shadow:none]" min="16" max="50" step="4" x-model="site.settings.social_spacing">
                                    <p class="absolute top-[10px] right-[12px] text-[14px] leading-[1.6] pointer-events-none capitalize" x-text="site.settings.social_spacing ? site.settings.social_spacing + 'px' : ''"></p>
                                </div>
                            
                                <div class="flex flex-col justify-between normal-case my-5">
                                    <span class="text-xl font-extrabold tracking-[-1px]">{{ __('Corners') }}</span>
                                </div>
                            
                                <div class="grid grid-cols-3 gap-4">
                                    @foreach (['straight' => ['class' => '!rounded-none', 'name' => 'Straight'], 'round' => ['class' => '!rounded-2xl', 'name' => 'Rounded'], 'rounded' => ['class' => '!rounded-full', 'name' => 'Round']] as $key => $value)
                                    <label class="sandy-big-checkbox is-bio-radius">
                                       <input type="radio" class="sandy-input-inner" name="settings[radius]"
                                       value="{{ $key }}" x-model="site.settings.corners">
                                       <div
                                          class="checkbox-inner {{ ao($value, 'class') }} !p-3 !h-10 !border-2 !border-dashed !border-color--hover">
                                          <div class="checkbox-wrap"><div class="content">
                                                <a class="leo-avatar-o !bg-white !rounded-md">
                                                  <div class="-avatar-inner !bg-gray-100 !p-0 !flex-none">
                                                     <div class="--avatar !p-0 !rounded-md !flex !items-center !justify-center">
                                                        <img src="{{ gs("assets/image/others/corner-$key.png") }}" alt="">
                                                     </div>
                                                  </div>
                                                  <h1 class="text-sm hidden">{{ __(ao($value, 'name')) }}</h1>
                                               </a>
                                             </div>
                                             <div class="icon">
                                                <div class="active-dot rounded-lg w-5 h-5">
                                                   <i class="la la-check text-xs"></i>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                    </label>
                                    @endforeach
                                </div>

                                <div class="flex flex-col justify-between normal-case my-5">
                                    <span class="text-xl font-extrabold tracking-[-1px]">{{ __('Theme') }}</span>
                                </div>
                                <div class="style">
                                    <div class="style-block site-layout !grid-cols-3 !p-0 custom-styles-link">
                                        <template x-for="(theme, index) in studio.links.themes" :key="index">
                                            <button class="btn-layout" :class="{
                                                'active': site.settings.__theme == theme
                                            }" type="button" @click="site.settings.__theme = theme">
                                                <span x-text="(index + 1)"></span>
                                                
                                                <div class="w-[100%] h-10 bg-gray-200 p-3 rounded-lg relative"
                                                     :class="{
                                                         [`--${theme}`]: true,
                                                         '!bg-white': site.settings.__theme == theme
                                                     }"></div>
                                            </button>
                                        </template>
                                    </div>
                                </div>

                                <div class="flex flex-col justify-between normal-case my-5">
                                    <span class="text-xl font-extrabold tracking-[-1px]">{{ __('Avatar') }}</span>
                                </div>
                                <div class="style">
                                    <div class="style-block site-layout !grid-cols-3 !p-0 pattern-image">
                                        <template x-for="(theme, index) in studio.pattern.themes" :key="index">
                                            <button class="btn-layout" :class="{
                                                'active': site.settings.__pattern_theme == theme
                                            }" type="button" @click="site.settings.__pattern_theme = theme">
                                                <span x-text="(index + 1)"></span>
                                                
                                                <div class="w-[100%] h-10 bg-gray-200 p-3 rounded-lg relative"
                                                     :class="{
                                                         [`--${theme}`]: true,
                                                         '!bg-white': site.settings.__pattern_theme == theme
                                                     }"></div>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            
                                <div class="flex flex-col justify-between normal-case my-5">
                                    <span class="text-xl font-extrabold tracking-[-1px]">{{ __('Page navigation') }}</span>
                                </div>
                            
                                <div class="style-block site-layout !grid-cols-4 !p-0 mb-5">
                                    <button class="btn-layout" :class="{
                                          'active': site.settings.tab_style == 'tabs_on_top'
                                       }" @click="site.settings.tab_style = 'tabs_on_top'">
                                          {{-- <span>{{ __('Tabs on top') }}</span> --}}
                                          <img alt="" src="{{ gs('assets/image/others/pagenav', 'top_tabs_navigation.webp') }}">
                                    </button>
                                    <button class="btn-layout" :class="{
                                          'active': site.settings.tab_style == 'tabs_on_bottom'
                                       }" @click="site.settings.tab_style = 'tabs_on_bottom'">
                                          {{-- <span>{{ __('Tabs on bottom') }}</span> --}}
                                          <img alt="" src="{{ gs('assets/image/others/pagenav', 'bottom_tabs_navigation.webp') }}">
                                    </button>
                                    <button class="btn-layout" :class="{
                                          'active': site.settings.tab_style == 'hamburger'
                                       }" @click="site.settings.tab_style = 'hamburger'">
                                          {{-- <span>{{ __('Hamburger') }}</span> --}}
                                          <img alt="" src="{{ gs('assets/image/others/pagenav', 'hamburger_menu_navigation.webp') }}">
                                    </button>
                                 </div>
                            
                                <div class="flex flex-col justify-between normal-case my-5">
                                    <span class="text-xl font-extrabold tracking-[-1px]">{{ __('Layout') }}</span>
                                </div>
                                <div class="d-swiper-o">
                                    <div class="swiper trending-slider">
                                        <div class="swiper-wrapper">
                                            <!-- SLide start -->
                                            @foreach (config('bio.layout-banners') as $key => $item)
                                            <div class="swiper-slide trending-slide" :class="{
                                                '--active': site.settings.banner=='{{ $key }}',
                                            }" data-id="{{ $key }}">
                                                @php
                                                    $banner = "livewire::components.bio.parts.design.banners.$key";
                                                @endphp
                                                <div class="trending-slide-img h-full border-[2px] border-solid border-[#bbb] rounded-[20px] bg-[#fff] p-2" :class="{
                                                    '!border-black': site.settings.banner=='{{ $key }}',
                                                }">
                                                    <x-dynamic-component :component="$banner" />
                                                </div>
                                            </div>
                                            @endforeach
                                            <!-- SLide end -->
                                        </div>
                            
                                        <div class="trending-slider-control">
                                            
                                                <div class="swiper-button-prev slider-arrow">
                                                    <i class="ph ph-caret-left"></i>
                                                </div>
                                                <div class="swiper-button-next slider-arrow">
                                                    <i class="ph ph-caret-right"></i>
                                                </div>
                                            
                                            <div class="swiper-pagination"></div>
                                        </div>
                                    </div>
                                </div>
                            
                                {{-- flex overflow-x-auto gap-x-2 gap-y-8 mt-0 show-overflowing --}}
                                {{-- <div class="flex overflow-x-auto gap-3">
                                        
                                    @foreach (config('bio.layout-banners') as $key => $item)
                                        <div class="text-center cursor-pointer" @click="site.settings.banner='{{ $key }}'" tabindex="0" role="button">
                                            <div class="flex flex-shrink-0 justify-center items-center bg-white rounded-[10px] text-black h-52 w-32 lg:h-64 lg:w-40 relative border-2" :class="{
                                                'border-black': site.settings.banner=='{{ $key }}',
                                            }">
                                            <div class="rounded-8 flex flex-col items-center h-[196px] w-[116px] lg:h-[244px] lg:w-[148px] overflow-hidden">
                                                @php
                                                    $banner = "livewire::components.bio.parts.design.banners.$key";
                                                @endphp
                                                <x-dynamic-component :component="$banner" />
                                            </div>
                                         </div>
                                        </div>
                                    @endforeach
                                </div> --}}
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
      Alpine.data('builder__edit_layout', () => {
         return {
            autoSaveTimer: null,
            studio: {!! collect(config('yena.studio'))->toJson() !!},
            layoutBanners: {!! collect(config('bio.layout-banners'))->toJson() !!},

            _save(){
                var $this = this;

               //  $this.$dispatch($eventID, $this.section);
                clearTimeout($this.autoSaveTimer);

                $this.autoSaveTimer = setTimeout(function(){
                    $this.$store.builder.savingState = 0;
                    event = new CustomEvent("builder::saveSite");

                    window.dispatchEvent(event);
                }, $this.$store.builder.autoSaveDelay);
            },

            init(){
               
               let $this = this;
               window.addEventListener("sectionMediaEvent:logo", (event) => {
                   $this.site.logo = event.detail.image;
                   $this._save();
               });
               window.addEventListener("sectionMediaEvent:banner", (event) => {
                   $this.site.banner = event.detail.image;
                   $this._save();
               });
            //    let initialSlide = Array.from($this.$root.querySelectorAll('.swiper-slide')).findIndex(element => element.classList.contains('--active')) || 0;

            //    console.log(initialSlide);

                let $swiper = $this.$root.querySelector('.trending-slider');
                let slider = new window.Swiper($swiper, {
                    effect: 'coverflow',
                    grabCursor: true,
                    centeredSlides: true,
                    loop: true,
                    slidesPerView: 'auto',
                    coverflowEffect: {
                        rotate: 0,
                        stretch: 0,
                        depth: 100,
                        modifier: 2.5,
                    },
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true,
                    },
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev',
                    },
                    on: {
                        afterInit: function(swiper) {
                            const swiperSlides = $this.$root.querySelectorAll('.swiper-slide');
                            const selectedBanner = $this.site.settings.banner;
                            swiper.slideTo(3);

                            // Convert object to an array of its entries
                            Object.entries($this.layoutBanners).forEach(([key, element]) => {
                                if (selectedBanner == key) {
                                    swiperSlides.forEach(slide => {
                                        if (slide.getAttribute('data-id') == key) {
                                            swiper.slideTo(slide.getAttribute('data-swiper-slide-index'));
                                        }
                                    });
                                }
                            });
                        }
                    }
                });

                slider.on('slideChange', function (e) {
                    let swiper_slide = $this.$root.querySelectorAll('.swiper-slide');
                    let el = swiper_slide[slider.activeIndex];
                    let _id = el.getAttribute('data-id');
                    
                    $this.site.settings.banner = _id;
                });

                slider.on('afterInit', function (e) {
                    const swiperSlides = $this.$root.querySelectorAll('.swiper-slide');
                    const selectedBanner = $this.site.settings.banner;
                    slider.slideTo(3);
                    console.log('key', $this.layoutBanners)

                    $this.layoutBanners.forEach((element, key) => {
                        console.log(key)
                        if (selectedBanner == key) {
                            console.log('selec- ', key)
                            swiperSlides.forEach(slide => {
                                if (slide.getAttribute('data-id') == key) {
                                    console.log(slide.getAttribute('data-swiper-slide-index'))
                                    slider.slideTo(slide.getAttribute('data-swiper-slide-index'));
                                }
                            });
                        }
                    });
                });
            }
         }
      });
   </script>
   @endscript
 </div>