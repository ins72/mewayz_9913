
<div>
   <div class="style">
      <div class="style-block site-layout !grid-cols-2">
         <template x-for="(item, index) in __header" :key="index">
            <button class="btn-layout" :class="{
               'active': site.header.style == index+1
            }" @click="site.header.style = index+1;">
               <span x-text="index+1"></span>
               <div x-html="item"></div>
            </button>
         </template>
      </div>
      <div class="banner-text-size">
         <form>
            <template x-if="site.header.logo_type == 'image'">
               <div class="input-box" x-data="{__showing: 'desktop'}">
                  <label for="text-size">{{__('Logo')}}</label>
                  <div class="input-group">
                     <button type="button" class="btn !w-[40px] !h-[40px] !min-w-[40px] !aspect-square !flex !items-center !justify-center !bg-[var(--c-mix-1)] !rounded-tr-none !rounded-br-none" @click="__showing = __showing == 'mobile' ? 'desktop' : 'mobile'">
                        <i class="text-sm fi fi-rr-computer text-[var(--foreground)]" x-cloak x-show="__showing == 'desktop'"></i>
                        <i class="text-sm fi fi-rr-mobile-notch text-[var(--foreground)]" x-show="__showing == 'mobile'"></i>
                     </button>
                     <input type="range" class="input-small range-slider" :class="{'!hidden': __showing !== 'desktop'}" min="10" max="100" step="1" x-model="site.header.logo_width">
                     <input type="range" class="input-small range-slider" :class="{'!hidden': __showing !== 'mobile'}" min="10" max="60" step="1" x-model="site.header.logo_width_mobile">
                     <p class="image-size-value" x-text="__showing == 'desktop' ? (site.header.logo_width ? site.header.logo_width  + 'px' : '') : (site.header.logo_width_mobile ? site.header.logo_width_mobile  + 'px' : '')"></p>
                  </div>
               </div>
            </template>
            <template x-if="site.header.logo_type == 'text'">
               <div class="input-box">
                  <label for="text-size">{{ __('Logo') }}</label>
                  <div class="input-group two-col" id="color">
                     <button class="btn-nav" :class="{
                        'active' : site.header.logo__c == 'default',
                     }" type="button" @click="site.header.logo__c = 'default'">
                        <span class="w-4 h-4 rounded-full bg-[#000]"></span>
                     </button>
                     <button class="btn-nav" :class="{
                        'active' : site.header.logo__c == 'accent',
                     }" type="button" @click="site.header.logo__c = 'accent'">
                        <span class="w-4 h-4 rounded-full bg-[var(--accent)]"></span>
                     </button>
                  </div>
               </div>
            </template>
            <div class="input-box">
               <label for="text-size">{{__('Width')}}</label>
               <div class="input-group two-col">
                  <button class="btn-nav" type="button" :class="{
                     'active': site.header.width == 'fill',
                  }" @click="site.header.width = 'fill'"
                  >{{ __('Fill') }}</button>

                  <button class="btn-nav" type="button" :class="{
                     'active': site.header.width == 'fit',
                  }" @click="site.header.width = 'fit'"
                  >{{ __('Fit') }}</button>
               </div>
            </div>
            <div class="input-box">
               <label for="text-size">{{__('Mobile Burger')}}</label>
               <div class="input-group align-type">
                  <button class="btn-nav" type="button" :class="{'active': site.header.mobile_burger == '1'}" @click="site.header.mobile_burger = '1'">
                     {!! __i('--ie', 'menu-burger-square.2', '!w-5 !h-5') !!}
                  </button>
                  <button class="btn-nav" type="button" :class="{'active': site.header.mobile_burger == '2'}" @click="site.header.mobile_burger = '2'">
                     {!! __i('--ie', 'menu-burger-square.5', '!w-5 !h-5') !!}
                  </button>
                  <button class="btn-nav" type="button" :class="{'active': site.header.mobile_burger == '3'}" @click="site.header.mobile_burger = '3'">
                     {!! __i('--ie', 'menu-burger-square', '!w-5 !h-5') !!}
                  </button>
               </div>
            </div>
            <div class="input-box banner-advanced banner-action">
               <div class="input-group">
                  <div class="switchWrapper">
                     <input id="hide-mobile-switch" type="checkbox" x-model="site.header.hide_mobile_burger" class="switchInput">

                     <label for="hide-mobile-switch" class="switchLabel">{{ __('Hide mobile burger') }}</label>
                     <div class="slider"></div>
                  </div>
               </div>
            </div>
            <div class="input-box banner-advanced banner-action">
               <div class="input-group">
                  <div class="switchWrapper">
                     <input id="Sticky-switch" type="checkbox" x-model="site.header.sticky" x-on:input="!$event.target.checked && site.header._float ? site.header._float = false : ''" class="switchInput">

                     <label for="Sticky-switch" class="switchLabel">{{ __('Sticky') }}</label>
                     <div class="slider"></div>
                  </div>
               </div>
            </div>
            <div class="input-box banner-advanced banner-action">
               <div class="input-group">
                  <div class="switchWrapper">
                     <input id="Float-switch" type="checkbox" x-model="site.header._float" x-on:input="$event.target.checked && !site.header.sticky ? site.header.sticky = true : ''" class="switchInput">

                     <label for="Float-switch" class="switchLabel">{{ __('Float') }}</label>
                     <div class="slider"></div>
                  </div>
               </div>
            </div>
            <div class="input-box banner-advanced banner-action">
               <div class="input-group">
                  <div class="switchWrapper">
                     <input id="Shadow-switch" type="checkbox" x-model="site.header.shadow" x-on:input="$event.target.checked && site.header.glass ? site.header.glass = false : ''" class="switchInput">

                     <label for="Shadow-switch" class="switchLabel">{{ __('Shadow') }}</label>
                     <div class="slider"></div>
                  </div>
               </div>
            </div>
            <div class="input-box banner-advanced banner-action">
               <div class="input-group">
                  <div class="switchWrapper">
                     <input id="Glass-switch" type="checkbox" x-model="site.header.glass" x-on:input="$event.target.checked && site.header.shadow ? site.header.shadow = false : ''" class="switchInput">

                     <label for="Glass-switch" class="switchLabel">{{ __('Glass') }}</label>
                     <div class="slider"></div>
                  </div>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>