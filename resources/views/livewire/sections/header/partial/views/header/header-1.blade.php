<div class="header-1 w-full" :class="{
   'w-boxed': site.header.width == 'fit',
   'w-full': site.header.width == 'fill' || !site.header.width,
 }">
   <div class="desktop-nav">
      <header>
         <nav>
            <div class="icon-link">
               <a x-outlink="site.header.link" class="logo yena-site-link">
                  <template x-if="site.header.logo_type == 'image'">
                     <div>
                        <template x-if="!site.header.logo">
                           <div class="default-image light !block">
                              {!! __i('--ie', 'image-picture', 'text-gray-300') !!}
                           </div>
                        </template>
                        <template x-if="site.header.logo">
                           <img :src="$store.builder.getMedia(site.header.logo)" class="site-logo light !block" :class="{'!h-[var(--logo-height)]': site.header.logo_width}" alt="">
                        </template>
                     </div>
                  </template>
                  <template x-if="site.header.logo_type == 'text' || !site.header.logo_type">
                     <span class="t-2 logo-text" :class="{'text-accent': site.header.logo__c == 'accent'}" x-text="site.header.logo_text ? site.header.logo_text : site.name"></span>
                  </template>
               </a>
               <div class="screen"></div>
            </div>
            <ul class="nav__list">
               <template x-for="item in window._.sortBy(_links, 'position')" :key="item.uuid">
                  <li class="nav__list__link">
                     <a class="link__a t-1" :class="{'chevron': item.children && item.children.length > 0}" x-outlink="item.link" x-text="item.title"></a>
                     
                     <template x-if="item.children && item.children.length > 0">
                        <div class="modal page-modal nav__list__sub-links">
                           <div class="mt-1 modal-card">
                              <ul>
                                 <template x-for="child in window._.sortBy(item.children, 'position')" :key="'children' + child.uuid">
                                    <li class="relative sub-link">
                                       <a x-outlink="item.link" x-text="child.title"></a>
                                       <div class="screen"></div>
                                    </li>
                                 </template>
                              </ul>
                           </div>
                        </div>
                     </template>
                     <div class="screen"></div>
                  </li>
               </template>
            </ul>
         </nav>
         <div class="button-holder">
            <x-builder.partials.button prefix="site.header" />
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
            <div class="screen"></div>
         </div>
      </header>
   </div>
</div>