
<div>
   <div class="style px-[var(--s-2)] mt-2">
      <div class="style-block site-layout !grid-cols-2 !p-0">
         <button class="btn-layout !p-2" type="button" :class="{
            'active': section.settings.style == 'button'
         }" @click="section.settings.style = 'button'">
            <span>1</span>
            <div>
               {!! __i('custom', 'card-style-b') !!}
            </div>
         </button>
         
         <button class="btn-layout !p-2" type="button" :class="{
            'active': section.settings.style == 'card'
         }" @click="section.settings.style = 'card'">
            <span>2</span>
            <div>
               {!! __i('custom', 'card-style-o') !!}
            </div>
         </button>
      </div>
   </div>
</div>