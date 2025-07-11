<?php

?>

<div>

   <div x-data="builder__courses_add">
      <div>
         <div class="website-section">
            <div class="design-navbar">
               <ul >
                   <li class="close-header !flex">
                     <a @click="__page = '-'">
                       <span>
                           {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                       </span>
                     </a>
                  </li>
                  <li class="!pl-0">{{ __('Course') }}</li>
                  <li class="!flex items-center !justify-center">
                    
                 </li>
               </ul>
            </div>
            <div class="container-small p-[var(--s-2)] pb-[150px]">
              <form method="post">


                <template x-for="(item, index) in courses" :key="index">
                    <div>
                        <div class="contact-list flex items-center justify-center px-5 py-3" >
                            <div>
                                <div class="rounded-xl [box-shadow:var(--yena-shadows-md)] [background:var(--yena-colors-gradient-light)] p-0.5 w-12 h-12 overflow-hidden">
                                    <img :src="item.featured_image" alt=" " class="block object-cover w-[100%] h-full">
                                </div>
                            </div>
    
                            <div class=" ml-4 w-[100%] flex justify -center truncate flex-col">
                                <h2 class="flex items-center truncate text-xs md:text-sm">
                                    <div class="truncate" x-text="item.name"></div>
                                </h2>
                                <div class="text-sm text-gray-500">
                                    <div class="truncate">
                                        <span class="flex gap-3" x-html="item.price_html"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-end gap-1 w-auto ml-auto">
                                <a @click="importCourse(item)" class="yena-black-btn !h-[28px] gap-2">{!! __i('--ie', 'synchronize') !!}{{ __('Import') }}</a>
                            </div>
                        </div>
                    </div>
                </template>
                <a href="{{ route('console-courses-index') }}" class="yena-black-btn gap-2 mt-2">{!! __i('Building, Construction', 'store') !!}{{ __('Create Course') }}</a>
              </form>
            </div>
         </div>
      </div>
      
   </div>

    @script
    <script>
        Alpine.data('builder__courses_add', () => {
           return {

            importCourse(course){
                let $this = this;

                let item = {
                    uuid: this.$store.builder.generateUUID(),
                    content: {
                        'course_id': course.id,
                        'course': {
                            ...course
                        }
                    },
                };
                $this.section.items.push(item);
                var $index = $this.section.items.length-1;

                this.__page = '-';

                this.$dispatch('section::create_section_item', {
                    item: item,
                    section_id: this.section.uuid,
                });
            },

            init(){

               var $this = this;
            }
           }
         });
    </script>
    @endscript
</div>