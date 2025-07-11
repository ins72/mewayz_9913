<div>

    <div class="website-section" x-data="builder___footer_groups">
        <div class="design-navbar">
           <ul >
               <li class="close-header !flex">
                  <a @click="__page='-'">
                   <span>
                       {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                   </span>
                 </a>
              </li>
              <li class="!pl-0">{{ __('Links') }}</li>
              <li></li>
           </ul>
        </div>
        <div class="container-small p-[var(--s-2)] pb-[150px]">
         <form class="form-panel" onsubmit="return false">
            
            <div class="accordion">
               <div x-ref="sortable_wrapper">
                  <template x-for="(item, index) in window._.sortBy(footerGroups, 'position')" :key="item.uuid" x-ref="sortable_template">
                     <div class="accordion-item accordion sortable-item" x-bind:data-id="item.uuid" @click="__page = 'section::groups::' + item.uuid">
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
               
                <template x-if="(footerGroups.length + 1) <= 6">
                  <div class="accordion-item add-new-accordion" @click="createGroup">
                     <button class="accordion-header" type="button">
                        <p class=" !m-0"><span >{{ __('Add Group') }}</span></p>
                        <span class="plus-icon">
                           {!! __i('interface-essential', 'plus-add.3') !!}
                        </span>
                     </button>
                  </div>
                </template>
            </div>
         </form>
        </div>
     </div>

     @script
     <script>
         Alpine.data('builder___footer_groups', () => {
            return {
               links: [],

               createGroup(){
                  var count = this.footerGroups.length + 1;

                  let item = {
                     uuid: this.$store.builder.generateUUID(),
                     title: 'Group ' + count,
                     position: count,
                     links: [],
                  };

                  this.footerGroups.push(item);
                  this.$wire.createGroup(item);
               },
               initSort(){
                  var $this = this;
                  if(this.$refs.sortable_wrapper){
                     this.$store.builder.generalSortable(this.$refs.sortable_wrapper, {
                        handle: '.handle'
                     }, this.$refs.sortable_template, this.footerGroups, function(){
                        // console.log($this.item);
                     });
                  }
               },
               init(){
                  this.initSort();
               }
            }
         })
     </script>
     @endscript
</div>
