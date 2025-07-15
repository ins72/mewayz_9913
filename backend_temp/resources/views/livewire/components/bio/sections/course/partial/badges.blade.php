<div>
    <template x-if="item.content.sticker == 'badge'">
        <div class="--flower w-7 h-7 rounded-full text-[10px] flex items-center justify-center font-bold" :style="{
           'background-color': item.content.sticker_color,
           'color': $store.builder.getContrastColor(item.content.sticker_color)
           }" x-text="item.content.sticker_text">
        </div>
     </template>
     <template x-if="item.content.sticker == 'hottwo'">
        <div class="w-10 h-6 rounded-r-full text-[10px] flex items-center justify-start pl-1 font-bold" :style="{
           'background-color': item.content.sticker_color,
           'color': $store.builder.getContrastColor(item.content.sticker_color)
           }" x-text="item.content.sticker_text">
        </div>
     </template>
     <template x-if="item.content.sticker == 'sale'">
        <div class="w-7 h-7 rounded-b-md text-[10px] flex items-center justify-center font-bold" :style="{
           'background-color': item.content.sticker_color,
           'color': $store.builder.getContrastColor(item.content.sticker_color)
           }" x-text="item.content.sticker_text">
        </div>
     </template>
     <template x-if="item.content.sticker == 'star'">
        <div class="--star bg-gray-300 w-7 h-7 rounded-full text-[10px] flex items-center justify-center font-bold" :style="{
           'background-color': item.content.sticker_color,
           'color': $store.builder.getContrastColor(item.content.sticker_color)
           }" x-text="item.content.sticker_text">
        </div>
     </template>
     <template x-if="item.content.sticker == 'hot'">
        <div class="w-10 h-6 rounded-br-[100%] text-[10px] flex items-center justify-start pl-1 font-bold" :style="{
           'background-color': item.content.sticker_color,
           'color': $store.builder.getContrastColor(item.content.sticker_color)
           }" x-text="item.content.sticker_text">
        </div>
     </template>
</div>