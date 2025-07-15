<?php

use App\Livewire\Forms\LoginForm;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Session;

use function Livewire\Volt\form;
use function Livewire\Volt\state;
use function Livewire\Volt\layout;

layout('components.layouts.base');

form(LoginForm::class);

$login = function () {
    $this->validate();

    $this->form->authenticate();

    Session::regenerate();

    $this->redirect(route('console-index'),
        navigate: false
    );
};

?>
<div>

    {{-- <div x-init="window.__livewirelazy($wire.__instance)"></div> --}}
    <div>
        <div x-data="_login">
          <div class="flex min-h-screen flex-col md:!flex-row bg-gray-50 dark:bg-gray-900">
            <div class="flex items-center justify-center bg-white dark:bg-gray-800 flex-1 md:max-w-[var(--yena-sizes-container-sm)] border-r border-gray-200 dark:border-gray-700">
  
                <div class="h-screen w-full">
            
                    <div class="h-full !max-w-full p-12 md:p-12 lg:p-24">
            
                        <div class="flex flex-col gap-[var(--yena-space-6)] flex-1 h-full w-full">
                            <img src="{{ logo() }}" class="h-16 w-16 object-contain" alt=" " width="36" class="block">
            
                            <div class="flex-1 place-self-stretch"></div>
            
                            {{-- <div>{{ __('🎉 Welcome to the party') }}</div> --}}
                            <h2 class="text-4xl relative font-medium leading-[1.2em] tracking-[-0.03em] md:text-4xl text-gray-900 dark:text-white">{{ __('Welcome back ✨') }}</h2>
            
                            <div inline="true" class="inline-flex items-center text-[14px] text-gray-600 dark:text-gray-400">
                                <div class="m-0">
                                  <i class="fi fi-rr-info flex"></i>
                                </div>
                                <div class="ml-1">{{ __('You\'re new here?') }}
                                  <a href="{{ route('register') }}" x-link.prefetch class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300"><b>{{ __('Create an account.') }}</b></a></div>
                             </div>
                            {{-- <p class="yena-text">{{ __('Don’t worry, we will send you a reset link.') }}</p> --}}
                            

                            @if (config('app.FACEBOOK_ENABLE') || config('app.GOOGLE_ENABLE'))
                            <div class="mt-5">
                                <div class="grid grid-cols-2 gap-2">
                                    @if (config('app.GOOGLE_ENABLE'))
                                    <a href="{{ route('auth.driver.redirect', 'google') }}">
                                        <button type="button" class="btn btn-secondary w-full">
                                            <div class="flex items-center justify-center">
                                                <div>
                                                    {!! __i('others', 'google-icon', 'w-6 h-6') !!}
                                                </div>
                                            </div>
                                        </button>
                                    </a>
                                    @endif
                                    @if (config('app.FACEBOOK_ENABLE'))
                                    <a href="{{ route('auth.driver.redirect', 'facebook') }}">
                                        <button type="button" class="btn btn-secondary w-full">
    
                                            <div class="flex items-center justify-center">
                                                <div>
                                                    <i class="fi fi-brands-facebook flex text-[#3b5998]"></i>
                                                </div>
                                            </div>
                                        </button>
                                    </a>
                                    @endif
                                </div>
                                <div class="flex items-center flex-row gap-2 mt-6">
                                    <hr class="opacity-60 [border-image:none] [border-color:inherit] border-solid w-full border-gray-300 dark:border-gray-600">
    
                                    <p class="text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">{{ __('or') }}</p>
    
                                    <hr class="opacity-60 [border-image:none] [border-color:inherit] border-solid w-full border-gray-300 dark:border-gray-600">
                                </div>
                            </div>
                            @endif
                            <form wire:submit="login">

                                <div class="form-input">
                                    <label class="text-gray-700 dark:text-gray-300">{{ __('Your Email') }}</label>
                                    <input type="email" name="email" x-model="email" placeholder="{{ __('e.g: email@gmail.com') }}" class="form-input bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white border-gray-300 dark:border-gray-600 focus:border-primary-500 dark:focus:border-primary-400">
                                </div>
                                <div class="form-input mt-4">
                                    <label class="text-gray-700 dark:text-gray-300">{{ __('Your Password') }}</label>
                                    <div class="relative">
                                        <input type="password" name="password" x-model="password" placeholder="*******" class="form-input bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white border-gray-300 dark:border-gray-600 focus:border-primary-500 dark:focus:border-primary-400 transition-all" :class="{'pb-[2.4rem!important]' : shown()}">
        
                                        <div class="p-2 absolute right-1 cursor-pointer text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300" :class="{'top-2/4 transform -translate-y-1/2': !shown(), 'top-1': shown()}" @click="showPassword =! showPassword">
                                            <span x-cloak :class="{'hidden': !shown()}">
                                                {!! __icon('interface-essential', 'eye-show-visible', 'w-4 h-4') !!}
                                            </span>
                                            <span x-cloak :class="{'hidden': shown()}">
                                                {!! __icon('interface-essential', 'eye-hidden', 'w-4 h-4') !!}
                                            </span>
                                        </div>
                                    </div>
        
                                    <div class="w-full px-[10px] pb-[6px] absolute bottom-0" :class="{'hidden': !shown()}">
                                        <span class="text-xs text-gray-500 dark:text-gray-400" x-text="shown() ? password : ''"></span>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary w-full mt-5" :class="{'opacity-40 cursor-not-allowed [box-shadow:var(--yena-shadows-none)] pointer-events-none': email == '' || email == null || password == '' || password == null}">
                                    <div wire:loading wire:target="login"> 
                                        <div class="loader-animation-container flex items-center justify-center"><div class="inner-circles-loader !w-5 !h-5"></div></div>
                                    </div>
                                    <div class="flex justify-center items-center" wire:loading.class="!hidden" wire:target="login">
                                        <div>{{ __('Login now') }}</div>
                                        <div>
                                            <i class="fi fi-rr-angle-small-right flex"></i>
                                        </div>
                                    </div>
                                </button>
                                
                                
                                <div class="mt-4 text-[11px] text-left text-gray-600 dark:text-gray-400">{{ __('Forgot your password?') }}
                                    <a href="{{ route('password.request') }}" x-link.prefetch class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300"><b>{{ __('Reset it here.') }}</b></a>
                                </div>
                            </form>
                            <div>
            
                                @php
                                    $error = false;
                            
                                    if(!$errors->isEmpty()){
                                        $error = $errors->first();
                                    }
                                    if(Session::get('error')) $error = Session::get('error');
                                @endphp
                                
                                @if ($error)
                                    <div class="mb-5 mt-2 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 text-[11px] p-1 px-2 rounded-md border border-red-200 dark:border-red-800">
                                        <div class="flex items-center">
                                            <div>
                                                <i class="fi fi-rr-cross-circle flex"></i>
                                            </div>
                                            <div class="flex-grow ml-1">{{ $error }}</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
            
                            <div class="flex-1 place-self-stretch"></div>
            
                            <div class="flex items-center justify-center">
                                <div>
                                    <img src="{{ logo_icon() }}" class="h-10 w-10 object-contain" alt=" " width="36" class="block">
                                </div>
                            </div>
                            <div class="text-[11px] color-gray mt-5 !hidden">
                                @php
                                    $terms_link = settings('others.terms');
                                    $privacy_link = settings('others.privacy');
                                @endphp
                                {!! __t("By logging in, I agree to the <a href=\"$terms_link\">Terms of service</a> and <a href=\"$privacy_link\">Privacy policy</a>. I also agree to receive emails and communication relating to our services and offers.") !!}
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div style="--bg-url: url({{ login_image() }})" class="[background:var(--bg-url)_center_bottom_/_cover_no-repeat] flex-1 relative overflow-hidden  hidden md:!flex">
            </div>
        </div>
      </div>
  
    </div>

    @script
        <script>
            Alpine.data('_login', () => {
                return {
                    email: @entangle('form.email'),
                    password: @entangle('form.password'),
                    showPassword: false,

                    shown(){
                        if(this.password !== '' && this.password !== null && this.showPassword) return true;
                        return false;
                    },
                }
            });
        </script>
    @endscript
</div>
