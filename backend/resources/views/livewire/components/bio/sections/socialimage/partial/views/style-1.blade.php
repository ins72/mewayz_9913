<div :class="`link-button builder-block`">
    <div class="-item -item-style animate__animated animate__ animate__delay-2s">
        <div :class="`--style`">
            <div class="w-[100%] h-full">
                <a :class="`--link --control`">
                   <template x-if="item.image">
                      <div class="--link-icon">
                         <img :src="$store.builder.getMedia(item.image)" class="">
                      </div>
                   </template>
                   <div class="--link-text-wrap">
                      <p x-text="item.content.title"></p>
                      <p class="--link-text"></p>
                   </div>
                   <div class="--link-status"><span></span></div>
                </a>
            </div>
        </div>
        <div class="--item--bg"></div>
    </div>
</div>