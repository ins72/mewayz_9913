
<div>
   <div class="style">
      
      <div class="mt-2 banner-text-size">
         <div class="style-block site-layout !px-0 !pt-0 !grid-cols-2">
            <button class="btn-layout" type="button" @click="section.settings.split = '1'" :class="{
               'active': section.settings.split == '1'
            }">
               <span>1</span>
               <svg width="91" height="34" viewBox="0 0 91 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M0.114258 32.0781L90.1138 32.0781V33.0781L0.114258 33.0781L0.114258 32.0781Z" fill="var(--c-mix-10)"></path>
                  <path d="M90.1138 24.0781L0.114258 24.0781L0.114258 25.0781L90.1138 25.0781V24.0781Z" fill="var(--c-mix-10)"></path>
                  <path d="M90.1138 16.0781L0.114258 16.0781V17.0781L90.1138 17.0781V16.0781Z" fill="var(--c-mix-10)"></path>
                  <path d="M90.1138 8.07812L0.114258 8.07813V9.07813L90.1138 9.07812V8.07812Z" fill="var(--c-mix-10)"></path>
                  <path d="M90.1138 0.078125L0.114258 0.0781288V1.07813L90.1138 1.07812V0.078125Z" fill="var(--c-mix-10)"></path>
               </svg>
            </button>
            <button class="btn-layout" type="button" @click="section.settings.split = '2'" :class="{
               'active': section.settings.split == '2'
            }">
               <span>2</span>
               <svg width="91" height="34" viewBox="0 0 91 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M40.4434 16.0762L0.443359 16.0762V17.0762L40.4434 17.0762V16.0762Z" fill="var(--c-mix-10)"></path>
                  <path d="M40.4434 8.07617L0.443359 8.07618V9.07618L40.4434 9.07617V8.07617Z" fill="var(--c-mix-10)"></path>
                  <path d="M40.4434 0.0761719L0.443359 0.0761757V1.07618L40.4434 1.07617V0.0761719Z" fill="var(--c-mix-10)"></path>
                  <path d="M50.4434 32.0762L90.4434 32.0762V33.0762L50.4434 33.0762V32.0762Z" fill="var(--c-mix-10)"></path>
                  <path d="M90.4434 24.0762L50.4434 24.0762V25.0762L90.4434 25.0762V24.0762Z" fill="var(--c-mix-10)"></path>
                  <path d="M90.4434 16.0762L50.4434 16.0762V17.0762L90.4434 17.0762V16.0762Z" fill="var(--c-mix-10)"></path>
                  <path d="M90.4434 8.07617L50.4434 8.07618V9.07618L90.4434 9.07617V8.07617Z" fill="var(--c-mix-10)"></path>
                  <path d="M90.4434 0.0761719L50.4434 0.0761757V1.07618L90.4434 1.07617V0.0761719Z" fill="var(--c-mix-10)"></path>
               </svg>
            </button>
         </div>

         <form>
            <div class="input-box" :class="{'!hidden': section.settings.split_title}">
               <label for="text-size">{{ __('Align') }}</label>
               <div class="input-group align-type">
                  <button class="btn-nav" type="button" :class="{'active': section.settings.align == 'left'}" @click="section.settings.align = 'left'">
                     {!! __i('Type, Paragraph, Character', 'align-left') !!}
                  </button>
                  <button class="btn-nav" type="button" :class="{'active': section.settings.align == 'center'}" @click="section.settings.align = 'center'">
                     {!! __i('Type, Paragraph, Character', 'align-center') !!}
                  </button>
                  <button class="btn-nav" type="button" :class="{'active': section.settings.align == 'right'}" @click="section.settings.align = 'right'">
                     {!! __i('Type, Paragraph, Character', 'align-right') !!}
                  </button>
               </div>
            </div>

            <div class="input-box banner-advanced banner-action">
               <div class="input-group">
                  <div class="switchWrapper">
                     <input id="splitSection-switch" type="checkbox" x-model="section.settings.split_title" class="switchInput">

                     <label for="splitSection-switch" class="switchLabel">{{ __('Split Section') }}</label>
                     <div class="slider"></div>
                  </div>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>