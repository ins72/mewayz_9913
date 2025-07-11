
<?php
    use App\Models\BioSiteStory;
    use function Livewire\Volt\{state, mount, placeholder, updated, on};

    state(['site']);
    on([
    
    ]);

    updated([
    
    ]);

    mount(fn() => '');

    placeholder('
    <div class="p-5 w-full mt-1">
        <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)]"></div>
        <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
        <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
    </div>
    ');

    // Methods

    $createStory = function($item){
        $_item = new BioSiteStory;
        $_item->fill($item);
        $_item->site_id = $this->site->id;
        $_item->save();
    };

    $deleteStory = function($id){
        if(!$_item = BioSiteStory::where('uuid', $id)->where('site_id', $this->site->id)->delete()) return;
    };

    $saveStory = function($array){
    if(!is_array($array)) return;

    foreach ($array as $item) {
        if(!$_item = BioSiteStory::where('uuid', __a($item, 'uuid'))->where('site_id', $this->site->id)->first()) continue;
        
        $_item->fill($item);
        $_item->site_id = $this->site->id;
        $_item->save();
    }
    
    $this->js('$store.builder.savingState = 2');
    };
?>

<div class="website-section --create-section">

   <div x-data="builder__story" wire:ignore>
      <div class="design-navbar">
         <ul >
            <li class="close-header">
                <a @click="closePage()">
                    <span>
                        {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                    </span>
                </a>
            </li>
            <li>{{ __('Story') }}</li>
            <li class="!flex md:!hidden">
                <a @click="closePage()" class="menu--icon !bg-black !text-[10px] !text-white !w-auto !px-2 !h-5 cursor-pointer">
                    {{ __('Close') }}
                </a>
            </li>
         </ul>
      </div>
      <div class="container-small">
         <div class="mt-2 all-pages-style">
            
            <a @click="createStory" class="yena-black-btn mb-1 !justify-between">{{ __('+ Add Story') }}</a>

            <ul>
                <div class="flex flex-col gap-3" x-ref="story_sortable_wrapper">
                    <template x-for="(item, index) in window._.sortBy(story, 'position')" " :key="item.uuid" x-ref="story_sortable_template">
                       <div>
                          <div x-init="window.addEventListener('storyMediaEvent:' + item.uuid, (event) => {
                             item.thumbnail = event.detail.image;
                         });"></div>
                          <div class="border-2 border-dashed border-color-[#cbcbcb] rounded-xl p-4 relative">
                             <div class="flex gap-2">
                                 <div class="w-20 h-full">
                                   <div class="border-2 border-dashed border-color-[#cbcbcb] rounded-xl shadow-xl px-2 py-4 flex flex-col gap-2 relative" @click="openMedia({
                                      event: 'storyMediaEvent:' + item.uuid,
                                      sectionBack:'navigatePage(\'__last_state\')'
                                  });">
                                      <div>
                                         <template x-if="!item.thumbnail">
                                             <div class="w-8 h-8 bg-gray-100 p-2 rounded-full">
                                               {!! __i('--ie', 'image-picture', 'text-gray-400') !!}
                                             </div>
                                         </template>
                                         <template x-if="item.thumbnail">
                                             <img alt=" " class="w-8 h-8 rounded-full object-cover" :src="$store.builder.getMedia(item.thumbnail)">
                                         </template>
                                      </div>
                                      <span class="text-[10px]">{{ __('Upload thumbnail') }}</span>
                                   </div>
                                 </div>
                 
                                 <div class="max-w-full flex-grow">
                                     <div class="flex flex-col gap-2">
                                         <div class="form-input">
                                             <input type="text" x-model="item.name" placeholder="{{ __('Story Text') }}">
                                         </div>
                                         <div class="form-input">
                                             <input type="text" x-model="item.link" placeholder="{{ __('Story link') }}">
                                         </div>
                                     </div>
                                 </div>
                             </div>
                             
                             <div class="flex justify-between items-center pt-2 gap-2 text-concrete">
                 
                                 <div>
                                     <div class="flex items-center justify-center cursor-pointer w-6 h-6 md:h-8 md:w-8 flex justify-center items-center bg-gray-50 handle rounded-full">
                                         <i class="fi fi-rr-arrows text-xs flex cursor-move"></i>
                                     </div>
                                 </div>
                                 <div>
                                     <a class="pointer-events-auto cursor-pointer w-[24px] h-[24px] bg-[var(--c-red)] text-[var(--c-light)] rounded-[var(--r-full)] p-0 flex items-center justify-center hover:opacity-70" @click="deleteStory(item)">
                                         {!! __i('interface-essential', 'trash-bin-delete', 'w-4 h-4') !!}
                                     </a>
                                 </div>
                             </div>
                         </div>
                       </div>
                    </template>
                </div>
            </ul>
         </div>
      </div>
   </div>
   
    @script
    <script>
        Alpine.data('builder__story', () => {
         return {
            autoSaveTimer: null,
            __page: '-',
            

            createStory(){
               var count = this.story.length + 1;
               var item = {
                  uuid: this.$store.builder.generateUUID(),
                  name: `{{ __('Highlight') }}` +' '+ count,
                  link: '',
                  position: count,
               };


               this.story.push(item);
               this.$wire.createStory(item);
            },

            deleteStory(story){
               this.story.forEach((element, index) => {
                   if(story.uuid == element.uuid){
                       this.story.splice(index, 1);
                   }
               });
               this.$wire.deleteStory(story.uuid);
            },


            __save(){
               var $this = this;
               clearTimeout($this.autoSaveTimer);

               $this.autoSaveTimer = setTimeout(function(){
                  $this.$store.builder.savingState = 0;
                  $this.$wire.saveStory($this.story);
               }, $this.$store.builder.autoSaveDelay);
            },

            initSort(){
                this.$store.builder.generalSortable(this.$root.querySelector(' [x-ref="story_sortable_wrapper"]'), {
                    handle: '.handle'
                }, this.$root.querySelector('[x-ref="story_sortable_template"]'), this.story, function (){
                    // console.log($this.item);
                });
            },

            init(){
               var $this = this;

               $this.$watch('story', (value) => {
                  $this.__save();
               });
            }
         }
      });
    </script>
    @endscript
</div>