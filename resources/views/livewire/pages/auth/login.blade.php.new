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
<div class="min-h-screen bg-[#101010] flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <!-- Logo Section -->
        <div class="text-center mb-8">
            <div class="mx-auto w-20 h-20 bg-gradient-to-br from-[#FDFDFD] to-[#E5E5E5] rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                <div class="text-[#141414] font-bold text-2xl">M</div>
            </div>
            <h1 class="text-3xl font-bold text-[#F1F1F1] mb-2">Welcome Back</h1>
            <p class="text-[#7B7B7B] text-lg">Sign in to continue to your Mewayz workspace</p>
        </div>

        <!-- Login Form -->
        <div class="bg-[#191919] rounded-2xl p-8 border border-[#282828]">
            <form wire:submit="login" class="space-y-6">
                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-[#F1F1F1] mb-2">Email</label>
                    <input 
                        wire:model="form.email" 
                        type="email" 
                        id="email"
                        placeholder="Enter your email address"
                        class="w-full px-4 py-3 bg-[#191919] border border-[#282828] rounded-xl text-[#F1F1F1] placeholder-[#7B7B7B] focus:outline-none focus:border-[#FDFDFD] focus:ring-2 focus:ring-[#FDFDFD] focus:ring-opacity-20 transition-all duration-200"
                        required
                    />
                    @error('form.email')
                        <p class="mt-1 text-sm text-[#FF3838]">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-[#F1F1F1] mb-2">Password</label>
                    <input 
                        wire:model="form.password" 
                        type="password" 
                        id="password"
                        placeholder="Enter your password"
                        class="w-full px-4 py-3 bg-[#191919] border border-[#282828] rounded-xl text-[#F1F1F1] placeholder-[#7B7B7B] focus:outline-none focus:border-[#FDFDFD] focus:ring-2 focus:ring-[#FDFDFD] focus:ring-opacity-20 transition-all duration-200"
                        required
                    />
                    @error('form.password')
                        <p class="mt-1 text-sm text-[#FF3838]">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input 
                            wire:model="form.remember" 
                            type="checkbox" 
                            class="w-4 h-4 bg-[#191919] border border-[#282828] rounded focus:ring-[#FDFDFD] focus:ring-2 text-[#FDFDFD]"
                        />
                        <span class="ml-2 text-sm text-[#7B7B7B]">Remember me</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm text-[#FDFDFD] hover:text-[#E5E5E5] transition-colors">
                        Forgot password?
                    </a>
                </div>

                <!-- Login Button -->
                <button 
                    type="submit" 
                    class="w-full bg-[#FDFDFD] text-[#141414] font-semibold py-3 px-4 rounded-xl hover:bg-[#E5E5E5] focus:outline-none focus:ring-2 focus:ring-[#FDFDFD] focus:ring-opacity-50 transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98]"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove>Sign In</span>
                    <span wire:loading class="flex items-center justify-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-[#141414]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Signing in...
                    </span>
                </button>

                <!-- Divider -->
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-[#282828]"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-[#191919] text-[#7B7B7B]">or continue with</span>
                    </div>
                </div>

                <!-- Social Login Buttons -->
                @if (config('app.GOOGLE_ENABLE') || config('app.FACEBOOK_ENABLE'))
                <div class="grid grid-cols-2 gap-3">
                    @if (config('app.GOOGLE_ENABLE'))
                    <a href="{{ route('auth.driver.redirect', 'google') }}" class="flex items-center justify-center px-4 py-2 border border-[#282828] rounded-xl bg-[#191919] text-[#F1F1F1] hover:bg-[#282828] transition-colors">
                        <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                            <path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        Google
                    </a>
                    @endif
                    @if (config('app.FACEBOOK_ENABLE'))
                    <a href="{{ route('auth.driver.redirect', 'facebook') }}" class="flex items-center justify-center px-4 py-2 border border-[#282828] rounded-xl bg-[#191919] text-[#F1F1F1] hover:bg-[#282828] transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        Facebook
                    </a>
                    @endif
                </div>
                @endif
            </form>
        </div>

        <!-- Sign Up Link -->
        <div class="text-center mt-8">
            <p class="text-[#7B7B7B]">
                Don't have an account? 
                <a href="{{ route('register') }}" class="text-[#FDFDFD] hover:text-[#E5E5E5] font-semibold transition-colors">
                    Sign up
                </a>
            </p>
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