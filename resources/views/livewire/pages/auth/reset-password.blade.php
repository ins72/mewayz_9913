<?php

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

use function Livewire\Volt\layout;
use function Livewire\Volt\rules;
use function Livewire\Volt\state;

layout('components.layouts.base');

state('token')->locked();

state([
    'email' => fn () => request()->string('email')->value(),
    'password' => '',
    'password_confirmation' => ''
]);

rules([
    'token' => ['required'],
    'email' => ['required', 'string', 'email'],
    'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
]);

$resetPassword = function () {
    $this->validate();

    // Here we will attempt to reset the user's password. If it is successful we
    // will update the password on an actual user model and persist it to the
    // database. Otherwise we will parse the error and return the response.
    $status = Password::reset(
        $this->only('email', 'password', 'password_confirmation', 'token'),
        function ($user) {
            $user->forceFill([
                'password' => Hash::make($this->password),
                'remember_token' => Str::random(60),
            ])->save();

            event(new PasswordReset($user));
        }
    );

    // If the password was successfully reset, we will redirect the user back to
    // the application's home authenticated view. If there is an error we can
    // redirect them back to where they came from with their error message.
    if ($status != Password::PASSWORD_RESET) {
        $this->addError('email', __($status));

        return;
    }

    Session::flash('success.success', __($status));

    $this->redirectRoute('login', navigate: true);
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
            
                            <p class="yena-text">{{ __('Enter your new password.') }}</p>
                            
                            <form wire:submit="resetPassword">
                                <!-- Email Address -->
                                <div>
                                    <div class="form-input">
                                        <label>{{ __('Your Email') }}</label>
                                        <input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required autofocus autocomplete="username">
                                    </div>
                                </div>
                                <div>
                                    <div class="form-input mt-4">
                                        <label>{{ __('New Password') }}</label>
                                        <input wire:model="password" id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password">
                                    </div>
                                </div>
                                <div>
                                    <div class="form-input mt-4">
                                        <label>{{ __('Confirm Password') }}</label>
                                        <input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full"
                                        type="password"
                                        name="password_confirmation" required autocomplete="new-password">
                                    </div>
                                </div>

                                <div class="flex items-center justify-end mt-4">
                                    <button type="submit" class="yena-button-stack w-full">
                                        <div wire:loading wire:target="resetPassword"> 
                                            <div class="loader-animation-container flex items-center justify-center"><div class="inner-circles-loader !w-5 !h-5"></div></div>
                                        </div>
                                        <div wire:loading.class="hidden" wire:target="resetPassword">
                                            {{ __('Reset Password') }}
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
