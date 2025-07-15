<div class="banner-layout-1 w-boxed">
    <div class="banner section-component">
       <div class="banner-text content-heading" :class="{
         'text-center': post.settings.align == 'center',
         'text-right': post.settings.align == 'right',
         'left-title': post.settings.split_title
       }">
          <section class="subtitle-width-size [text-align:inherit]">
                  
            <template x-if="post.content.label">
               <div class="banner-label section-label t-0 [text-align:inherit]" x-text="post.content.label"></div>
            </template>


             <h1 class="title pre-line [text-align:inherit] --text-color" :class="{
               't-4': post.settings.title == 'xs',
               't-5': post.settings.title == '' && post.settings.title == null,
               't-5': post.settings.title == 's',
               't-6': post.settings.title == 'm',
               't-7': post.settings.title == 'l',
               't-8': post.settings.title == 'xl',
               }" x-text="post.content.title">
            </h1>
          </section>

          <section class="subtitle-width-size [text-align:inherit] flex flex-col" :class="{
            'items-center': post.settings.align == 'center',
            'items-end': post.settings.align == 'right',
          }">
             <p class="t-2 pre-line subtitle-width-size subtitle [text-align:inherit] --text-color"  :style="`width: ${post.settings.width !== undefined ? post.settings.width : 100}%;`" :class="{
               '![font-size:var(--t-1)]': post.settings.title == 'xs',
             }" x-text="post.content.subtitle"></p>
             

            <x-livewire::sections.posts.partial.views.includes.author />
          </section>
       </div>

       <template x-if="post.settings.enable_image">
         <div>
            <div class="mt-4 banner-image section-item-image min-shape" :class="{'default': !post.content.image}" id="banner-image_1" :style="`height: ${post.settings.height}px; --height: ${post.settings.height}px`">
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
       </template>
    </div>
 </div>