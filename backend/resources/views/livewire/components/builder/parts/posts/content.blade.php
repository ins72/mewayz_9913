<div>
    <div class="mt-2 content">
        <div class="panel-input mb-1 px-[var(--s-2)]">
           <form>
              <div class="input-box">
                 <label for="text">{{ __('Title') }}</label>
                 <div class="input-group">
                    <input type="text" class="input-small resizable-textarea blur-body"  x-model="post.content.title" name="title" placeholder="{{ __('Add main heading') }}">
                 </div>
              </div>
              <div class="input-box">
                 <label for="text">{{ __('Subtitle') }}</label>
                 <div class="input-group">
                    <x-builder.textarea class="input-small resizable-textarea blur-body overflow-y-hidden !h-[110px] !min-h-[100px]" x-model="post.content.subtitle" name="title" placeholder="{{ __('Add text here') }}"/>
                 </div>
              </div>
              <div class="input-box !hidden">
                 <div class="input-label">{{ __('Type') }}</div>
                 <div class="input-group link-group">
                    <button class="text-center btn-link btn-image active" type="button">{{ __('Image') }}</button>
                    <button class="text-center btn-link btn-video" type="button">{{ __('Video') }}</button>
                </div>
              </div>
              <div class="group block h-20 bg-white- cursor-pointer rounded-lg border-2 border-dashed text-center hover:border-solid hover:border-yellow-600 relative" :class="{
               'border-gray-200': !post.content.image,
               'border-transparent': post.content.image,
              }">
               <template x-if="post.content.image">
                  <div class="group-hover:flex hidden w-full h-full items-center justify-center absolute right-0 top-0 left-0 bottom-0">
                     <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center icon-shadow" @click="post.content.image = ''; $dispatch('sectionPostMedia:' + post.uuid, {
                        image: null,
                        public: null,
                        })">
                         <i class="fi fi-rr-trash"></i>
                     </div>
                 </div>
               </template>
               <template x-if="!post.content.image">
                  <div class="w-full h-full flex items-center justify-center" @click="page = 'media'; $dispatch('mediaEventDispatcher', {
                     event: 'sectionPostMedia:' + post.uuid, sectionBack:'navigatePage(\'__last_state\')'
                     })">
                      <div>
                          <span class="loader-line-dot-dot text-black font-2 -mt-2 m-0"></span>
                      </div>
                      <i class="fi fi-ss-plus"></i>
                  </div>
               </template>
               <template x-if="post.content.image">
                  <div class="h-full w-[100%]">
                      <img :src="$store.builder.getMedia(post.content.image)" class="h-full w-[100%] object-cover rounded-md" alt="">
                  </div>
               </template>
               </div>
           </form>
        </div>

        <div class="form-action !block">
            <div class="cursor-pointer input-box" @click="__page=post.settings.actiontype == 'form' ? 'form' : 'button'; clearTimeout(autoSaveTimer);">
               <div class="input-group" >
                  <div class="input-chevron" >
                     <label>{{ __('Author') }}</label>
                     <span>
                        {!! __icon('Arrows, Diagrams', 'Arrow.5', 'w-5 h-5') !!}
                     </span>
                  </div>
               </div>
            </div>
            <div class="cursor-pointer input-box" @click="__page=post.settings.actiontype == 'form' ? 'form' : 'button'; clearTimeout(autoSaveTimer);">
               <div class="input-group" >
                  <div class="input-chevron" >
                     <label x-text="post.settings.actiontype == 'form' ? '{{ __('Form') }}' : '{{ __('Button') }}'"></label>
                     <span>
                        {!! __icon('Arrows, Diagrams', 'Arrow.5', 'w-5 h-5') !!}
                     </span>
                  </div>
               </div>
            </div>
        </div>
     </div>

</div>