<div class="banner-layout-4 w-boxed">
    <div class="banner section-component">

        <template x-if="section.settings.enable_image">
            <div class="section-item-image banner-image min-shape" :class="{'default': !media}" :style="`height: ${section.settings.height}px; --height: ${section.settings.height}px`">
                  <img :src="media" :class="{
                     'Fit': section.settings.image_type == 'fit',
                     'Fill': section.settings.image_type !== 'fit',
                     'grey': section.settings.color == 'default',
                     'transparent': section.settings.color == 'transparent',
                     'accent': section.settings.color == 'accent',
                     '!hidden': !media
                  }">

                  <template x-if="!media">
                     <div>
                        {!! __i('--ie', 'image-picture', 'text-gray-300') !!}
                     </div>
                  </template>
                  <div class="screen"></div>
            </div>
        </template>

       <div class="banner-text content-heading" :class="{
        'full': !section.settings.enable_image,
        'text-center': section.settings.align == 'center',
        'text-right': section.settings.align == 'right',
        }">

        <template x-if="section.content.label">
            <div class="banner-label section-label t-0" x-text="section.content.label"></div>
        </template>
        <h1 class="title pre-line [text-align:inherit] --text-color" :class="{
            't-4': section.settings.title == 'xs',
           't-5': section.settings.title == '' && section.settings.title == null,
           't-5': section.settings.title == 's',
           't-6': section.settings.title == 'm',
           't-7': section.settings.title == 'l',
           't-8': section.settings.title == 'xl',
           }" x-text="section.content.title">
        </h1>
            
          <section class="flex flex-col subtitle-width-size" :class="{
            'items-center': section.settings.align == 'center',
            'items-end': section.settings.align == 'right',
          }">
            <p class="t-2 pre-line subtitle-width-size subtitle --text-color" :class="{
                'text-center': section.settings.align == 'center',
                'text-right': section.settings.align == 'right',
               '![font-size:var(--t-1)]': section.settings.title == 'xs',
                }"
                :style="{
                    'width': !section.settings.enable_image && section.settings.width ? section.settings.width + '%' : '100%'
                    }" x-text="section.content.subtitle"></p>
          </section>


          <template x-if="section.settings.enable_action">
            <div :style="{
               'width': !section.settings.enable_image && section.settings.width ? section.settings.width + '%' : '100%'
               }" :class="{
                  '!mx-auto': section.settings.align == 'center',
                  '!ml-auto': section.settings.align == 'right',
               }" >
               <template x-if="section.settings.actiontype == 'form'">
                  <x-builder.partials.form />
               </template>

               <template x-if="section.settings.actiontype == 'button'">
                     <div class="mt-2 button-holder subtitle-width-size" :class="{
                        '!justify-center': section.settings.align == 'center',
                        '!justify-end': section.settings.align == 'right',
                     }">
                        <x-builder.partials.button />
                     </div>
               </template>
            </div>
          </template>
       </div>
    </div>
 </div>