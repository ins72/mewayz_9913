<div>
    <a x-outlink="service.route" class="booking-card-1 block !bg-white !border !border-solid !border-[#5383ff]" :class="{
        '!rounded-none': site.settings.corners == 'straight',
        '!rounded-xl': site.settings.corners == 'round',
        '!rounded-2xl': site.settings.corners == 'rounded',
     }">
        <div class="head-card flex justify-between items-center">
            <div class="creator-name">
                <div class="image-user">
                    <img :src="site.user.avatar_json" class="image" alt="">
                 </div>
                <h3 x-text="site.user.name"></h3>
            </div>
        </div>
        <div class="body-card !pt-0">
            <div class="cover-image">
                <template x-if="!item.image">
                    <div class="default-image p-4 !flex bg-gray-200 items-center justify-center img-cover">
                       {!! __i('--ie', 'image-picture', 'text-gray-400 !w-full !h-full') !!}
                    </div>
                 </template>
                 <template x-if="item.image">
                    <img class="img-cover" :src="$store.builder.getMedia(item.image)" alt=" ">
                 </template>
            </div>
            <div class="image-cells-container mt-1" :class="{
                '!hidden': !service.gallery || service.gallery.length == 0
            }">
                 <template x-for="(item, index) in service.gallery" :key="index">
                     <a class="image-cells-button image-cells-selected" type="button">
                        <img :src="getMedia(item)" alt=" " class="image-cells-image">
                     </a>
                 </template>
            </div>
        </div>
        <div class="footer-card !pb-0">
           <p class="-price" x-html="service.price_html"></p>
            <div class="starting-bad">
                 <div class="gap-1 flex items-center">
                    <div class="text-[rgba(19,_23,_23,_0.36)] truncate" x-html="service.duration + ' {{ __('min') }}'"></div>
                 </div>
                 <h4 x-text="section.content.booking.data.title"></h4>
                 <div class="flex items-center">
                    <span class="font-bold" x-text="section.content.booking.data.subtitle"></span>

                    <div class="flex items-center ml-1">
                       <img :src="site.user.avatar_json" class="h-[18px] w-[18px] rounded-full" alt="">
                    </div>
                 </div>
            </div>
        </div>
        
        <div class="px-[20px] pb-[15px]">
            <button class="yena-black-btn mt-1 !bg-black !text-white !text-center w-[100%] !justify-center ![--accent:#000] !rounded-full" x-text="section.content.booking.data.button ? section.content.booking.data.button : '{{ __('Book') }}'"></button>
        </div>
    </a>
</div>