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
                           <template x-if="!section.content.booking">
                              <div>
                                 <div>
                                     <div class="flex flex-col justify-center items-start px-0 py-[60px]">
                                       {!! __i('--ie', 'eye-cross-circle 2', 'w-14 h-14') !!}
                                       <p class="mt-3 text-base text-left md:!text-2xl font-bold">
                                          {!! __t('Your booking section is empty. <br> Click here to import a booking service.') !!}
                                       </p>
                                       <a class="yena-black-btn gap-2 mt-2 cursor-pointer">{!! __i('Building, Construction', 'store') !!}{{ __('Import Service') }}</a>
                                     </div>
                                 </div>
                              </div>
                           </template>

                           <template x-if="section.content.booking">
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
                           </template>
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