<div>
    <div class="link-button builder-block custom-link-container-o">
        <div class="-item -item-style !rounded-full !m-0">
            <div class="--style custom-link-container layout-description !h-full !rounded-full">
                <div class="w-[100%] h-full">
    
                    <a class="custom-link-content-wrapper --control !p-1 !rounded-full border border-solid border-[#5383ff]" x-outlink="service.route">
                        <div class="--link-icon !h-[50px] !w-[50px] [flex:0_0_50px]">
                          <template x-if="!item.image">
                              <div class="default-image p-4 !flex bg-gray-200 items-center justify-center !rounded-full">
                                 {!! __i('--ie', 'image-picture', 'text-gray-400 !rounded-full !w-full !h-full') !!}
                              </div>
                           </template>
                           <template x-if="item.image">
                              <img :src="$store.builder.getMedia(item.image)" alt=" " class="custom-link-preview-image default-transparent-on-hover !relative !rounded-full !w-full !h-full">
                           </template>
                        </div>
                        <div class="custom-link-preview-content description !pl-4">
                            <div class="custom-link-preview-title">
                                <span class="custom-link-title-overflow">
                                    <p x-text="section.content.booking.data.button"></p>
                                </span>
                            </div>
    
                            <div class="custom-link-preview-description line-clamp-2 !text-[#5383ff] !mt-1 font-bold" x-html="service.price_html"></div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="--item--bg"></div>
        </div>
    </div>
</div>