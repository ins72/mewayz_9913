<div>
    <div class="banner-layout-2 w-boxed">
        <div  class="banner section-component">
         
           <div class="banner-text content-heading" :class="{
            'text-center': post.settings.align == 'center',
            'text-right': post.settings.align == 'right',
            'left-title': post.settings.split_title
          }">
              <section  class="subtitle-width-size" :class="{
               '[text-align:inherit]': !post.settings.split_title
              }">
                  
                  <template x-if="post.content.label">
                     <div class="banner-label section-label t-0" x-text="post.content.label"></div>
                  </template>

                  <h1 class="title pre-line --text-color" :class="{
                     't-4': post.settings.title == 'xs',
                     't-5': post.settings.title == '' && post.settings.title == null,
                     't-5': post.settings.title == 's',
                     't-6': post.settings.title == 'm',
                     't-7': post.settings.title == 'l',
                     't-8': post.settings.title == 'xl',
                     '[text-align:inherit]': !post.settings.split_title
                     }" x-text="post.content.title">
                  </h1>
              </section>
              <section class="flex flex-col subtitle-width-size" :class="{
                 'items-center': post.settings.align == 'center',
                 'items-end': post.settings.align == 'right',
               }">
                 <p class="t-2 pre-line subtitle-width-size subtitle --text-color" :class="{
                     'text-center': post.settings.align == 'center',
                     'text-right': post.settings.align == 'right',
                     '![font-size:var(--t-1)]': post.settings.title == 'xs',
                     }"
                     :style="{
                         'width': post.settings.width ? post.settings.width + '%' : '100%'
                         }" x-text="post.content.subtitle"></p>


                  <x-livewire::sections.posts.partial.views.includes.author />
              </section>
           </div>
           
           <template x-if="post.settings.enable_image">
            <div>
               <div>

                  <div class="section-item-image banner-image min-shape" :class="{
                     'section-item-image': post.settings.image_type == 'fit', 'default': !post.content.image
                     }" :style="`height: ${post.settings.height}px; --height: ${post.settings.height}px`">
                     <img :src="$store.builder.getMedia(post.content.image)" :class="{
                        'Fit': post.settings.image_type == 'fit',
                        'Fill': post.settings.image_type !== 'fit',
                        'grey': post.settings.color == 'default',
                        'transparent': post.settings.color == 'transparent',
                        'accent': post.settings.color == 'accent',
                        '!hidden': !post.content.image
                     }">
   
                     <template x-if="!post.content.image">
                        <div>
                           {!! __i('--ie', 'image-picture', 'text-gray-300') !!}
                        </div>
                     </template>
                     <div class="screen"></div>
                  </div>
               </div>
            </div>
           </template>
        </div>
     </div>
</div>