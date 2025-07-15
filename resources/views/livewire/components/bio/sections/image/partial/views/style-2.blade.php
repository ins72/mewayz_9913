<div class="link-button builder-block custom-link-container-o">
    <div class="-item -item-style animate__animated animate__ animate__delay-2s">
        <div class="--style" :class="`custom-link-container layout-${section.settings.style}`">
            <div class="w-[100%] h-full">

                <a class="custom-link-content-wrapper --control">
                                    
                   <template x-if="item.image">
                      <div class="--link-icon">
                         <img :src="$store.builder.getMedia(item.image)" alt=" " class="custom-link-preview-image default-transparent-on-hover">
                      </div>
                   </template>

                   <template x-if="['hover', 'botton', 'banner', 'overlay'].includes(section.settings.style)">
                    <span class="custom-link-preview-image-overlay"></span>
                   </template>
                   
                    <div class="custom-link-preview-content" :class="`${section.settings.style}`">
                        <div class="custom-link-preview-title">
                            <span class="custom-link-title-overflow">
                                <p x-text="item.content.title"></p>
                            </span>
                        </div>

                        <div class="custom-link-preview-description line-clamp-2" x-text="item.content.subtitle"></div>
{{-- 
                        @if ($styles == 'botton' && !empty(ao($item->content, 'button')))
                        <div class="custom-link-preview-button line-clamp-2"><p>{{ ao($item->content, 'button') }}</p></div>
                        @endif --}}
                    </div>
                </a>
            </div>
        </div>
        <div class="--item--bg"></div>
    </div>
</div>