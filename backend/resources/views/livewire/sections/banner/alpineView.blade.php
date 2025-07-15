

<div>
   <div class="wire-section" x-intersect="__section_loaded($el)" :class="{
       'section-width-fill': section.section_settings.width == 'fill',
       'lr-padding': section.section_settings.width == 'fit',

       ...getClass(),
       }" x-data="builder__bannerSectionView">
   <section class="section-content" :class="{
       'w-boxed min-shape !py-[var(--s-2)]': section.section_settings.width == 'fit'
   }">
   {{-- <!-- <div class="frame-container" v-if="isValidUrl(component?.data?.videoUrl) && component?.data?.media === 'video'">
            <iframe
              class="iframe"
              v-if="isValidUrl(component?.data?.videoUrl)"
              style="width: 100%; height: 100%"
              :src="getEmbed(component?.data?.videoUrl) + '?autoplay=1&controls=0&mute=1'"
              allow="autoplay; fullscreen; picture-in-picture"
              frameborder="0"
              allowfullscreen
            >
            </iframe>
          </div> --> --}}
       <div class="banner-box section-bg-wrapper transparent color" :class="sectionClass" :style="{'--section-image': 'url('+$store.builder.getMedia(section.section_settings !== null && section.section_settings.image)+')'}">
          <div class="inner-content section-container" :class="{
            'align-items-start': section.section_settings.align == 'top',
            'align-items-center': section.section_settings.align == 'center',
            'align-items-end': section.section_settings.align == 'bottom',
            'parallax': section.section_settings.parallax,
          }">

               @foreach(Storage::disk('sections')->files('banner/partial/views/banner') as $item)
                   @php
                       $file = Str::before($item, '.blade.php');
                       $name = basename($file);

                       $banner_name = str_replace('banner-', '', $name);
                       
                       $component = "livewire::sections.banner.partial.views.banner.$name";
                   @endphp

                   <template x-if="section.settings.banner_style == '{{$banner_name}}'">
                       <x-dynamic-component :component="$component"/>
                   </template>
               @endforeach
          </div>
       </div>
    </section>

    @script
    <script>
        Alpine.data('builder__bannerSectionView', () => {
           return {
              bannerConfigs: {!! collect(config('yena.banners'))->toJson() !!},
              media: null,
              autoSaveTimer: null,
              generateSection: null,
              aiContent: {
                  category: 'Art',
                  textPrompt: '',
                  textContent: 'generate',
                  textAmount: 'brief',
                  textTone: 'casual',
                  textLanguage: 'english',
                  generateImages: 'none',
                  imageQuery: null,
              },
              getClass: function(){
               var __bannerConfig = this.bannerConfigs[this.section.settings.banner_style];
               var object = {
                  'banner-box': true,
                  'new': true,
                  'box': true,
                  'section-bg-wrapper': true, 
                  'focus': true
               }
               
               var align = '';
               if(!__bannerConfig.align){
                  align = __bannerConfig.default.align;

                  object = {
                     ...object,
                     'align':align
                  }
               }

               return object;
              },
              sectionClass: function(){
               var $classes = {
                  // '!my-[var(--s-2)]': this.section.section_settings.width == 'fit',
                  ...this.$store.builder.generateSectionClass(this.site, this.section),
               };
               
               return $classes;
              },
              sectionStyles: function(){
               return this.$store.builder.generateSectionStyles(this.section);
              },

              sectionClasses: function(){
               this.sectionClass();
               this.sectionStyles();
              },
              regenerateAi(content = []){
                  let $this = this;
                  let $content = {
                     ...$this.aiContent,
                     ...content,
                  };
                  let ai = new Ai($this.section);
                  ai.setPrompt($content);
                  let section = ['title', 'subtitle'];

                  section.forEach(sec => {
                        ai.setTake(sec);
                        ai.run(function(e){
                           if(e.includes('--ai-start-')){
                              $this.section.content[sec] = '';
                           }

                           e = e.replace('--ai-start-', '');
                           $this.section.content[sec] += e;
                        });
                  });


                  if($content !== 'none'){
                     ai.image(function(e){
                           $this.section.image = e;
                           $this.section.get_image = e;
                           $this.section.settings.enable_image = true;
                     });
                  }
               },
               _save(){
                  let $this = this;
                  clearTimeout($this.autoSaveTimer);

                  $this.autoSaveTimer = setTimeout(function(){
                     $this.$store.builder.savingState = 0;
                     let event = new CustomEvent("builder::saveSection", {
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
                  window.addEventListener("reaiSection:" + this.section.uuid, (event) => {
                     let detail = event.detail;

                     $this.generateSection = detail.section;
                     $this.aiContent = detail.prompt;

                     $this.regenerateAi();
                  });
                 $this.media = this.$store.builder.getMedia(this.section.image);
                 window.addEventListener('sectionMediaEvent:' + this.section.uuid, (event) => {
                     $this.media = event.detail.public;
                 });
               
                  window.addEventListener("sectionMediaEvent:" + this.section.uuid, (event) => {
                     this.section.image = event.detail.image;
                     this.section.get_image = event.detail.public;
                     $this._save();
                  });
                  
                  window.addEventListener("sectionSettingsMediaEvent:" + this.section.uuid, (event) => {
                     this.section.section_settings.image = event.detail.image;
                     $this._save();
                  });

                  this.$watch('section' , (value, _v) => {
                     $this._save();
                  });
              }
           }
        });
    </script>
    @endscript
   </div>
</div>