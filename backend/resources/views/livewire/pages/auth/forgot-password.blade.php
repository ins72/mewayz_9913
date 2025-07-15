<?php

use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;

use function Livewire\Volt\layout;
use function Livewire\Volt\rules;
use function Livewire\Volt\state;

layout('components.layouts.base');

state(['email' => '']);

rules(['email' => ['required', 'string', 'email']]);

$sendPasswordResetLink = function () {
    $this->validate();

    // We will send the password reset link to this user. Once we have attempted
    // to send the link, we will examine the response then see the message we
    // need to show to the user. Finally, we'll send out a proper response.
    $status = Password::sendResetLink(
        $this->only('email')
    );

    if ($status != Password::RESET_LINK_SENT) {
        $this->addError('email', __($status));

        return;
    }

    $this->reset('email');

    session()->flash('success.success', __($status));
};

?>

<div>
    <div>
        <div x-data>
          <div class="flex min-h-screen flex-col md:!flex-row">
            <div class="flex items-center justify-center bg-[var(--yena-colors-white)] flex-1 md:max-w-[var(--yena-sizes-container-sm)]">
  
                <div class="h-screen w-full">
            
                    <div class="h-full !max-w-full p-12 md:p-12 lg:p-24">
            
                        <div class="flex flex-col gap-[var(--yena-space-6)] flex-1 h-full w-full">
                            <img src="{{ logo() }}" class="h-16 w-16 object-contain" alt=" " width="36" class="block">
            
                            <div class="flex-1 place-self-stretch"></div>
            
                            {{-- <div>{{ __('🎉 Welcome to the party') }}</div> --}}
                            <h2 class="text-4xl relative font-medium leading-[1.2em] tracking-[-0.03em] md:text-4xl">{{ __('Reset Password') }}</h2>
            
                            <p class="yena-text">{{ __('Don’t worry, we will send you a reset link.') }}</p>
                            
                            <form wire:submit="sendPasswordResetLink">
                                <!-- Email Address -->
                                <div>
                                    <div class="form-input">
                                        <label>{{ __('Your Email') }}</label>
                                        <input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required autofocus>
                                    </div>
                                </div>

                                <div class="flex items-center justify-end mt-4">
                                    <button type="submit" class="yena-button-stack w-full">
                                        
                                        <div wire:loading wire:target="sendPasswordResetLink"> 
                                            <div class="loader-animation-container flex items-center justify-center"><div class="inner-circles-loader !w-5 !h-5"></div></div>
                                        </div>
                                        <div wire:loading.class="hidden" wire:target="sendPasswordResetLink">
                                            {{ __('Reset') }}
                                        </div>
                                    </button>
                                </div>
                                <div class="mt-4 text-[11px] text-left">{{ __('Have an account?') }}
                                    <a href="{{ route('login') }}" wire:navigate class=""><b>{{ __('Login.') }}</b></a>
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
                                @if ($message = Session::get('success.success'))
                                    <div class="mb-5 mt-2 bg-green-200 text-[11px] p-1 px-2 rounded-md">
                                        <div class="flex items-center">
                                            <div>
                                                <i class="fi fi-rr-cross-circle flex"></i>
                                            </div>
                                            <div class="flex-grow ml-1">{{ $message }}</div>
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
                            {{-- <div class="text-[11px] text-center color-gray mt-5">
                                @php
                                    $terms_link = settings('others.terms');
                                    $privacy_link = settings('others.privacy');
                                @endphp
                                {!! __t("By joining, I agree to the <a href=\"$terms_link\">Terms of service</a> and <a href=\"$privacy_link\">Privacy policy</a>.") !!}
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
            
            <div style="--bg-url: url({{ login_image() }})" class="[background:var(--bg-url)_center_bottom_/_cover_no-repeat] flex-1 relative overflow-hidden  hidden md:!flex">
            </div>
        </div>
      </div>
  
    </div>
</div>
