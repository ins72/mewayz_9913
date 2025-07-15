<div>

    <div class="website-section" x-data="builder___footer_links_single">
        <div class="design-navbar">
           <ul >
               <li class="close-header !flex">
                  <a @click="item.parent_id ? __page='section::groups::' + item.parent_id : __page='groups'">
                   <span>
                       {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                   </span>
                 </a>
              </li>
              <li class="!pl-0" x-text="item.title"></li>
              <li class="!flex items-center !justify-center">
                <button class="btn btn-save !w-[24px] !bg-[var(--c-red)] !text-[var(--c-light)] !rounded-[var(--r-full)] p-0 !flex" @click="__delete_link(item)">{!! __i('interface-essential', 'trash-bin-delete', 'w-4 h-4') !!}</button>
              </li>
           </ul>
        </div>
        <div class="container-small p-[var(--s-2)] pb-[150px]">
         <form class="form-panel" onsubmit="return false">
            
            <div class="mt-1 input-box">
               <div class="input-label">{{ __('Text') }}</div>
               <div class="input-group button-input-group">
                  <input type="text" class="input-small button-text" x-model="item.title" placeholder="{{ __('Link Text') }}">
               </div>
            </div>
            <template x-if="item.parent_id">
               <div class="mt-1 input-box">
                  <div class="input-label">{{ __('Link') }}</div>
                  <div class="input-group button-input-group">
   
                     <x-builder.input>
                        <div class="relative link-options__main">
                           <input class="input-small main__link" type="text" x-model="item.link" placeholder="{{ __('Search site or paste link') }}" x-on:input="filter()">
                        </div>
                     </x-builder.input>
                  </div>
               </div>
            </template>

            <template x-if="!item.parent_id">
               <div class="mt-1 accordion">
                  <div x-init="initSort()"></div>
                  <div x-ref="sortable_children_wrapper">
                     <template x-for="(item, index) in window._.sortBy(item.links, 'position')" :key="item.uuid" x-ref="sortable_children_template">
                        <div class="accordion-item accordion sortable-item" x-bind:data-id="item.uuid" @click="__page = 'section::footer_link::' + item.uuid">
                           <div class="accordion-header menu">
                              <div class="handle cursor-move">
                                 {!! __i('custom', 'grip-dots-vertical', '!w-[10px] !h-[10px]') !!}
                              </div>
                              <p class="sub-panel-width !m-0">
                                 <span x-text="item.title"></span>
                              </p>
                              <span>
                                 {!! __i('Arrows, Diagrams', 'Arrow.5', '!w-5 !h-5') !!}
                              </span>
                           </div>
                           <div class="accordion-body menu call-to-action"></div>
                        </div>
                     </template>
                   </div>
                  
                  <div class="accordion-item add-new-accordion" @click="create_link">
                     <button class="accordion-header" type="button">
                        <p class=" !m-0"><span >{{ __('Add Link') }}</span></p>
                        <span class="plus-icon">
                           {!! __i('interface-essential', 'plus-add.3') !!}
                        </span>
                     </button>
                  </div>
               </div>
            </template>
         </form>
        </div>
     </div>

     @script
     <script>
         Alpine.data('builder___footer_links_single', () => {
            return {

               create_link(){
                  var count = this.item.links.length + 1;

                  let item = {
                     uuid: this.$store.builder.generateUUID(),
                     parent_id: this.item.uuid,
                     title: 'Sub Link ' + count,
                     link: '',
                     position: count,
                  };

                  this.item.links.push(item);
               },

               __delete_link(item){
                  let $this = this;
                  
                  if(item.parent_id){
                     this.footerGroups.forEach((element, index) => {
                        if(item.parent_id == element.uuid){
                           
                           element.links.forEach((e, i) => {
                              if(item.uuid == e.uuid){
                                 element.links.splice(i, 1);
                              }
                           });
                        }
                     });
                     
                     this.__page = 'section::groups::' + item.parent_id;
                  }else{
                     this.footerGroups.forEach((element, index) => {
                        if(item.uuid == element.uuid){
                           this.footerGroups.splice(index, 1);
                        }
                     });
                     this.__page = 'groups';
                  }
                  
                  this.$wire.deleteGroup(item.uuid);
               },
               initSort(){
                  var $this = this;
                  if(this.$refs.sortable_children_wrapper){
                     this.$store.builder.generalSortable(this.$refs.sortable_children_wrapper, {
                        handle: '.handle'
                     }, this.$refs.sortable_children_template, this.item.links, function(){
                        // console.log($this.item);
                     });
                  }
               },
               init(){
                  var $this = this;

                  if(this.item.links === undefined || this.item.links == null){
                     this.item.links = [];
                  }
               }
            }
         })
     </script>
     @endscript
</div>
