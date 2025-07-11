
<div>
   <div class="style px-[var(--s-2)] mt-2">
      <div class="style-block site-layout !grid-cols-4">
         <template x-for="(item, index) in styles" :key="index">
            <button class="btn-layout" :class="{
               'active': section.settings.style == item.style
            }" @click="section.settings.style = item.style">
               <span x-text="index"></span>
               
               <template x-if="item.style !== '-'">
                  <img :src="item.link" alt="">
               </template>
            </button>
         </template>
      </div>
   </div>
</div>