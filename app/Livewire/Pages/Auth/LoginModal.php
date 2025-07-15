<?php

namespace App\Livewire\Pages\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Validate;

class LoginModal extends Component
{
    /**
     * Form properties directly on the component
     */
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    public function login()
    {
        // Validate the input
        $this->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Attempt to authenticate
        $credentials = [
            'email' => $this->email,
            'password' => $this->password,
        ];

        if (! auth()->attempt($credentials, $this->remember)) {
            $this->addError('email', trans('auth.failed'));
            return;
        }

        // Success - regenerate session and redirect
        Session::regenerate();

        return redirect()->intended(route('dashboard-index'));
    }

    public function render()
    {
        return view('livewire.pages.auth.login-modal');
    }
} 