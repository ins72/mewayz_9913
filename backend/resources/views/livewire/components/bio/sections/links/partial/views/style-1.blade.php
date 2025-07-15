<div>

    <div x-data="builder__links_single">
        <div class="link-button builder-block animate__animated animate__delay-2s" :class="{
            [`animate__${item.content.animation}`]: en && item.content.animation !== '-',
            [`animate__${item.content.animation_runs}`]: en && true,
            '--is-right': en && item.content.flip_thumbnail,
            'pattern-image --in-img': en && item.content.thumbnail_pattern,
            'custom-styles-link': en && item.content.theme,
        }" :style="{
            '--link-block-bg-color': en && item.content.color ? item.content.color : '',
            '--accent': en && item.content.color ? item.content.color : '',
        }">
            <div class="-item -item-style" :class="{
                '!rounded-none': en && item.content.corners == 'straight' || en && item.content.theme == 'sand',
                '!rounded-xl': en && item.content.corners == 'round',
                '!rounded-full': en && item.content.corners == 'rounded',
                [`--${item.content.theme}`]: en && item.content.theme,
            }" :style="{
                'box-shadow': en && item.content.theme == 'retro' ? `6px 6px 0px 0px ${$store.builder.hexToRgba(item.content.color ? item.content.color : site.settings.color, 0.4)}` : false,
            }">
                <div class="--style">
                    <div class="w-[100%] h-full">
                        <a class="--link --control" :style="{
                            'height': en ? item.content.height + 'px' : '',
                        }" x-outlink="item.content.link">
                           <template x-if="item.image">
                            <div class="--link-icon" :class="{
                                [`--${item.content.thumbnail_pattern}`]: en && item.content.thumbnail_pattern
                              }" :style="{
                                'width': en ? item.content.thumb_width + 'px' : '',
                              }">
                                 <img :src="$store.builder.getMedia(item.image)" :class="{
                                    '!rounded-none': en && item.content.corners == 'straight',
                                    '!rounded-xl': en && item.content.corners == 'round',
                                    '!rounded-full': en && item.content.corners == 'rounded',
                                }" class="">
                              </div>
                           </template>
                           <div class="--link-text-wrap">
                              <p x-text="item.content.title" class=" ![line-height:normal]" :style="{
                                'font-size': en ? item.content.font_size + 'px' : '',
                                'color': en && item.content.color ? $store.builder.getContrastColor(item.content.color) : '',
                              }" :class="{
                                'text-left': item.content.align == 'left',
                                'text-center': item.content.align == 'center',
                                'text-right': item.content.align == 'right',
                              }"></p>
                              <p class="--link-text ![line-height:normal]" x-text="item.content.description" :class="{
                                '!block': item.content.description,
                                'text-left': item.content.align == 'left',
                                'text-center': item.content.align == 'center',
                                'text-right': item.content.align == 'right',
                              }"></p>
                           </div>
                           <div class="--link-status"><span></span></div>
                        </a>
                    </div>
                </div>
                <div class="--item--bg"></div>
            </div>
        </div>
    </div>
  
    @script
      <script>
          Alpine.data('builder__links_single', () => {
             return {
                en: false,
  
                run(){
  
                 //console.log('RUNNNN', this.item)
                },
                __en(){
                    this.en = this.item.content.customize;
                },
                init(){
                 var $this = this;
                 $this.__en();
                 this.$watch('item' , (value, _v) => {
                    $this.item = value;
                    $this.__en();
                 });
                 
                 //console.log('BRUHHH', this.item)
                }
             }
          });
      </script>
    @endscript
</div>