@extends('layouts.app')

@section('title', 'Sign Up - Mewayz')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">
        <div class="card">
            <div class="text-center mb-8">
                <div class="logo" style="width: 3rem; height: 3rem; background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary)); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.5rem; color: white;">
                    M
                </div>
                <h1 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                    Create Account
                </h1>
                <p style="color: var(--text-secondary); font-size: 0.875rem;">
                    Start building your digital empire today
                </p>
            </div>
            
            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf
                
                <div>
                    <label for="name" class="form-label">Full Name</label>
                    <input id="name" type="text" name="name" class="form-input" value="{{ old('name') }}" required autofocus>
                    @error('name')
                        <div style="color: var(--accent-error); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>
                
                <div>
                    <label for="email" class="form-label">Email Address</label>
                    <input id="email" type="email" name="email" class="form-input" value="{{ old('email') }}" required>
                    @error('email')
                        <div style="color: var(--accent-error); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>
                
                <div>
                    <label for="password" class="form-label">Password</label>
                    <input id="password" type="password" name="password" class="form-input" required>
                    @error('password')
                        <div style="color: var(--accent-error); font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>
                
                <div>
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" class="form-input" required>
                </div>
                
                <div>
                    <label style="display: flex; align-items: flex-start; gap: 0.5rem; font-size: 0.875rem; color: var(--text-secondary);">
                        <input type="checkbox" name="terms" style="width: 1rem; height: 1rem; accent-color: var(--accent-primary); margin-top: 0.125rem;" required>
                        <span>
                            I agree to the 
                            <a href="/terms-of-service" style="color: var(--accent-primary); text-decoration: none;">Terms of Service</a>
                            and 
                            <a href="/privacy-policy" style="color: var(--accent-primary); text-decoration: none;">Privacy Policy</a>
                        </span>
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary w-full">
                    Create Account
                </button>
            </form>
            
            <div style="margin-top: 2rem; text-align: center;">
                <p style="color: var(--text-secondary); font-size: 0.875rem;">
                    Already have an account?
                    <a href="{{ route('login') }}" style="color: var(--accent-primary); text-decoration: none; font-weight: 500;">
                        Sign in
                    </a>
                </p>
            </div>
            
            <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border-primary);">
                <div style="text-align: center; margin-bottom: 1rem;">
                    <span style="color: var(--text-secondary); font-size: 0.875rem;">Or continue with</span>
                </div>
                
                <div class="flex gap-3">
                    <button type="button" class="btn btn-secondary flex-1">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        Google
                    </button>
                    
                    <button type="button" class="btn btn-secondary flex-1">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        Facebook
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection