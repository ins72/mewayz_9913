<div>
    <div class="link-button builder-block custom-link-container-o">
        <div class="-item -item-style !rounded-xl !m-0 !p-3">
            <div class="--style custom-link-container layout-description !h-full !m-0">
                <a class="w-[100%] h-full" x-outlink="service.route">
    
                    <div class="custom-link-content-wrapper --control">
                        <div class="--link-icon !h-[90px] !w-[90px] [flex:0_0_90px]">
                          <template x-if="!item.image">
                              <div class="default-image p-4 !flex bg-gray-200 items-center justify-center !rounded-xl">
                                 {!! __i('--ie', 'image-picture', 'text-gray-400 !w-full !h-full') !!}
                              </div>
                           </template>
                           <template x-if="item.image">
                              <img :src="$store.builder.getMedia(item.image)" alt=" " class="custom-link-preview-image default-transparent-on-hover !relative !rounded-xl !w-full !h-full">
                           </template>
                        </div>
                        <div class="custom-link-preview-content description !pl-4">
                            <div class="custom-link-preview-title">
                                <span class="custom-link-title-overflow">
                                    <p x-text="section.content.booking.data.title"></p>
                                </span>
                            </div>
    
                            <div class="custom-link-preview-description line-clamp-2" x-text="section.content.booking.data.subtitle"></div>

                            <div class="custom-link-preview-description line-clamp-2 !text-[#5383ff] !mt-1 font-bold" x-html="service.price_html"></div>
                        </div>
                    </div>

                    <div class="yena-black-btn mt-1 !bg-black !text-white !text-center w-[100%] !justify-center ![--accent:#000] !rounded-full" x-text="section.content.booking.data.button ? section.content.booking.data.button : '{{ __('Book') }}'"></div>
                </a>
            </div>
            <div class="--item--bg"></div>
        </div>
    </div>
</div>