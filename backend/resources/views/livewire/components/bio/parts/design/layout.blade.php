<div class="pt-[26px] px-[36px]">
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

    {{-- flex overflow-x-auto gap-x-2 gap-y-8 mt-0 show-overflowing --}}
    <div class="flex overflow-x-auto gap-3">
            
        @foreach ($banners as $key => $item)
            <div class="text-center cursor-pointer" @click="site.settings.banner='{{ $key }}'" tabindex="0" role="button">
                <div class="flex flex-shrink-0 justify-center items-center bg-white rounded-[10px] text-black h-52 w-32 lg:h-64 lg:w-40 relative border-2" :class="{
                    'border-black': site.settings.banner=='{{ $key }}',
                }">
                <div class="rounded-8 flex flex-col items-center h-[196px] w-[116px] lg:h-[244px] lg:w-[148px] overflow-hidden">
                    @php
                        $banner = "livewire::components.bio.parts.design.banners.$key";
                    @endphp
                    <x-dynamic-component :component="$banner" />


                    {{-- <img alt="user profile avatar" class="mt-6 h-10 w-10 rounded-full" :src="$store.builder.getMedia(site.logo)">
                    <div class="mt-3 text-small-bold" x-text="site.name"></div>
                    <div class="mt-1 flex fill-white">
                        <svg width="200" height="200" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1">
                            <path d="M65 16.6667H135C161.667 16.6667 183.333 38.3334 183.333 65.0001V135C183.333 147.819 178.241 160.113 169.177 169.177C160.113 178.241 147.819 183.333 135 183.333H65C38.3333 183.333 16.6667 161.667 16.6667 135V65.0001C16.6667 52.1813 21.7589 39.8875 30.8232 30.8233C39.8874 21.759 52.1812 16.6667 65 16.6667ZM63.3333 33.3334C55.3768 33.3334 47.7462 36.4941 42.1201 42.1202C36.494 47.7463 33.3333 55.3769 33.3333 63.3334V136.667C33.3333 153.25 46.75 166.667 63.3333 166.667H136.667C144.623 166.667 152.254 163.506 157.88 157.88C163.506 152.254 166.667 144.623 166.667 136.667V63.3334C166.667 46.7501 153.25 33.3334 136.667 33.3334H63.3333ZM143.75 45.8334C146.513 45.8334 149.162 46.9309 151.116 48.8844C153.069 50.8379 154.167 53.4874 154.167 56.2501C154.167 59.0128 153.069 61.6623 151.116 63.6158C149.162 65.5693 146.513 66.6668 143.75 66.6668C140.987 66.6668 138.338 65.5693 136.384 63.6158C134.431 61.6623 133.333 59.0128 133.333 56.2501C133.333 53.4874 134.431 50.8379 136.384 48.8844C138.338 46.9309 140.987 45.8334 143.75 45.8334ZM100 58.3334C111.051 58.3334 121.649 62.7233 129.463 70.5373C137.277 78.3513 141.667 88.9494 141.667 100C141.667 111.051 137.277 121.649 129.463 129.463C121.649 137.277 111.051 141.667 100 141.667C88.9493 141.667 78.3512 137.277 70.5372 129.463C62.7232 121.649 58.3333 111.051 58.3333 100C58.3333 88.9494 62.7232 78.3513 70.5372 70.5373C78.3512 62.7233 88.9493 58.3334 100 58.3334ZM100 75.0001C93.3696 75.0001 87.0107 77.634 82.3223 82.3224C77.6339 87.0108 75 93.3697 75 100C75 106.631 77.6339 112.989 82.3223 117.678C87.0107 122.366 93.3696 125 100 125C106.63 125 112.989 122.366 117.678 117.678C122.366 112.989 125 106.631 125 100C125 93.3697 122.366 87.0108 117.678 82.3224C112.989 77.634 106.63 75.0001 100 75.0001Z"></path>
                        </svg>
                        <svg width="200" height="200" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1">
                            <path d="M107.867 13.4255V131.484C107.867 144.526 97.2917 155.092 84.2583 155.092C71.2167 155.092 60.65 144.517 60.65 131.484C60.65 118.442 71.225 107.876 84.2583 107.876V76.3922C53.8333 76.3922 29.1667 101.059 29.1667 131.484C29.1667 161.909 53.8333 186.576 84.2583 186.576C114.683 186.576 139.35 161.909 139.35 131.484V76.3922L141.008 77.2255C150.267 81.8589 160.475 84.2672 170.825 84.2672V52.7755L169.883 52.5422C151.933 48.0589 139.342 31.9255 139.342 13.4255H107.867Z"></path>
                        </svg>
                        <svg role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1">
                            <title>X</title>
                            <path d="M18.901 1.153h3.68l-8.04 9.19L24 22.846h-7.406l-5.8-7.584-6.638 7.584H.474l8.6-9.83L0 1.154h7.594l5.243 6.932ZM17.61 20.644h2.039L6.486 3.24H4.298Z"></path>
                        </svg>
                    </div>
                    <div class="flex flex-col w-5/6 mt-4 gap-2">
                        <div class="flex w-[100%] h-6 border border-solid border-gray-200 rounded-4"></div>
                        <div class="flex w-[100%] h-6 border border-solid border-gray-200 rounded-4"></div>
                        <div class="flex w-[100%] h-6 border border-solid border-gray-200 rounded-4"></div>
                    </div> --}}
                </div>
                </div>
                {{-- <div class="mt-2">Classic</div>
                <span class="MuiButtonBase-root MuiRadio-root MuiRadio-colorDefault PrivateSwitchBase-root MuiRadio-root MuiRadio-colorDefault Mui-checked MuiRadio-root MuiRadio-colorDefault p-1 css-11hcv7q">
                <input class="PrivateSwitchBase-input css-1m9pwf3" type="radio" value="" checked="">
                <span class="css-hyxlzm">
                    <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeMedium css-q8lw68" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="RadioButtonUncheckedIcon">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"></path>
                    </svg>
                    <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeMedium css-1u5ei5s" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="RadioButtonCheckedIcon">
                        <path d="M8.465 8.465C9.37 7.56 10.62 7 12 7C14.76 7 17 9.24 17 12C17 13.38 16.44 14.63 15.535 15.535C14.63 16.44 13.38 17 12 17C9.24 17 7 14.76 7 12C7 10.62 7.56 9.37 8.465 8.465Z"></path>
                    </svg>
                </span>
                <span class="MuiTouchRipple-root css-w0pj6f"></span>
                </span> --}}
            </div>
            {{-- <label class="sandy-big-checkbox o-checkbox !hidden">
                <input value="{{ $key }}" class="sandy-input-inner" type="radio" name="setting[banner]" x-model="site.settings.banner" id="theme_{{ md5("banner-key-$key") }}">
                <div class="checkbox-inner h-full p-1 rounded-3xl w-36">
                    <div class="fancy-ooo-card w-[100%] rounded-xl -op-lol">
                        <div class="h-full">
                            <div class="content ml-0 w-[100%] z-50 relative">
                                <div class="relative w-[100%] rounded shadow-sm border- cursor-pointer p---2 flex flex-col p-2" for="theme_{{ md5("banner-key-$key") }}">
                                    @includeIf('mix::livewire.builder.banners.' . $key)
                                </div>
                            </div>
                            <div class="-svg-container">
                                {!! __i('Design Tools', 'Mesh Tool', 'h-full w-[100%]') !!}
                            </div>
                            <div class="--first--card w-[100%]"></div>
                            <div class="--second--card w-[100%]"></div>
                        </div>
                    </div>
                </div>
            </label> --}}
        @endforeach
    </div>
</div>