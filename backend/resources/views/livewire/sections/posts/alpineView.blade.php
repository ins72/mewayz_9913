

<div>
   <div class="wire-section" x-intersect="__section_loaded($el)" :class="{
       'section-width-fill': post.section_settings.width == 'fill',
       'lr-padding': post.section_settings.width == 'fit',

       ...getClass(),
       }" x-data="builder__postSectionView">
   <section class="section-content" :class="{
       'w-boxed min-shape !py-[var(--s-2)]': post.section_settings.width == 'fit'
   }">
       <div class="banner-box section-bg-wrapper transparent color" :class="sectionClass" :style="{'--section-image': 'url('+$store.builder.getMedia(post.section_settings !== null && post.section_settings.image)+')'}">
          <div class="inner-content section-container" :class="{
            'align-items-start': post.section_settings.align == 'top',
            'align-items-center': post.section_settings.align == 'center',
            'align-items-end': post.section_settings.align == 'bottom',
            'parallax': post.section_settings.parallax,
          }">

               @foreach(Storage::disk('sections')->files('posts/partial/views/banner') as $item)
                   @php
                       $file = Str::before($item, '.blade.php');
                       $name = basename($file);

                       $banner_name = str_replace('banner-', '', $name);
                       
                       $component = "livewire::sections.posts.partial.views.banner.$name";
                   @endphp

                   <template x-if="post.settings.banner_style == '{{$banner_name}}'">
                       <x-dynamic-component :component="$component"/>
                   </template>
               @endforeach
               <div
                   x-ref="quillEditor"
                   x-init="
                       quill = new window.Quill($refs.quillEditor, {theme: 'snow'});
                       quill.on('text-change', function () {
                           post.description = quill.root.innerHTML;
                       });

                       quill.root.innerHTML = post.description;
                   "
               >
               
               </div>
          </div>
       </div>
    </section>

    @script
    <script>
        Alpine.data('builder__postSectionView', () => {
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
               var __bannerConfig = this.bannerConfigs[this.post.settings.banner_style];
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
                  ...this.$store.builder.generateSectionClass(this.site, this.post),
               };
               
               return $classes;
              },
              sectionStyles: function(){
               return this.$store.builder.generateSectionStyles(this.post);
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

                  post.forEach(sec => {
                        ai.setTake(sec);
                        ai.run(function(e){
                           if(e.includes('--ai-start-')){
                              $this.post.content[sec] = '';
                           }

                           e = e.replace('--ai-start-', '');
                           $this.post.content[sec] += e;
                        });
                  });


                  if($content !== 'none'){
                     ai.image(function(e){
                           $this.post.image = e;
                           $this.post.get_image = e;
                           $this.post.settings.enable_image = true;
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
                  // window.addEventListener("reaiSection:" + this.post.uuid, (event) => {
                  //    let detail = event.detail;

                  //    $this.generateSection = detail.section;
                  //    $this.aiContent = detail.prompt;

                  //    $this.regenerateAi();
                  // });
                  
                  window.addEventListener("sectionSettingsMediaEvent:" + this.post.uuid, (event) => {
                     this.post.section_settings.image = event.detail.image;
                     $this._save();
                  });

                  // this.$watch('section' , (value, _v) => {
                  //    $this._save();
                  // });
              }
           }
        });
    </script>
    @endscript
   </div>
</div>