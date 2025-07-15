
<div>
   <div class="style">
      <div class="style-block site-layout">

         <template x-for="(item, index) in styles" :key="index">
            <button class="btn-layout" :class="{
               'active': section.settings.style == 'bn-' + item
            }" @click="section.settings.style = 'bn-' + item;">
               <span x-text="item"></span>
               <svg width="1em" height="1em" fill="currentColor" aria-hidden="true" focusable="false" class="w-10 h-16 text-[#d4d6d9]">
                  <use x-bind:href="'#icon-bn-' + item"></use>
               </svg>
            </button>
         </template>
      </div>
   </div>
</div>