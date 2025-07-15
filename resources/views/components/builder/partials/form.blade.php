

<form class="mt-2 email subscribe subtitle-width-size relative" @submit.prevent="saveForm" x-data="form__subscribe" :class="{
    'name': !onlyEmail(),
    '!flex !flex-col': !onlyEmail()
}">
    <div class="names-input">
        <template x-if="section.form.first_name_enable">
            <input name="firstname" type="text" class="shape" @input="resetForm" :placeholder="section.form.first_name ? section.form.first_name : '{{ __('First Name') }}'" x-model="formContent.first_name">
        </template>
        <template x-if="section.form.last_name_enable">
            <input name="lastname" type="text" class="shape" @input="resetForm" :placeholder="section.form.last_name ? section.form.last_name : '{{ __('Last Name') }}'" x-model="formContent.last_name">
        </template>
    </div>
    
    <input name="email" type="text" class="shape" @input="resetForm" :class="{'t-1': onlyEmail()}" :placeholder="section.form.email ? section.form.email : '{{ __('Email') }}'" x-model="formContent.email">
    
    <template x-if="section.form.phone_enable">
        <input name="phone" type="number" class="shape" @input="resetForm" :placeholder="section.form.phone ? section.form.phone : '{{ __('Phone') }}'" x-model="formContent.phone">
    </template>
    <template x-if="section.form.message_enable">
        <textarea name="message" class="shape mt-[10px]" :class="{
            'min-shape': site.settings.corner == 'straight' || site.settings.corner == 'rounded',
            'shape': site.settings.corner == 'round',
        }" @input="resetForm" :placeholder="section.form.message ? section.form.message : '{{ __('Message') }}'" x-model="formContent.message"></textarea>
    </template>
    
    <button class="site-btn t-1 shape" type="submit" :class="{
        'mt-2': !onlyEmail(),
        '!mr-auto': !onlyEmail() && section.settings.align == 'left',
        '!mx-auto': !onlyEmail() && section.settings.banner_style !== '6' && section.settings.banner_style !== '5' && section.settings.align == 'center',
        '!ml-auto':  !onlyEmail() && section.settings.banner_style !== '6' && section.settings.banner_style !== '5' && section.settings.align == 'right',
        }">
        <template x-if="loading">
            <div class="loader-animation-container flex items-center justify-center"><div class="inner-circles-loader !w-5 !h-5"></div></div>
        </template>
        
        <span x-text="section.form.button_name ? section.form.button_name : '{{ __('Sign up') }}'" x-show="!loading"></span>
    </button>



    <template x-if="formSuccess">
        <div class="bg-green-200 text-[11px] p-1 px-2 rounded-md mt-2 absolute -bottom-[30px]" :class="{
            'w-[100%]': !onlyEmail()
        }">
            <div class="flex items-center">
                <div class="flex-grow ml-1 text-xs">
                    {{ __('Thank you! Your submission has been received') }}
                </div>
            </div>
        </div>
    </template>

    <template x-if="formError">
        <div class="bg-red-200 text-[11px] p-1 px-2 rounded-md mt-2 absolute -bottom-[30px]" :class="{
            'w-[100%]': !onlyEmail()
        }">
        <div class="flex items-center">
            <div>
                <i class="fi fi-rr-cross-circle flex text-xs"></i>
            </div>
            <div class="flex-grow ml-1 text-xs" x-text="formError"></div>
        </div>
        </div>
    </template>
    <div class="screen"></div>

    @script
    <script>
        Alpine.data('form__subscribe', () => {
           return {
            formContent: {
                email: '',
                first_name: '',
                last_name: '',
                phone: '',
                message: '',
            },
            formError: false,
            formSuccess: false,
            loading: false,
            resetForm(){
                this.formError=false;
                this.formSuccess=false;
            },
            saveForm(){
                let $this = this;
                $this.loading = true;
                $this.formError = false;
                $this.formSuccess = false;
                if(!this.formContent.email.match(/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/)){
                  this.formError = '{{ __('Invalid email') }}';
                  return;
                }

                let element = document.querySelector('.generate-builder-wire');
                if(!element) return;


                let $wire = Livewire.find(element.getAttribute('wire:id'));
                let content = {
                    email: this.formContent.email,
                    content: this.formContent,
                };
                $wire.saveForm(content).then(r => {
                    $this.loading = false;

                    if(r.status == 'error'){
                        $this.formError = r.response;
                    }

                    if(r.status == 'success'){
                        $this.formSuccess = true;
                        $this.formContent = {
                            email: '',
                            first_name: '',
                            last_name: '',
                            phone: '',
                            message: '',
                        };
                    }
                });
            },
            onlyEmail(){
                return !this.section.form.first_name_enable && !this.section.form.last_name_enable && !this.section.form.phone_enable && !this.section.form.message_enable;
            },
            init(){
               var $this = this;
            }
           }
        });
    </script>
    @endscript
</form>