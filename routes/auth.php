<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware('guest')->group(function () {
    Route::get('register', function () {
        return view('pages.auth.register');
    })->name('register');

    Route::get('login', function () {
        return view('pages.auth.login');
    })->name('login');

    Route::get('forgot-password', function () {
        return view('pages.auth.forgot-password');
    })->name('password.request');

    Route::get('reset-password/{token}', function ($token) {
        return view('pages.auth.reset-password', ['token' => $token]);
    })->name('password.reset');
});

Route::middleware('auth')->group(function () {
    Volt::route('verify-email', 'pages.auth.verify-email')
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Volt::route('confirm-password', 'pages.auth.confirm-password')
        ->name('password.confirm');
});
