<?php

use App\Livewire\Actions\Logout;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use function Livewire\Volt\layout;

layout('components.layouts.base');

$sendVerification = function () {
    if (Auth::user()->hasVerifiedEmail()) {
        $this->redirect(
            session('url.intended', RouteServiceProvider::HOME),
            navigate: true
        );

        return;
    }

    Auth::user()->sendEmailVerificationNotification();

    Session::flash('status', 'verification-link-sent');
};

$logout = function (Logout $logout) {
    $logout();

    $this->redirect(route('login'), navigate: true);
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
                            <h2 class="text-4xl relative font-medium leading-[1.2em] tracking-[-0.03em] md:text-4xl">{{ __('Thanks for signing up!') }}</h2>
            
                            <p class="yena-text">{{ __('Could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}</p>
                            
                            
                            <div>

            
                                <button type="button" wire:click="sendVerification" class="yena-button-stack w-full">
                                    <div wire:loading wire:target="sendVerification"> 
                                        <div class="loader-animation-container flex items-center justify-center"><div class="inner-circles-loader !w-5 !h-5"></div></div>
                                    </div>
                                    <div wire:loading.class="hidden" wire:target="sendVerification">
                                        {{ __('Resend') }}
                                    </div>
                                </button>

                                <div class="mt-4 text-[14px] underline text-left cursor-pointer">
                                    <a wire:click="logout" ><b>{{ __('Logout') }}</b></a>
                                </div>
                                @if (session('status') == 'verification-link-sent')
                                    <div class="mb-5 mt-3 bg-green-200 text-[11px] p-1 px-2 rounded-md">
                                        <div class="flex items-center">
                                            <div>
                                                <i class="fi fi-rr-cross-circle flex"></i>
                                            </div>
                                            <div class="flex-grow ml-1">{{ __('A new verification link has been sent to the email address you provided during registration.') }}</div>
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
