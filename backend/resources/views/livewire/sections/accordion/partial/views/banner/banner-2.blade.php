<div>
    <div class="banner-layout-2 w-boxed">
        <div  class="banner section-component">
         
           <div class="banner-text content-heading {{ ao($section->settings, 'split_title') ? 'left-title' : '' }}" style="">
              <section  class="subtitle-width-size" data-size="50" style="zwidth: calc(50% - 50px);">
                  
                  <template x-if="section.content.label !== '' && section.content.label !== null">
                     <div class="banner-label section-label t-0" x-text="section.content.label"></div>
                  </template>

                  <h1 class="title pre-line" :class="{
                     't-5': section.settings.title == '' && section.settings.title == null,
                     't-5': section.settings.title == 's',
                     't-6': section.settings.title == 'm',
                     't-7': section.settings.title == 'l',
                     't-8': section.settings.title == 'xl',
                     }" x-text="section.content.title">
                  </h1>
              </section>
              <section class="subtitle-width-size" data-size="50" zstyle="width: 50%;">
                 <p class="t-2 pre-line subtitle-width-size subtitle" style="{{ !ao($section->settings, 'enable_image') && ao($section->settings, 'width') ? 'width:' . ao($section->settings, 'width') . '%' : '' }}" x-text="section.content.subtitle"></p>


                 <template x-if="section.settings.actiontype == 'form'">
                     <form class="email subscribe name subtitle-width-size mt-2">
                        <x-livewire::sections.banner.partial.views.includes.form />
                     </form>
                 </template>
                 <template x-if="section.settings.actiontype == 'button'">
                     <div class="button-holder mt-2 subtitle-width-size">
                        <x-livewire::sections.banner.partial.views.includes.button />
                     </div>
                 </template>
              </section>
           </div>
           @php
               $_color = 'grey';
               if(ao($section->settings, 'color') == 'transparent') $_color = 'transparent';
               if(ao($section->settings, 'color') == 'default') $_color = 'grey';
               if(ao($section->settings, 'color') == 'accent') $_color = 'accent';
           @endphp
           <template x-if="section.settings.enable_image">
            <div>
               <template x-if="section.settings.shape == 'circle'">
                  <div>
                     <div class="avatar-image flex flex-col items-start">
                        <img :src="media" class="Fit accent banner-image rounded-[100%] mb-[var(--s-2)] object-cover" :style="`height: ${section.settings.shape_avatar}px; width: ${section.settings.shape_avatar}px`">
                        <div class="screen"></div>
                     </div>
                  </div>
               </template>
               <template x-if="section.settings.shape !== 'circle'">
                  <div>

                     <div class="banner-image min-shape" :class="{'section-item-image': section.settings.image_type == 'fit'}" :style="`height: ${section.settings.height}px; --height: ${section.settings.height}px`">
                        <img :src="media" :class="{'Fit': section.settings.image_type == 'fit', 'Fill': section.settings.image_type !== 'fit'}" class=" {{ $_color }}">
                        <div class="screen"></div>
                     </div>
                  </div>
               </template>
            </div>
           </template>
        </div>
     </div>
</div>