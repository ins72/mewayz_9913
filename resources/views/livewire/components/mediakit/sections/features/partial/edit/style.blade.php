
<div>
   <div class="style px-[var(--s-2)] mt-2">
      <div class="style-block site-layout !grid-cols-2 !p-0">
         <button class="btn-layout !p-2" type="button" :class="{
            'active': section.settings.style == 'default'
         }" @click="section.settings.style = 'default'">
            <span>1</span>
            <div>
               <svg width="70" height="85" viewBox="0 0 160 272" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <rect x="11" y="11" width="138" height="250" rx="13" fill="#bbbbbb" stroke="#FFFFFF" stroke-width="2"></rect>
                  <rect x="62" y="59" width="44" height="11" rx="5.5" fill="#FFFFFF"></rect>
                  <rect x="62" y="120" width="44" height="11" rx="5.5" fill="#FFFFFF"></rect>
                  <rect x="62" y="181" width="44" height="11" rx="5.5" fill="#FFFFFF"></rect>
                  <rect x="62" y="77" width="77" height="5" rx="2.5" fill="#FFFFFF"></rect>
                  <rect x="62" y="138" width="77" height="5" rx="2.5" fill="#FFFFFF"></rect>
                  <rect x="62" y="199" width="77" height="5" rx="2.5" fill="#FFFFFF"></rect>
                  <rect x="62" y="85" width="63" height="5" rx="2.5" fill="#FFFFFF"></rect>
                  <rect x="62" y="146" width="63" height="5" rx="2.5" fill="#FFFFFF"></rect>
                  <rect x="62" y="207" width="63" height="5" rx="2.5" fill="#FFFFFF"></rect>
                  <rect x="21" y="59" width="33" height="33" rx="5" fill="#ffffff" stroke="#FFFFFF" stroke-width="2"></rect>
                  <rect x="21" y="120" width="33" height="33" rx="5" fill="#ffffff" stroke="#FFFFFF" stroke-width="2"></rect>
                  <rect x="21" y="181" width="33" height="33" rx="5" fill="#ffffff" stroke="#FFFFFF" stroke-width="2"></rect>
               </svg>
            </div>
         </button>
         
         <button class="btn-layout !p-2" type="button" :class="{
            'active': section.settings.style == 'flip'
         }" @click="section.settings.style = 'flip'">
            <span>2</span>
            <div>

               <svg width="70" height="85" viewBox="0 0 160 272" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <rect x="11" y="11" width="138" height="250" rx="13" fill="#bbbbbb" stroke="#ffffff" stroke-width="2"></rect>
                  <rect x="20" y="59" width="41" height="11" rx="5.5" fill="#ffffff"></rect>
                  <rect x="20" y="77" width="74" height="5" rx="2.5" fill="#ffffff"></rect>
                  <rect x="20" y="85" width="61" height="5" rx="2.5" fill="#ffffff"></rect>
                  <rect x="20" y="120" width="41" height="11" rx="5.5" fill="#ffffff"></rect>
                  <rect x="20" y="138" width="74" height="5" rx="2.5" fill="#ffffff"></rect>
                  <rect x="20" y="146" width="61" height="5" rx="2.5" fill="#ffffff"></rect>
                  <rect x="20" y="181" width="41" height="11" rx="5.5" fill="#ffffff"></rect>
                  <rect x="20" y="199" width="74" height="5" rx="2.5" fill="#ffffff"></rect>
                  <rect x="20" y="207" width="61" height="5" rx="2.5" fill="#ffffff"></rect>
                  <rect x="106" y="59" width="33" height="33" rx="5" fill="#ffffff" stroke="#ffffff" stroke-width="2"></rect>
                  <rect x="106" y="120" width="33" height="33" rx="5" fill="#ffffff" stroke="#ffffff" stroke-width="2"></rect>
                  <rect x="106" y="181" width="33" height="33" rx="5" fill="#ffffff" stroke="#ffffff" stroke-width="2"></rect>
               </svg>
            </div>
         </button>
         
         <button class="btn-layout !p-2" type="button" :class="{
            'active': section.settings.style == 'switches'
         }" @click="section.settings.style = 'switches'">
            <span>3</span>
            <div>

               <svg width="70" height="85" viewBox="0 0 160 272" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <rect x="11" y="11" width="138" height="250" rx="13" fill="#bbbbbb" stroke="#ffffff" stroke-width="2"></rect>
                  <rect x="20" y="59" width="41" height="11" rx="5.5" fill="#ffffff"></rect>
                  <rect x="20" y="77" width="74" height="5" rx="2.5" fill="#ffffff"></rect>
                  <rect x="20" y="85" width="61" height="5" rx="2.5" fill="#ffffff"></rect>
                  <rect x="63" y="120" width="41" height="11" rx="5.5" fill="#ffffff"></rect>
                  <rect x="63" y="138" width="74" height="5" rx="2.5" fill="#ffffff"></rect>
                  <rect x="63" y="146" width="61" height="5" rx="2.5" fill="#ffffff"></rect>
                  <rect x="20" y="181" width="41" height="11" rx="5.5" fill="#ffffff"></rect>
                  <rect x="20" y="199" width="74" height="5" rx="2.5" fill="#ffffff"></rect>
                  <rect x="20" y="207" width="61" height="5" rx="2.5" fill="#ffffff"></rect>
                  <rect x="106" y="59" width="33" height="33" rx="5" fill="#ffffff" stroke="#ffffff" stroke-width="2"></rect>
                  <rect x="21" y="120" width="33" height="33" rx="5" fill="#ffffff" stroke="#ffffff" stroke-width="2"></rect>
                  <rect x="106" y="181" width="33" height="33" rx="5" fill="#ffffff" stroke="#ffffff" stroke-width="2"></rect>
               </svg>
            </div>
         </button>
         
         <button class="btn-layout !p-2" type="button" :class="{
            'active': section.settings.style == 'carded'
         }" @click="section.settings.style = 'carded'">
            <span>4</span>
            <div>
               <svg width="70" height="85" viewBox="0 0 160 272" fill="none" xmlns="http://www.w3.org/2000/svg">
                   <rect x="11" y="11" width="138" height="250" rx="13" fill="#bbbbbb" stroke="#ffffff" stroke-width="2"></rect>
                   <rect x="27" y="228" width="94" height="5" rx="2.5" fill="#ffffff"></rect>
                   <rect x="27" y="220" width="105" height="5" rx="2.5" fill="#ffffff"></rect>
                   <rect x="42" y="202" width="73" height="11" rx="5.5" fill="#ffffff"></rect>
                   <rect x="60" y="151" width="40" height="40" rx="5" fill="#ffffff" stroke="#ffffff" stroke-width="2"></rect>
                   <rect x="27" y="115" width="94" height="5" rx="2.5" fill="#ffffff"></rect>
                   <rect x="27" y="107" width="105" height="5" rx="2.5" fill="#ffffff"></rect>
                   <rect x="42" y="89" width="73" height="11" rx="5.5" fill="#ffffff"></rect>
                   <rect x="60" y="40" width="40" height="39" rx="5" fill="#ffffff" stroke="#ffffff" stroke-width="2"></rect>
                </svg>
            </div>
         </button>
      </div>
   </div>
</div>