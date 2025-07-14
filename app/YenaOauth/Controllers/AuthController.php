<?php

namespace App\YenaOauth\Controllers;

use Illuminate\Http\RedirectResponse;
use App\YenaOauth\Facades\YenaOauth;

class AuthController
{
    /**
     * Handle redirecting the user to the OAuth provider.
     */
    public function redirect(string $driver): RedirectResponse
    {
        return YenaOauth::redirect($driver);
    }

    /**
     * Handle an OAuth response from the provider.
     */
    public function callback(string $driver): RedirectResponse
    {
        return YenaOauth::callback($driver);
    }
}
