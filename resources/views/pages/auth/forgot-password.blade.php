<x-layouts.guest title="Reset Password - Mewayz">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo">M</div>
                <h1 class="auth-title">Reset Password</h1>
                <p class="auth-subtitle">Enter your email to receive a reset link</p>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="auth-success">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Error Display -->
            @if ($errors->any())
                <div class="auth-error">
                    <ul class="list-none">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Password Reset Form -->
            <form method="POST" action="{{ route('password.email') }}" class="auth-form">
                @csrf
                
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input 
                        id="email" 
                        name="email" 
                        type="email" 
                        class="form-input" 
                        value="{{ old('email') }}" 
                        required 
                        autocomplete="email" 
                        autofocus 
                        placeholder="Enter your email"
                    >
                </div>

                <button type="submit" class="btn btn-primary">
                    Send Reset Link
                </button>
            </form>

            <div class="auth-footer">
                <p>
                    Remember your password? 
                    <a href="{{ route('login') }}" class="auth-link">
                        Sign in
                    </a>
                </p>
            </div>
        </div>
    </div>
</x-layouts.guest>