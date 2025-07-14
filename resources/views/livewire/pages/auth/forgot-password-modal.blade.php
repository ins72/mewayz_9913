<?php

use Illuminate\Support\Facades\Password;

use function Livewire\Volt\state;

state('email', '');
state('emailSent', false);

$sendPasswordResetLink = function () {
    $this->validate([
        'email' => ['required', 'string', 'email'],
    ]);

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

    $this->emailSent = true;
};

?>

<div>
    <div>
        
        <form wire:submit.prevent="sendPasswordResetLink">
            <div class="floating-input">
                <input type="email" wire:model="email" class="form-control modallogin-inpt shadow-none" id="forgot_password_email" required>
                <label for="forgot_password_email">Email</label>
                @error('email')
                    <span class="text-danger text-xs">{{ $message }}</span>
                @enderror
            </div>

            <div class="mt-4">
                <div class="sing-in">
                    <button type="submit" class="sign-btn border-0 w-100">
                        <div wire:loading wire:target="sendPasswordResetLink"> 
                            <div class="loader-animation-container flex items-center justify-center"><div class="inner-circles-loader !w-5 !h-5"></div></div>
                        </div>
                        <div wire:loading.class="!hidden" wire:target="sendPasswordResetLink" style="text-align: center;">
                            Send Password Reset Link
                        </div>
                    </button>
                </div>
            </div>
            
            @if (session('status'))
                <div class="alert alert-success mt-3">
                    {{ session('status') }}
                </div>
            @endif
        </form>
    </div>
</div> 