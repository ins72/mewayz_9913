<?php
    use function Livewire\Volt\{state, mount, on, placeholder};

    placeholder('
    <div class="section-content">

        <div class="banner-box section-bg-wrapper">
            <div class="w-boxed">

                <div class="--placeholder-skeleton w-[30%] h-[42px] rounded-[var(--yena-radii-sm)] mt-4"></div>
                <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-2"></div>
                <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-2"></div>

                <div class="flex items-center mt-8 gap-4">
                    <div class="--placeholder-skeleton w-[230px] h-[240px] rounded-[var(--yena-radii-sm)]"></div>
                    <div class="--placeholder-skeleton w-[230px] h-[240px] rounded-[var(--yena-radii-sm)]"></div>
                </div>
            </div>
        </div>
    </div>');
    state([
        'section',
        '__section' => [],
        '_clkey' => function(){
            $section_id = $this->section->id;
            return "sectionComponent:$section_id";
        },

        '_section' => fn() => !empty($banner_style = ao($this->section->settings, 'banner_style')) ? $banner_style : 1,
        'media' => fn() => $this->section->getMedia(),
        'bannerConfig' => []
    ]);

    on([
        'updated-banner-settings.{section.id}' => function($settings){
            $this->section->settings = $settings;

            if($this->_section !== ao($settings, 'banner_style')) $this->_section = ao($settings, 'banner_style');
            $this->_get_banner_config();
        },
        'updated-banner.{section.id}' => function(){
            $this->section->refresh();
        },
        'updated-banner-content.{section.id}' => function($content){
            $this->section->content = $content;
        },
        'updated-banner-form.{section.id}' => function($form){
            $this->section->form = $form;
        },
        /*'sectionMediaEvent:{section.id}' => function($public, $image){
            $this->media = $public;
        },*/
    ]);

    mount(function(){
        $this->__section = $this->section->toArray();
        $this->_get_banner_config();
    });


    // Methods

    $_get_banner_config = function(){

       $bannerConfig = [];
       if(ao($this->section->settings, 'banner_style')) $bannerConfig = config("yena.banners." . ao($this->section->settings, 'banner_style'));

       $this->bannerConfig = $bannerConfig;
    };
?>


<div>

    @php
        $classes = collect([
            'banner-box',
            'new',
            'box',
            'section-bg-wrapper', 
            'focus'
        ]);

        $align = '';
        if(!ao($bannerConfig, 'align')){
            $align = ao($bannerConfig, 'default.align');
        }
        $classes->push($align);

        $classes = $classes->toArray();
    @endphp
    <div class="{{ implode(' ', $classes) }}" :class="{
        'section-width-fill': section.section_settings.width == 'fill',
        'lr-padding': section.section_settings.width == 'fit',
        }" x-data="builder__bannerSectionView">

 
    <section class="section-content" :class="{
        'w-boxed min-shape': section.section_settings.width == 'fit'
    }">
        <div class="banner-box section-bg-wrapper transparent color" :class="sectionClass" :style="{'--section-image': 'url('+$store.builder.getMedia(section.section_settings !== null && section.section_settings.image)+')'}">
           <div class="inner-content section-container">
                @foreach(Storage::disk('sections')->files('banner/partial/views/banner') as $item)
                    @php
                        $file = Str::before($item, '.blade.php');
                        $name = basename($file);

                        $banner_name = str_replace('banner-', '', $name);
                        
                        $component = "livewire::sections.banner.partial.views.banner.$name";
                    @endphp

                    <template x-if="section.settings.banner_style == '{{$banner_name}}'">
                        <x-dynamic-component :component="$component" :$media :$section/>
                    </template>
                @endforeach
           </div>
        </div>
     </section>

     @script
     <script>
         Alpine.data('builder__bannerSectionView', () => {
            return {
               section: @entangle('__section'),
               media: @entangle('media'),
               sectionClass: function(){
                return this.$store.builder.generateSectionClass(this.site, this.section);
               },
               sectionStyles: function(){
                return this.$store.builder.generateSectionStyles(this.section);
               },

               sectionClasses: function(){
                this.sectionClass();
                this.sectionStyles();
               },

               init(){
                  var $this = this;
                  var $eventID = 'section::' + this.section.uuid;



                  window.addEventListener($eventID, (event) => {
                    $this.section = event.detail;
                    $this.sectionClasses();
                  });
                  window.addEventListener("sectionMediaEvent:{{ $section->id }}", (event) => {
                        this.media = event.detail.public;
                  });
               }
            }
         });
     </script>
     @endscript
    </div>
</div>