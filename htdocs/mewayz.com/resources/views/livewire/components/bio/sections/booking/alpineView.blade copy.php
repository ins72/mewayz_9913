<div>
   <div x-data="builder__booking_section_view">
      <div :class="{
        'custom-styles-link': site.settings.__theme !== 'default'
      }" :style="{
         '--accent': site.settings.color,
         '--contrast': $store.builder.getContrastColor(site.settings.color),
      }">
               <div class="builder-block -booking-block-item g--block-o">

                  <div class="-item-style" :class="{
                     '!rounded-none': site.settings.__theme == 'sand',
                  }">
                        <div class="--style p-2">
                           <div x-data="{service: getBookingService(section.content.booking.booking_id)}">
                              <template x-if="section.content.booking.data.style == 'button' || !section.content.booking.data.style">
                                  <x-livewire::components.bio.sections.booking.partial.views.style-1/>
                              </template>
                              <template x-if="section.content.booking.data.style == 'callout'">
                                  <x-livewire::components.bio.sections.booking.partial.views.style-2/>
                              </template>
                              <template x-if="section.content.booking.data.style == 'full'">
                                  <x-livewire::components.bio.sections.booking.partial.views.style-3 />
                              </template>
                           </div>
                           {{-- <a @click="openBooking=true" class="booking-card-1 block !bg-white" :class="{
                              '!rounded-none': site.settings.corners == 'straight',
                              '!rounded-xl': site.settings.corners == 'round',
                              '!rounded-2xl': site.settings.corners == 'rounded',
                           }">
                              <div class="head-card flex justify-between items-center">
                                  <div class="creator-name">
                                      <div class="image-user">
                                          <img :src="site.user.avatar_json" class="image" alt="">
                                       </div>
                                      <h3 x-text="site.user.name"></h3>
                                  </div>
                              </div>
                              <div class="body-card !pt-0">
                                  <div class="cover-image">
                                      <img class="img-cover" :src="getMedia(site.user.booking_gallery[0])" alt=" ">
                                  </div>
                                  <div class="image-cells-container mt-1">
                                       <template x-for="(item, index) in site.user.booking_gallery" :key="index">
                                           <a class="image-cells-button image-cells-selected" type="button">
                                              <img :src="getMedia(item)" alt=" " class="image-cells-image">
                                           </a>
                                       </template>
                                  </div>
                              </div>
                              <div class="footer-card">
                                 <p class="-price" x-html="bookingSettings.lowestPrice"></p>
                                  <div class="starting-bad">
                                       <div class="gap-1 flex items-center">
                                          <div class="text-[rgba(19,_23,_23,_0.36)] truncate" x-text="bookingSettings.timevalue"></div>
                                       </div>
                                       <h4 x-text="bookingSettings.services +' '+ '{{ __('Services') }}'"></h4>
                                       <div class="flex items-center">
                                          <span class="font-bold text-green-600" :class="{
                                             'text-green-600': bookingSettings.available,
                                             'text-red-600': !bookingSettings.available,
                                          }"  x-text="bookingSettings.available ? '{{ __('Available') }}' : '{{ __('Unavailable') }}'"></span>

                                          <div class="flex items-center ml-1">
                                             <img :src="site.user.avatar_json" class="h-[18px] w-[18px] rounded-full" alt="">
                                          </div>
                                       </div>
                                  </div>
                                  <div>
                                       <div class="yena-black-btn !justify-between">
                                          {{ __('Book Now') }} <i class="ph ph-caret-right"></i>
                                       </div>
                                  </div>
                              </div>
                          </a> --}}
                           {{-- <div class="relative rounded-xl bg-[#fdfdfd] overflow-hidden">
                              <div class="flex flex-col gap-2">
                                 <div class="flex flex-row-reverse gap-4">
                                    <div>
                                       <div class="w-[120px]">
                                          <div class="!w-full bg-[rgba(19,_22,_23,_0.04)] overflow-hidden relative pb-[100%]">
                                             <img :src="getMedia(site.user.booking_gallery[0])" alt=" " class="absolute top-0 left-0 !w-full h-full object-cover">
                                          </div>
                                       </div>
                                    </div>

                                    <div class="flex flex-col gap-1 flex-[1_1]">
                                       <div class="gap-1 flex items-center">
                                          <div class="text-[rgba(19,_23,_23,_0.36)] truncate">
                                             11:30 PM
                                          </div>
                                       </div>
                                       <div>
                                          <div class="text-[18px] font-medium">Jeffrey</div>
                                       </div>

                                       <div class="flex flex-col gap-1">
                                          
                                          <div class="flex flex items-start gap-[.5rem] text-[color:#d29512]">
                                             <i class="ph ph-users"></i>
                                             <span class="truncate">1 guest</span>
                                          </div>
                                          <div class="flex flex items-start gap-[.5rem] text-[color:rgba(19,_23,_23,_0.36)]">
                                             <i class="ph ph-users"></i>
                                             <span class="truncate">1 guest</span>
                                          </div>
                                       </div>
                                    </div>
                                 </div>

                                 <div class="flex justify-between w-[100%]">
                                    <div class="flex items-center gap-1">

                                       <a class="yena-black-btn !justify-between">
                                          {{ __('Manage') }} <i class="ph ph-caret-right"></i>
                                       </a>
                                       <div class="flex items-center">
                                          <img :src="site.user.avatar_json" class="h-[18px] w-[18px] rounded-full" alt="">
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div> --}}
                           {{-- <div class="event-card overflow-hidden" :style="{
                              'background': site.user.booking_gallery.length > 0 ? 'url('+getMedia(site.user.booking_gallery[0])+')' : '',
                           }">
                              <section class="top"></section>
                              <div class="event-info">
                                  <p class="-price">70$</p>
                                  <p class="-title" x-text="site.user.name"></p>
                      
                                  <div class="-additional-info">
                                      <p class="-info">
                                          <i class="fas fa-map-marker-alt"></i>
                                          
                                          <span x-text="site.user.booking_title"></span>
                                      </p>
                                      <p class="-info">
                                          <i class="fas fa-calendar-alt"></i>
                                          Sat, Sep 19, 10:00 AM
                                      </p>
                                  </div>
                                  <button class="-action">{{ __('Book it') }}</button>
                              </div>
                          </div> --}}
                        </div>
                        <div class="--item--bg"></div>
                  </div>
               </div>
      </div>
   </div>
   @script
   <script>
      Alpine.data('builder__booking_section_view', () => {
         return {
            gs: '{{ gs('media/booking/image') }}',
            autoSaveTimer: null,
            getMedia(media){
                return this.gs +'/'+ media;
            },
            _save(){
               var $this = this;
               clearTimeout($this.autoSaveTimer);

               $this.autoSaveTimer = setTimeout(function(){
                  $this.$store.builder.savingState = 0;

                  let event = new CustomEvent("builder::save_sections_and_items", {
                        detail: {
                           section: $this.section,
                           js: '$store.builder.savingState = 2',
                        }
                  });

                  window.dispatchEvent(event);
               }, $this.$store.builder.autoSaveDelay);
            },
            
            init(){
               var $this = this;

               this.$watch('section' , (value, _v) => {
                  $this._save();
               });
               window.addEventListener("sectionMediaEvent:" + this.section.uuid, (event) => {
                  $this.section.image = event.detail.image;
                  $this._save();
               });
               
               window.addEventListener('section::' + this.section.uuid, (event) => {
                  $this.section = event.detail;
               });
            }
         }
      });
   </script>
   @endscript
</div>