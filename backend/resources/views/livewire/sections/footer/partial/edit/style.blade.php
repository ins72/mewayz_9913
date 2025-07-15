
<div>
   <div class="style">
      <div class="style-block site-layout !grid-cols-2">
         <template x-for="(item, index) in __footer" :key="index">
            <button class="btn-layout" :class="{
               'active': site.footer.style == index+1
            }" @click="site.footer.style = index+1;">
               <span x-text="index+1"></span>
               <div x-html="item"></div>
            </button>
         </template>
      </div>
   </div>
</div>