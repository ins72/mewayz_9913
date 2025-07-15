<div class="link-button builder-block custom-link-container-o">
    <div class="-item -item-style animate__animated animate__ animate__delay-2s" :class="{
            [`animate__${item.content.animation}`]: item.content.animation !== '-',
            [`animate__${item.content.animation_runs}`]: true,
        }">
        <div class="--style" :class="`custom-link-container layout-${section.settings.style}`">
            <div class="w-[100%] h-full">

                <a class="custom-link-content-wrapper --control" x-outlink="item.content.link">
                    <div class="--link-icon">
                      <template x-if="!item.image">
                          <div class="default-image p-4 !flex bg-gray-200 items-center justify-center custom-link-preview-image default-transparent-on-hover">
                             {!! __i('--ie', 'image-picture', 'text-gray-400 !w-7 !h-7') !!}
                          </div>
                       </template>
                       <template x-if="item.image">
                          <img :src="$store.builder.getMedia(item.image)" alt=" " class="custom-link-preview-image default-transparent-on-hover">
                       </template>
                    </div>

                   <template x-if="['hover', 'botton', 'banner', 'overlay'].includes(section.settings.style)">
                    <span class="custom-link-preview-image-overlay"></span>
                   </template>
                   
                    <div class="custom-link-preview-content" :class="`${section.settings.style}`">
                        <div class="custom-link-preview-title">
                            <span class="custom-link-title-overflow">
                                <p x-text="item.content.title"></p>
                            </span>
                        </div>

                        <div class="custom-link-preview-description line-clamp-2" x-text="item.content.description"></div>
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