<?php

?>

<div>

   <div x-data="builder__bookings_add">
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
                  <li class="!pl-0">{{ __('Booking') }}</li>
                  <li class="!flex items-center !justify-center">
                    
                 </li>
               </ul>
            </div>
            <div class="container-small p-[var(--s-2)] pb-[150px]">
              <form method="post">


                <template x-for="(item, index) in bookingServices" :key="index">
                    <div>
                        <div class="contact-list flex items-center justify-center px-5 py-3 px-[10px] py-[5px] relative [transition:box-shadow_0.25s_ease-out] items-center rounded-[10px] justify-between bg-[#F7F7F7] cursor-pointer hover:bg-[rgb(255,_255,_255)] hover:[box-shadow:rgba(0,_0,_0,_0.2)_0px_8px_20px]" >
                            {{-- <div>
                                <div class="rounded-xl [box-shadow:var(--yena-shadows-md)] [background:var(--yena-colors-gradient-light)] p-0.5 w-12 h-12 overflow-hidden">
                                    <img :src="item.featured_image" alt=" " class="block object-cover w-[100%] h-full">
                                </div>
                            </div> --}}
    
                            <div class=" ml-4 w-[100%] flex justify -center truncate flex-col">
                                <h2 class="flex items-center truncate text-xs md:text-sm">
                                    <div class="truncate" x-text="item.name"></div>
                                </h2>
                                <div class="text-sm text-gray-500">
                                    <div class="truncate">
                                        <span class="flex gap-3" x-html="item.duration + ' {{ __('min') }} ' + item.price_html"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-end gap-1 w-auto ml-auto">
                                <a @click="importBooking(item)" class="yena-black-btn !h-[28px] gap-2">{!! __i('--ie', 'synchronize') !!}{{ __('Import') }}</a>
                            </div>
                        </div>
                    </div>
                </template>
                {{-- <a href="{{ route('console-bookings-index') }}" class="yena-black-btn gap-2 mt-2">{!! __i('Building, Construction', 'store') !!}{{ __('Create Booking') }}</a> --}}
              </form>
            </div>
         </div>
      </div>
      
   </div>

    @script
    <script>
        Alpine.data('builder__bookings_add', () => {
           return {

            importBooking(booking){
                let $this = this;

                let item = {
                    booking_id: booking.id,
                    data: {
                        ...booking,
                        style: 'button',
                        title: '{{ __('1:1 Coaching') }}',
                        subtitle: '{{ __('Book a private coaching session with me!') }}',
                        button: '{{ __('Book a 1:1 Call with Me') }}',
                    }
                };

                $this.section.content = {
                    ...$this.section.content,
                    booking: item,
                };
                // $this.section.items.push(item);
                // var $index = $this.section.items.length-1;

                this.__page = '-';
            },

            init(){

               var $this = this;
            }
           }
         });
    </script>
    @endscript
</div>