<?php

namespace App\Livewire\Pages\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Validate;

class ForgotPasswordModal extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    public function sendPasswordResetLink()
    {
        $this->validate();

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            ['email' => $this->email]
        );

        if ($status === Password::RESET_LINK_SENT) {
            session()->flash('status', trans($status));
            $this->reset('email');
        } else {
            $this->addError('email', trans($status));
        }
    }

    public function render()
    {
        return view('livewire.pages.auth.forgot-password-modal');
    }
} 