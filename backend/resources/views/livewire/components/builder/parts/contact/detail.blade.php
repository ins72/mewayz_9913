<div>
   <div x-data="secton__contact_detail">

      <div x-show="!messagePage">
         <div class="website-section">
            <div class="design-navbar">
               <ul >
                   <li class="close-header"></li>
                  <li>{{ __('Contacts') }}</li>
                  <li class="!flex items-center !justify-center">
                    <button class="btn btn-save !bg-black !text-[var(--c-light)] !rounded-md p-0 !flex" @click="_save()">{{ __('Done') }}</button>
                 </li>
               </ul>
            </div>
            <div class="container-small p-[var(--s-2)] pb-[150px]">
              <div class="contacts !px-0">
                 <div class="user-contact-option">
                    <p x-text="item.content.first_name +' '+ item.content.last_name"></p>
                    <p class="company" x-text="item.content.company"></p>
                    <ul class="gap-[20px] [&>li]:!mr-0">
                       <template x-if="item.content.phone">
                          <li>
                             <a :href="'tel:'+item.content.phone" name="open-site">
                                <span>
                                   {!! __i('Computers Devices Electronics', 'Phone') !!}
                                </span>
                                {{ __('Call') }}
                             </a>
                          </li>
                       </template>
                       <template x-if="item.email">
                          <li>
                             <a :href="'mailto:'+item.email" name="send-mail" target="_blank">
                                <span>
                                   {!! __i('emails', 'email-mail-letter') !!}
                                </span>
                                {{ __('Email') }} 
                             </a>
                          </li>
                       </template>
                       <template x-if="item.content.phone">
                          <li>
                             <a :href="'sms://'+item.content.phone" name="send-message">
                                <span>
                                   {!! __i('--ie', 'chat-message-text') !!}
                                </span>
                                {{ __('Text') }}
                             </a>
                          </li>
                       </template>
                    </ul>
                 </div>
                 <div class="input-form">
                    <div class="input-box">
                       <div class="input-group">
                          <input type="text" class="input-small" placeholder="{{ __('Enter First Name') }}" x-model="item.content.first_name">
                       </div>
                    </div>
                    <div class="input-box">
                       <div class="input-group">
                          <input type="text" class="input-small" placeholder="{{ __('Enter Last Name') }}" x-model="item.content.last_name">
                       </div>
                    </div>
                    <div class="input-box">
                       <div class="input-group">
                          <input type="text" class="input-small" name="Phone" placeholder="{{ __('Enter Phone') }}" x-model="item.content.phone">
                       </div>
                    </div>
                    <div class="input-box">
                       <div class="input-group">
                          <input type="text" class="input-small" placeholder="{{ __('Enter Email') }}" x-model="item.email">
                       </div>
                    </div>
                    <div class="input-box">
                       <div class="input-group">
                          <input type="text" class="input-small" name="Company" placeholder="{{ __('Enter Company') }}" x-model="item.content.company">
                       </div>
                    </div>
                 </div>
                 <div class="contact-action">
                    <ul>
                       <li @click="messagePage=true">
                          <a name="send-message">
                             <p>{{ __('Messages') }}</p>
                             <span>
                                {!! __i('Arrows, Diagrams', 'Arrow.5', '!w-5 !h-5') !!}
                             </span>
                          </a>
                       </li>
                       <li @click="$wire.saveVCard(item)">
                          <a name="save-contact">
                             <p>{{ __('Save contact') }}</p>
                          </a>
                       </li>
                       <li @click="deleteContact(item)">
                          <a name="dlete-contact">
                             <p>{{ __('Delete contact') }}</p>
                          </a>
                       </li>
                    </ul>
                 </div>
              </div>
            </div>
         </div>
      </div>
      <div x-show="messagePage">
         <div class="website-section">
            <div class="design-navbar">
               <ul >
                   <li class="close-header"></li>
                  <li>{{ __('Messages') }}</li>
                  <li class="!flex items-center !justify-center">
                    <button class="btn btn-save !bg-black !text-[var(--c-light)] !rounded-md p-0 !flex" @click="_save()">{{ __('Done') }}</button>
                 </li>
               </ul>
            </div>
            <div class="container-small p-[var(--s-2)] pb-[150px]">
               <div class="contacts !px-0">
                  <div class="user-contact">
                     <p x-text="item.content.first_name +' '+ item.content.last_name"></p>
                     <p class="company" x-text="item.email"></p>
                  </div>
                  <div class="input-form mb-1" index="0">
                     <div class="input-box">
                        <div class="input-group">
                           <textarea class="input-small message !pt-[var(--unit)]" name="message" x-model="item.content.message"></textarea>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>


    @script
    <script>
        Alpine.data('secton__contact_detail', () => {
           return {
               messagePage: false,
               deleteContact(){
                  this.contacts.forEach((e, index) => {
                     if(this.item.uuid == e.uuid){
                        this.contacts.splice(index, 1);
                     }
                  });
                  this.$wire.deleteContact(this.item);
                  this.__page='-';
               },
               _save(){
                  this.$wire.saveContact(this.item);

                  if(this.messagePage){
                     this.messagePage = false;
                     return;
                  }

                  if(!this.messagePage){
                     this.__page='-';
                  }
               },

               init(){
                  var $this = this;
                  

               }
           }
         });
    </script>
    @endscript
</div>