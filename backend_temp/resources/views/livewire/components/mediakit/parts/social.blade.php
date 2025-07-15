<div class="builder-socials relative z-10 gap-4 w-[100%] overflow-x-auto flex flex-row" :class="{
    'justify-start': site.settings.align == 'left',
    'justify-center': site.settings.align == 'center' || !site.settings.align,
    'justify-end': site.settings.align == 'right',
 }" :style="{
    'gap': site.settings.social_spacing + 'px'
 }">
    <template x-for="(social, index) in window._.sortBy(site.socials, 'position')" :key="index">
       <div class="builder-block">
          <div class="-item-style" :class="{
             '!rounded-none': site.settings.corners == 'straight',
             '!rounded-xl': site.settings.corners == 'round',
             '!rounded-full': site.settings.corners == 'rounded',
          }">
             <div class="--style">

                <a class="!w-10 !h-10 flex items-center justify-center -socials-item m-0" :class="{
                   '!rounded-none': site.settings.corners == 'straight',
                   '!rounded-xl': site.settings.corners == 'round',
                   '!rounded-full': site.settings.corners == 'rounded',
                }" x-outlink="social.link" target="_blank">
                   <i :class="socials[social.social].icon"></i>
                </a>
             </div>
             <div class="--item--bg"></div>
          </div>
       </div>
    </template>
 </div>