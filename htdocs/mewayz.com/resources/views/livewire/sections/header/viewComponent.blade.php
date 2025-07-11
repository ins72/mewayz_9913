<?php
    use function Livewire\Volt\{state, mount, on, placeholder};

    placeholder('
    <div class="flex items-center">
        <header class="flex w-full h-[60px] justify-center items-center">
            <div class="w-[10%] h-full flex items-center justify-center">
                <div class="--placeholder-skeleton w-[32px] h-[32px] rounded-lg"></div>
            </div>
            <div class="--placeholder-skeleton w-full h-[30px] rounded-sm"></div>
            <div class="w-[10%] h-full flex items-center justify-center">
                <div class="--placeholder-skeleton w-[100px] h-[32px] rounded-sm"></div>
            </div>
        </header>
    </div>');

    state(['site']);
?>


<div wire:ignore>

    <div class="navbar-box focus !relative shadow-none" x-intersect="__section_loaded($el)" x-data="builder__header" :class="{
      'w-fit': site.header.width == 'fit',
      'shadow': site.header.shadow,
      'fixed': site.header.sticky,
      'float': site.header.sticky && site.header._float,
      'glass': site.header.glass,
      'mobile-open': openMobile,
    }" :style="{
      '--logo-height': site.header.logo_width + 'px',
    }">
        <div class="navbar header-box focus">
            
           @foreach(Storage::disk('sections')->files('header/partial/views/header') as $item)
               @php
                   $file = Str::before($item, '.blade.php');
                   $name = basename($file);

                   $header_name = str_replace('header-', '', $name);
                   
                   $component = "livewire::sections.header.partial.views.header.$name";
               @endphp

               <template x-if="site.header.style == '{{$header_name}}'">
                   <x-dynamic-component :component="$component"/>
               </template>
           @endforeach
           
           <div class="mobile-nav" :style="{
            '--logo-height': site.header.logo_width_mobile + 'px',
          }" :class="{
            '!grid lg:!hidden': site.header.sticky && site.header._float,
          }">
              <header>
                  <div class="icon-link">
                     <a x-outlink="site.header.link" @click="openMobile=false" class="logo">
                        <template x-if="site.header.logo_type == 'image'">
                           <div>
                              <template x-if="!site.header.logo">
                                 <div class="default-image light !block">
                                    {!! __i('--ie', 'image-picture', 'text-gray-300') !!}
                                 </div>
                              </template>
                              <template x-if="site.header.logo">
                                 <img :src="$store.builder.getMedia(site.header.logo)" class="site-logo light !block" :class="{'!h-[var(--logo-height)]': site.header.logo_width_mobile}" alt="">
                              </template>
                           </div>
                        </template>
                        <template x-if="site.header.logo_type == 'text' || !site.header.logo_type">
                           <span class="t-2 logo-text" :class="{'text-accent': site.header.logo__c == 'accent'}" x-text="site.header.logo_text ? site.header.logo_text : site.name"></span>
                        </template>
                     </a>
                     <div class="screen"></div>
                  </div>
                 <div class="menu-icon show-theme-btn" @click="openMobile=!openMobile">
                    <div class="screen"></div>
                    <ul class="theme-button" :class="{
                     '!hidden': site.settings.siteTheme == 'light' || site.settings.siteTheme == 'dark'
                    }" x-cloak @click="$event.stopPropagation()">
                        <li class="dark-mode" :class="{
                           '!hidden': siteTheme == 'light',
                        }" @click="toggleLight()">
                           <a name="dark-theme">
                              <i class="ph ph-sun text-lg"></i>
                           </a>
                        </li>
                        <li class="light-mode" :class="{
                           '!hidden': siteTheme == 'dark',
                        }" @click="toggleDark()">
                           <a name="light-theme">
                              <i class="ph ph-moon-stars text-lg"></i>
                           </a>
                        </li>
                    </ul>
                    <div class="site-menu-icon-container" :class="{
                     '!hidden': site.header.hide_mobile_burger
                    }">
                     <span class="site-menu-icon icon-1" :class="{
                        'icon-1': site.header.mobile_burger == '1' || !site.header.mobile_burger,
                        'icon-2': site.header.mobile_burger == '2',
                        'icon-3': site.header.mobile_burger == '3',
                        'open': openMobile,
                     }"></span>
                    </div>
                 </div>
              </header>
              <div class="mobile-nav-overlay bordered !h-[calc(100vh_-_44px_-_var(--logo-height))] !top-[calc(44px_+_var(--logo-height))]" :class="{
               'open': openMobile,
              }">
                 <div class="header-nav">
                    <nav>
                     <ul>
                        <template x-for="item in window._.sortBy(_links, 'position')" :key="item.uuid">
                           <li class="nav__list__link mobile">
                              <a class="link__a" :class="{'chevron': item.children && item.children.length > 0}" @click="openMobile=false" x-outlink="item.link" x-text="item.title"></a>
                              
                              <template x-if="item.children && item.children.length > 0">
                                 <template x-for="child in window._.sortBy(item.children, 'position')" :key="'children' + child.uuid">
                                    <li class="relative sub-link">
                                       <a x-outlink="item.link" @click="openMobile=false" x-text="child.title"></a>
                                    </li>
                                 </template>
                              </template>
                           </li>
                        </template>
                        <div class="screen"></div>
                     </ul>
                    </nav>

                    <div class="button-holder">
                        <template x-if="site.header.button_one_text">
                           <a x-outlink="site.header.button_one_link" class="btn-1">
                              <button class="t-1 shape" x-text="site.header.button_one_text"></button>
                           </a>
                        </template>
                        <template x-if="site.header.button_two_text">
                           <a  x-outlink="site.header.button_two_link" class="btn-2">
                              <button class="t-1 shape" x-text="site.header.button_two_text"></button>
                           </a>
                        </template>
                        <div class="screen"></div>
                    </div>
                 </div>
              </div>
           </div>
        </div>
     </div>

     @script
     <script>
         Alpine.data('builder__header', () => {
            return {
               _links: [],
               openMobile: false,
               init(){
                  var $this = this;
                  this._links = this.siteheader.links;

                  // console.log('--', this.siteheader.links)
                  
                //   var $eventID = 'section::{{-- $section->id --}}';


                  $this.$watch('openMobile', (value) => {
                     if(value){
                        document.querySelector('body').classList.add('overflow-hidden');
                     }else{
                        document.querySelector('body').classList.remove('overflow-hidden')
                     }
                     
                  })

                  window.addEventListener('section::header', (event) => {
                    $this._links = event.detail;
                  });
               }
            }
         });
     </script>
     @endscript
</div>