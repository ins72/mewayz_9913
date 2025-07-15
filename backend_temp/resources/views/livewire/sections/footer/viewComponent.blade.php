<div wire:ignore>
    <div class="yena-footer footer-1 box v_2-footer focus [&_.footer-3 .footer-text.v_2-footer]" x-intersect="__section_loaded($el)" x-data="builder__footer" :class="{
        'footer-1': site.footer.style == '1',
        'footer-2': site.footer.style == '2',
        'footer-3': site.footer.style == '3',
        'footer-4': site.footer.style == '4',
    }" :style="{
        '--logo-height': site.header.logo_width + 'px',
      }">
        <div class="footer-card w-boxed">
           <div class="footer-top">
              <div class="footer-top__nav-container">
                <template x-if="footerGroups && footerGroups.length > 0">
                    <div class="footer-nav mb-2 v_2-footer">
                        <ul class="links">
                            <template x-for="(group, index) in window._.sortBy(footerGroups, 'position')" :key="group.uuid">
                                <li class="link-group" :class="{
                                    'empty-group': group.links.length == 0,
                                }" :style="`--content-count: ${group.links.length}`">
                                    <span class="t-0 group__heading !relative">
                                        <span x-text="group.title"></span>
                                        <div class="screen"></div>
                                    </span>
                                    <ul class="group__sub-links">
                                        <template x-if="group.links || group.links.length > 0">
                                            <template x-for="(item, index) in window._.sortBy(group.links, 'position')" :key="item.uuid">
                                                <li class="group__sub-link">
                                                    <a x-outlink="item.link" x-text="item.title"></a>
                                                    <div class="screen"></div>
                                                </li>
                                            </template>
                                        </template>
                                    </ul>
                                </li>
                            </template>

                        </ul>
                    </div>
                </template>
                 <template x-if="site.socials && site.socials.length > 0">
                    <div class="social-media-link">
                        <ul>
                            <template x-for="(social, index) in window._.sortBy(site.socials, 'position')" :key="index">
                                <template x-if="true">
                                    <li class="relative">
                                        <a class="shape" target="_blank" :href="socials[social.social].address.replace('%s', social.link)">
                                            <i :class="socials[social.social].icon"></i>
                                        </a>
                                        <div class="screen"></div>
                                    </li>
                                </template>
                            </template>
                        </ul>
                    </div>
                </template>
              </div>
              <div class="footer-top__content-container">

                <template x-if="site.footer.enable_logo">
                    <div class="footer-logo mb-2">
                        <div class="site-logo !h-auto relative">
                            <a x-outlink="site.header.link" class="logo">
                               <template x-if="site.header.logo_type == 'image'">
                                  <div>
                                     <template x-if="!site.header.logo">
                                        <div class="default-image light !block w-5 h-5">
                                           {!! __i('--ie', 'image-picture', 'text-gray-300') !!}
                                        </div>
                                     </template>
                                     <template x-if="site.header.logo">
                                        <img :src="$store.builder.getMedia(site.header.logo)" class="site-logo light !block" :class="{'!h-[var(--logo-height)]': site.header.logo_width_mobile}" alt="">
                                     </template>
                                  </div>
                               </template>
                               <template x-if="site.header.logo_type == 'text' || !site.header.logo_type">
                                  <span class="t-2 logo-text" :class="{'text-accent': site.header.logo__c == 'accent'}" x-text="site.header.logo_text ? site.header.logo_text : site.name" :style="`--text-count: ${$store.builder.countTotalLetters(site.name)}`"></span>
                               </template>
                            </a>
                           <div class="screen"></div>
                        </div>
                    </div>
                </template>

                <template x-if="site.footer.text">
                    <div class="footer-text v_2-footer mb-4 !relative">
                        <p class="t-1" x-html="window.markdownRender(site.footer.text)">
                        </p>
                        <div class="screen"></div>
                    </div>
                </template>


                <template x-if="site.footer.button_one_text || site.footer.button_two_text">
                 <div class="footer-buttons__holder v_2-footer !relative">
                    <template x-if="site.footer.button_one_text">
                        <a x-outlink="site.footer.button_one_link" id="footer-btn-1" class="footer__button">
                            <button class="site-btn t-1 shape" x-text="site.footer.button_one_text"></button>
                        </a>
                    </template>
                    <template x-if="site.footer.button_two_text">
                        <a x-outlink="site.footer.button_two_link" id="footer-btn-2" class="footer__button">
                            <button class="site-btn t-1 shape" x-text="site.footer.button_two_text"></button>
                        </a>
                    </template>
                    <div class="screen"></div>
                 </div>
                </template>
              </div>
           </div>
           <template x-if="site.footer.copyright_one || site.footer.copyright_two">
            <div class="footer-bottom !relative">
                <template x-if="site.footer.copyright_one">
                  <div class="footer-bottom-left tip-tap-content__output">
                     <p class="footer-bottom-left__content text-[color:var(--c-mix-3)]" x-html="window.markdownRender(site.footer.copyright_one)">
                     </p>
                  </div>
                </template>
                <template x-if="site.footer.copyright_two">
                  <div class="footer-bottom-right tip-tap-content__output [&_p]:[text-align:inherit]">
                     <p class="footer-bottom-right__content" x-html="window.markdownRender(site.footer.copyright_two)"></p>
                  </div>
                </template>
                <div class="screen"></div>
             </div>
           </template>
        </div>
    </div>

     @script
     <script>
         Alpine.data('builder__footer', () => {
            return {
               openMobile: false,
               init(){
                  var $this = this;
               }
            }
         });
     </script>
     @endscript
</div>