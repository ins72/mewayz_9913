<?php

use App\Models\Plan;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

use function Livewire\Volt\layout;
use function Livewire\Volt\rules;
use function Livewire\Volt\state;

layout('components.layouts.base');

state([
    'name' => '',
    'email' => '',
    'password' => '',
    'password_confirmation' => ''
]);

rules([
    'name' => ['required', 'string', 'max:255'],
    'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
    'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
]);

$register = function () {
    $validated = $this->validate();

    $validated['password'] = Hash::make($validated['password']);
    $user = User::create($validated);

    if(config('app.email_verification')){
        $user->email_verified_at = null;
        $user->save();
    }

    event(new Registered($user));

    Auth::login($user);

    if ($plan = Plan::where('is_free', 1)->where('status', 1)->first()) {
        $user->cancelCurrentSubscription();
        $user->upgradeCurrentPlanTo($plan, 2999, false, false);

        $plan_history = new \App\Models\PlansHistory;
        $plan_history->plan_id = $plan->id;
        $plan_history->user_id = $user->id;
        $plan_history->save();
    }

    // Check for free plan

    $this->redirect(route('console-index'), navigate: false);
};

?>

<div>
    
    <div x-data="_register">
        <div class="min-h-screen bg-[#101010] flex items-center justify-center px-4">
            <div class="w-full max-w-md">
  
                <!-- Logo Section -->
                <div class="text-center mb-8">
                    <div class="mx-auto w-20 h-20 bg-gradient-to-br from-[#FDFDFD] to-[#E5E5E5] rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                        <div class="text-[#141414] font-bold text-2xl">M</div>
                    </div>
                    <h1 class="text-3xl font-bold text-[#F1F1F1] mb-2">Create Account</h1>
                    <p class="text-[#7B7B7B] text-lg">Join Mewayz and start growing your business</p>
                </div>
            
                            {{-- <p class="yena-text">{{ __('Don’t worry, we will send you a reset link.') }}</p> --}}

                            @if (config('app.FACEBOOK_ENABLE') || config('app.GOOGLE_ENABLE'))
                            <div x-show="!email" class="mt-5" x-transition:enter="transition-transform transition-opacity ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform -translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0"
                            x-transition:leave="transition ease-in duration-300"
                            x-transition:leave-end="opacity-0 transform -translate-y-3">
                                <div class="grid grid-cols-2 gap-2">
                                    @if (config('app.GOOGLE_ENABLE'))
                                    <a href="{{ route('auth.driver.redirect', 'google') }}">
                                        <button type="button" class="yena-button-stack w-full">
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
                                        <button type="button" class="yena-button-stack w-full">
    
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
                                    <hr class="opacity-60 [border-image:none] [border-color:inherit] border-solid w-full">
    
                                    <p class="text-sm whitespace-nowrap">{{ __('or') }}</p>
    
                                    <hr class="opacity-60 [border-image:none] [border-color:inherit] border-solid w-full">
                                </div>
                            </div>
                            @endif
                            
                            <form @submit.prevent="!proceedShow ? proceedShow=true : $wire.register()">
                                <div>
                                    <div class="form-input">
                                        <label>{{ __('Your Email') }}</label>
                                        <input x-model="email" id="email" class="block mt-1 w-full" type="email" name="email" @input="checkValidEmail" required autocomplete="username">
                                    </div>

                                    <div x-show="proceedShow && email" x-cloak x-transition>
                                        <div class="form-input mt-4">
                                            <label>{{ __('Your Name') }}</label>
                                            <input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" required autofocus autocomplete="name">
                                        </div>
                                        <div class="form-input mt-4">
                                            <label>{{ __('Your Password') }}</label>
                                            <div class="relative">
                                                <input class="transition-all block mt-1 w-full" :class="{'pb-[2.4rem!important]' : shown()}" x-model="password" id="password" type="password" name="password" required autocomplete="new-password" placeholder="*******" >
                
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
                                        <div class="form-input mt-4">
                                            <label>{{ __('Confirm Password') }}</label>
                                            <input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password">
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-end mt-4" :class="{
                                    '!hidden': proceedShow
                                }">
                                    <button type="button" @click="proceedShow=true;" x-cloak x-show="email" x-transition :class="{
                                        '!opacity-40 cursor-not-allowed [box-shadow:var(--yena-shadows-none)] pointer-events-none': !isValidEmail
                                     }" class="yena-button-stack w-full">{{ __('Continue with email') }}</button>
                                </div>

                                <div class="flex items-center justify-end mt-4" :class="{
                                    '!hidden': !proceedShow
                                }">
                                    <button type="submit" x-cloak x-show="email" x-transition :class="{
                                        '!opacity-40 cursor-not-allowed [box-shadow:var(--yena-shadows-none)] pointer-events-none': !isValidEmail
                                     }" class="yena-button-stack w-full">
                                        <div wire:loading wire:target="register"> 
                                            <div class="loader-animation-container flex items-center justify-center"><div class="inner-circles-loader !w-5 !h-5"></div></div>
                                        </div>
                                        <div wire:loading.class="hidden" wire:target="register">
                                            {{ __('Continue with email') }}
                                        </div>
                                    </button>
                                </div>

                                <div class="mt-4 text-[11px] text-left">{{ __('Have an account?') }}
                                    <a href="{{ route('login') }}" wire:navigate><b>{{ __('Login.') }}</b></a>
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
                                    <div class="mb-5 mt-2 bg-red-200 text-[11px] p-1 px-2 rounded-md">
                                        <div class="flex items-center">
                                            <div>
                                                <i class="fi fi-rr-cross-circle flex"></i>
                                            </div>
                                            <div class="flex-grow ml-1">{{ $error }}</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="text-xs text-left color-gray mt-5 terms-o">
                                @php
                                    $terms_link = settings('others.terms');
                                    $privacy_link = settings('others.privacy');
                                @endphp
                                {!! __t("By joining, I agree to the <a href=\"$terms_link\">Terms of service</a> and <a href=\"$privacy_link\">Privacy policy</a>.") !!}
                            </div>
            
                            <div class="flex-1 place-self-stretch"></div>
            
                            <div class="flex items-center justify-center">
                                <div>
                                    <img src="{{ logo_icon() }}" class="h-10 w-10 object-contain" alt=" " width="36" class="block">
                                </div>
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
            Alpine.data('_register', () => {
                return {
                    email: @entangle('email'),
                    password: @entangle('password'),
                    showPassword: false,
                    isValidEmail: false,
                    proceedShow: false,
                    checkValidEmail(){
                        this.isValidEmail = false;
                        if(this.email.match(/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/)){
                            this.isValidEmail = true;
                        }

                        if(!this.isValidEmail) {
                            this.proceedShow = false;   
                        }
                    },

                    shown(){
                        if(this.password !== '' && this.password !== null && this.showPassword) return true;
                        return false;
                    },
                }
            });
        </script>
    @endscript
</div>
