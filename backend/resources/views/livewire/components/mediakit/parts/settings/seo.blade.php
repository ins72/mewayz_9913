
<div class="settings-section">
   <div class="settings-section-content">
      <div class="top-bar">
        <div class="page-settings-navbar">
           <ul >
               <li class="close-header !flex">
                 <a @click="__page='-'">
                   <span>
                       {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                   </span>
                 </a>
              </li>
              <li class="!pl-0">{{ __('Seo') }}</li>
              <li></li>
           </ul>
        </div>
       </div>
       <div class="container-small p-[var(--s-2)] pb-[150px] tab-content-box" x-data="builder__seo">
        <div class="tab-content">
           <div class="active" data-tab-content>
              <template x-if="currentPage.seo">
                 <div class="page-settings">
                    <div class="input-box !mb-[calc(var(--s-1)_-_3px)]">
                       <div class="input-group !border-0">
                        <input type="text" class="input-small" x-model="currentPage.seo.title" name="description" placeholder="{{ __('Seo title') }}">
                      </div>
                    </div>
                    
                    <div class="mb-[calc(var(--s-1)_-_3px)]">
                      <x-builder.textarea class="w-[100%] h-[100px] border-[1px] border-solid border-[var(--c-mix-1)] rounded-[var(--r-small)] bg-[unset] p-[var(--s-1)] resize-none text-[var(--t-m)] text-[var(--foreground)] focus:bg-[var(--c-mix-1)] focus:outline-none focus:border-[var(--c-mix-1)]" x-model="currentPage.seo.description" placeholder="{{ __('Seo description') }}"></x-builder.textarea>
                    </div>
                    <div class="page-actions !mb-[calc(var(--s-1)_-_3px)]">
                       <ul>
                          <li>
                               <div class="input-box !mt-0">
                                   <div class="input-group !border-0">
                                       <div class="switchWrapper">
                                            <input class="switchInput !border-0" value="1" id="pageSeo" type="checkbox" x-model="currentPage.seo.block" :checked="currentPage.seo.block">
  
                                           <label for="pageSeo" class="switchLabel">{{__('Hide from search results')}}</label>
                                           <div class="slider"></div>
                                       </div>
                                   </div>
                               </div>
                           </li>
                       </ul>
                   </div>
       
                    <div class="bg-[var(--c-mix-1)] p-[var(--s-1)] rounded-[var(--r-small)] mb-[var(--s-1)]">
                       <div class="flex items-center w-[100%] border-[1px] border-solid border-[var(--c-mix-1)] rounded-[var(--r-small)] mb-[var(--s-1)]">
                          <div class="flex-[100%] flex relative">
                             <button class="h-[165px] bg-[var(--background)] border-[1px] border-solid border-[var(--c-mix-1)] p-0 flex transition-none hover:cursor-pointer hover:opacity-[.7] relative font-[var(--f-base)] text-[var(--t-m)] text-[var(--background)] rounded-[var(--r-small)] text-center leading-[var(--l-title)] opacity-100 w-[100%]" @click="openMedia({
                               event: 'seoLogoImage',
                               sectionBack:'navigatePage(\'__last_state\')'
                         });">
                                <label class="h-full flex object-cover rounded-tl-[3px] rounded-br-[0] rounded-tr-[0] rounded-bl-[3px] cursor-pointer items-center justify-center w-[100%] border-[0] rounded-[3px] overflow-hidden">
                                  <template x-if="currentPage.seo.image">
                                     <img class="w-[100%] h-full object-cover" :src="$store.builder.getMedia(currentPage.seo.image)" alt=" ">
                                  </template>
                                  <template x-if="!currentPage.seo.image">
                                     <div class="w-[100%] h-full flex justify-center items-center">
                                        {!! __i('--ie', 'image-picture', 'text-gray-300 w-7 h-7') !!}
                                     </div>
                                  </template>
        
                                </label>
                                <template x-if="currentPage.seo.image">
                                  <span class="!zzhidden h-[40px] w-[40px] border-l-[0] absolute right-[0] top-0 rounded-tl-[0] rounded-br-[0] rounded-tr-[0] rounded-bl-[4px] bg-gray-100 flex-grow flex items-center justify-center" @click="$event.stopPropagation(); currentPage.seo.image = ''; $dispatch('seoLogoImage', {
                                     image: null,
                                     public: null,
                                     });">
                                     {!! __i('--ie', 'trash-bin-delete', 'w-5 h-5') !!}
                                  </span>
                               </template>
                             </button>
                          </div>
                       </div>
                       <h4 class="text-[color:var(--c-mix-3)] text-[16px] mb-[var(--s-1)] leading-[var(--l-title)] font-normal" x-text="currentPage.seo && currentPage.seo.title ? currentPage.seo.title : currentPage.name"></h4>
                       <p class="text-[12px] leading-[var(--l-body)] text-[color:var(--c-mix-3)]">
                         <span x-show="currentPage.seo && currentPage.seo.block">{{__('This page won\'t show up on search engines, but you can share the link so people can access it.')}}</span>
       
                         <span x-show="currentPage.seo && !currentPage.seo.block" x-text="currentPage.seo ? currentPage.seo.description : ''"></span>
                      </p>
                       <!---->
                       {{-- <p class="text-[10px] leading-[var(--l-body)] text-[color:var(--c-mix-3)] mt-[10px]">jeffrey266.mewayz.com/page2</p> --}}
                    </div>
                 </div>
              </template>
           </div>
           
        </div>
       </div>
       @script
       <script>
           Alpine.data('builder__seo', () => {
              return {
                 autoSaveTimer: null,
                 init(){
                    var $this = this;
                    if(!$this.currentPage.seo) {
                       $this.currentPage.seo = {
                          title: '',
                       };
                    }
                    window.addEventListener("seoLogoImage", (event) => {
                        $this.currentPage.seo.image = event.detail.image;
                    });
  
                    this.$watch('site' , (value, _v) => {
                       if(!$this.currentPage.seo){
                          $this.currentPage.seo = null;
                          setTimeout(() => {
                             $this.currentPage.seo = {
                                title: '',
                             };
                          }, 700);
                       }
                       // $this.currentPage.seo = [];
                       // console.log('lollzzz', $this.currentPage)
                    });
                    // this.$watch('currentPage.seo' , (value, _v) => {
                    //    clearTimeout($this.autoSaveTimer);
                    //    console.log('lzzz')
  
                    //       $this.autoSaveTimer = setTimeout(function(){
                    //           $this.$store.builder.savingState = 0;
                    //           event = new CustomEvent("builder::savePage", {
                    //               detail: {
                    //                   page: $this.currentPage,
                    //                   js: '$store.builder.savingState = 2',
                    //               }
                    //           });
  
                    //           window.dispatchEvent(event);
                    //       }, $this.$store.builder.autoSaveDelay);
                    // });
                 }
              }
           });
       </script>
       @endscript
  </div>
</div>