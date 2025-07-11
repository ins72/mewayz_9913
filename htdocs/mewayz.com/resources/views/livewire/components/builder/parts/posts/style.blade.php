<div>
    <div class="style">
       <div class="style-block site-layout">
 
          <template x-for="(item, index) in __banners" :key="index">
             <button class="btn-layout" :class="{
                'active': post.settings.banner_style == index
             }" @click="post.settings.banner_style = index; getBannerConfig();">
                <span x-text="index"></span>
                <div x-html="item.preview"></div>
                {{-- @includeIf("livewire.sections.banner.partial.edit.banner.banner_$key") --}}
             </button>
          </template>
       </div>
       <div class="banner-text-size">
          <form>
             <template x-if="post.settings.enable_image">
                <div class="input-box">
                   <label for="text-size">{{ __('Image') }}</label>
                   <div class="input-group two-col">
                      <button class="btn-nav" type="button" :class="{
                         'active': post.settings.image_type == 'fill',
                      }" @click="post.settings.image_type = 'fill'"
                      >{{ __('Fill') }}</button>
    
                      <button class="btn-nav" type="button" :class="{
                         'active': post.settings.image_type == 'fit',
                      }" @click="post.settings.image_type = 'fit'"
                      >{{ __('Fit') }}</button>
                   </div>
                </div>
             </template>
 
             <div class="input-box" :class="{
                '!hidden': post.settings.image_type !== 'fit' || !post.settings.enable_image,
             }">
                <label for="text-size">{{ __('Color') }}</label>
                <div class="input-group two-col" id="color">
                   <button class="btn-nav" :class="{
                      'active' : post.settings.color == 'transparent',
                   }" type="button" @click="post.settings.color = 'transparent'">
                      {!! __i('--ie', 'delete-disabled-ross-hexagon.1', 'w-5 h-5') !!}
                   </button>
                   <button class="btn-nav" :class="{
                      'active' : post.settings.color == 'default',
                   }" type="button" @click="post.settings.color = 'default'">
                         <span class="w-4 h-4 rounded-full bg-[#ccc]"></span>
                   </button>
                   <button class="btn-nav" :class="{
                      'active' : post.settings.color == 'accent',
                   }" type="button" @click="post.settings.color = 'accent'">
                         <span class="w-4 h-4 rounded-full bg-[var(--accent)]"></span>
                   </button>
                </div>
             </div>
 
             
             <div class="input-box">
                <label for="text-size">{{__('Title')}}</label>
                <div class="input-group align-type">
                   <button class="btn-nav" type="button" :class="{'active': post.settings.title == 'xs'}" @click="post.settings.title = 'xs'">xs</button>
                   <button class="btn-nav" type="button" :class="{'active': post.settings.title == 's'}" @click="post.settings.title = 's'">S</button>
                   <button class="btn-nav" type="button" :class="{'active': post.settings.title == 'm'}" @click="post.settings.title = 'm'">M</button>
                   <button class="btn-nav" type="button" :class="{'active': post.settings.title == 'l'}" @click="post.settings.title = 'l'">L</button>
                   <button class="btn-nav" type="button" :class="{'active': post.settings.title == 'xl'}" @click="post.settings.title = 'xl'">XL</button>
                </div>
             </div>
 
             
             <div class="input-box" :class="{
                '!hidden': !__bannerConfig.align
             }">
                <label for="text-size">{{ __('Align') }}</label>
                <div class="input-group align-type">
                   <button class="btn-nav" :class="{'active': post.settings.align == 'left'}" type="button" @click="post.settings.align = 'left'">
                      {!! __i('Type, Paragraph, Character', 'align-left') !!}
                   </button>
                   <button class="btn-nav" :class="{'active': post.settings.align == 'center'}" type="button" @click="post.settings.align = 'center'">
                      {!! __i('Type, Paragraph, Character', 'align-center') !!}
                   </button>
                   <button class="btn-nav" :class="{'active': post.settings.align == 'right'}" type="button" @click="post.settings.align = 'right'">
                      {!! __i('Type, Paragraph, Character', 'align-right') !!}
                   </button>
                </div>
             </div>
 
             
             <template x-if="post.settings.banner_style == '1' || post.settings.banner_style == '2' || !post.settings.enable_image">
                <div class="input-box">
                   <label for="text-size">{{__('Width')}}</label>
                   <div class="input-group">
                      <input type="range" class="input-small range-slider" :min="__bannerConfig.minWidth ? __bannerConfig.minWidth : 50" :max="__bannerConfig.maxWidth ? __bannerConfig.maxWidth : 100" step="1" x-model="post.settings.width">
                      <p class="image-size-value" x-text="post.settings.width + '%'"></p>
                   </div>
                </div>
             </template>
 
 
             <div class="input-box">
                <label for="text-size">{{__('Height')}}</label>
                <div class="input-group">
                   <input type="range" class="input-small range-slider" min="200" max="700" step="10" x-model="post.settings.height">
                   <p class="image-size-value" x-text="post.settings.height + '%'"></p>
                </div>
             </div>
 
             <div class="columns">
                <div class="input-box">
                   <div class="input-label">{{ __('Shape') }}</div>
                   <div class="input-group">
                      <button class="btn btn-column !flex items-center justify-center text-center" :class="{'active': post.settings.shape == 'circle'}" type="button" @click="post.settings.shape = 'circle'">
                         {!! __i('Basic Shapes', 'Circle') !!}
                      </button>
                      <button class="btn btn-column !flex items-center justify-center text-center" :class="{'active': post.settings.shape == 'square'}" type="button" @click="post.settings.shape = 'square'">
                         {!! __i('Basic Shapes', 'Square', 'sfs[&>*]:stroke-transparent') !!}
                      </button>
                   </div>
                </div>
             </div>
 
             <div class="mt-2 input-box banner-advanced banner-image">
                <div class="input-group">
                   <div class="switchWrapper">
                      <input id="showImage-switch" type="checkbox" x-model="post.settings.enable_image" class="switchInput">
                      <label for="showImage-switch" class="switchLabel">{{ __('Enable Image') }}</label>
                      <div class="slider"></div>
                   </div>
                </div>
             </div>
             <template x-if="__bannerConfig.split">
                <div class="input-box banner-advanced banner-action">
                   <div class="input-group">
                      <div class="switchWrapper">
                         <input id="splitSection-switch" type="checkbox" x-model="post.settings.split_title" class="switchInput">
    
                         <label for="splitSection-switch" class="switchLabel">{{ __('Split Section') }}</label>
                         <div class="slider"></div>
                      </div>
                   </div>
                </div>
             </template>
             
             <div class="cursor-pointer input-box banner-advanced banner-action" @click="__page = 'section'">
                <div class="input-group" >
                   <div class="section-background" >
                      <label for="showSection-switch" class="" >{{ __('Section Background') }}</label>
                      <span>
                         {!! __icon('Arrows, Diagrams', 'Arrow.5', 'w-5 h-5') !!}
                      </span>
                   </div>
                </div>
             </div>
          </form>
       </div>
    </div>
</div>