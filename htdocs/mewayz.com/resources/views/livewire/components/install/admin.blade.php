<div>
    <div class="flex flex-col gap-6 flex-1">
    
        <div class="flex-1 place-self-stretch"></div>
        <h2 class="text-4xl relative font-medium leading-[1.2em] tracking-[-0.03em] md:text-4xl">
            Admin information
        </h2>
        


        <div class="grid grid-cols-1 gap-4">
            <div class="grid grid-cols-2 gap-4">
                <div class="form-input">
                    <label for="name">{{ __('Your Name') }}</label>
                    <div class="col-md-6">
                        <input name="name" type="text" x-model="name" required placeholder="{{ __('John') }}">
                    </div>
                </div>
    
                <div class="form-input">
                    <label>{{ __('Your Email') }}</label>
                    <input type="email" name="email" x-model="email" required placeholder="{{ __('e.g: email@gmail.com') }}">
                </div>
            </div>

            <div class="form-input">
                <label>{{ __('Your Password') }}</label>
                <div class="relative">
                    <input type="password" name="password" required x-model="password" placeholder="*******" class="transition-all" :class="{'pb-[2.4rem!important]' : shown()}">

                    <div class="p-2 absolute right-1 cursor-pointer" :class="{'top-2/4 transform -translate-y-1/2': !shown(), 'top-1': shown()}" @click="showPassword =! showPassword">
                        <span x-cloak :class="{'hidden': !shown()}">
                            {!! __icon('interface-essential', 'eye-show-visible', 'w-4 h-4') !!}
                        </span>
                        <span x-cloak :class="{'hidden': shown()}">
                            {!! __icon('interface-essential', 'eye-hidden', 'w-4 h-4') !!}
                        </span>
                    </div>
                </div>

                <div class="w-full px-[10px] pb-[6px] absolute bottom-0" :class="{'hidden': !shown()}">
                    <span class="text-xs" x-text="shown() ? password : ''"></span>
                </div>
            </div>
        </div>
        <a class="yena-button-stack --black cursor-pointer" wire:click="install">
            <div wire:loading wire:target="install">
                <div class="loader-animation-container flex">
                    <div class="inner-circles-loader !h-5 !w-5"></div>
                </div>
            </div>

            <span wire:loading.class="hidden" wire:target="install">{{ __('Install') }}</span>
        </a>

    </div>
</div>