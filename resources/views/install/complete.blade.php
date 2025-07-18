@extends('install.layout')

@section('title', 'Installation Complete - Mewayz')

@section('content')
<div class="install-card rounded-2xl shadow-2xl p-8 max-w-3xl mx-auto">
    <div class="text-center mb-8">
        <div class="w-20 h-20 bg-gradient-to-br from-green-400 to-blue-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <h2 class="text-3xl font-bold text-gray-800 mb-2">Installation Complete!</h2>
        <p class="text-gray-600">Your Mewayz platform is ready to use</p>
    </div>

    <div class="space-y-6">
        <!-- Success Message -->
        <div class="bg-green-50 border border-green-200 rounded-lg p-6">
            <div class="flex items-center space-x-3 mb-4">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-green-800">Congratulations!</h3>
            </div>
            <p class="text-green-700">
                Mewayz has been successfully installed and configured. You can now start building your online presence with our comprehensive platform.
            </p>
        </div>

        <!-- Installation Summary -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">What's Been Set Up</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-gray-700">Database configured and migrated</span>
                </div>
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-gray-700">Admin user created</span>
                </div>
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-gray-700">Environment configured</span>
                </div>
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-gray-700">Cache and queues optimized</span>
                </div>
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-gray-700">File storage configured</span>
                </div>
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-gray-700">All services initialized</span>
                </div>
            </div>
        </div>

        <!-- Quick Start Guide -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-blue-800 mb-4">Quick Start Guide</h3>
            <div class="space-y-3">
                <div class="flex items-start space-x-3">
                    <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mt-1">
                        <span class="text-blue-600 font-semibold text-sm">1</span>
                    </div>
                    <div>
                        <h4 class="font-medium text-blue-800">Access your dashboard</h4>
                        <p class="text-blue-700 text-sm">Log in with your admin credentials to start managing your platform</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mt-1">
                        <span class="text-blue-600 font-semibold text-sm">2</span>
                    </div>
                    <div>
                        <h4 class="font-medium text-blue-800">Complete your profile</h4>
                        <p class="text-blue-700 text-sm">Set up your workspace and customize your platform settings</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mt-1">
                        <span class="text-blue-600 font-semibold text-sm">3</span>
                    </div>
                    <div>
                        <h4 class="font-medium text-blue-800">Explore features</h4>
                        <p class="text-blue-700 text-sm">Discover social media management, e-commerce, courses, and more</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Notice -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-center space-x-2 mb-2">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.314 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <h4 class="font-medium text-yellow-800">Security Recommendation</h4>
            </div>
            <p class="text-sm text-yellow-700">
                For security purposes, consider removing the installer files after installation is complete. 
                You can delete the <code class="bg-yellow-100 px-1 rounded">/install</code> route from your routes file.
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-center space-x-4 pt-6">
            <a href="/login" class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold py-3 px-8 rounded-lg shadow-lg hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 transform hover:scale-105">
                Access Dashboard
            </a>
            <a href="/" class="bg-gray-200 text-gray-700 font-semibold py-3 px-8 rounded-lg hover:bg-gray-300 transition-colors">
                View Homepage
            </a>
        </div>
    </div>

    <!-- Footer -->
    <div class="text-center mt-8 pt-6 border-t border-gray-200">
        <p class="text-gray-600 text-sm">
            Thank you for choosing Mewayz! Need help? Visit our 
            <a href="/docs" class="text-indigo-600 hover:underline">documentation</a> or 
            <a href="/support" class="text-indigo-600 hover:underline">support center</a>.
        </p>
    </div>
</div>

@push('scripts')
<script>
    // Confetti animation for completion
    function createConfetti() {
        const colors = ['#667eea', '#764ba2', '#f093fb', '#f5576c', '#4facfe', '#00f2fe'];
        const confettiContainer = document.createElement('div');
        confettiContainer.style.position = 'fixed';
        confettiContainer.style.top = '0';
        confettiContainer.style.left = '0';
        confettiContainer.style.width = '100%';
        confettiContainer.style.height = '100%';
        confettiContainer.style.pointerEvents = 'none';
        confettiContainer.style.zIndex = '9999';
        document.body.appendChild(confettiContainer);

        for (let i = 0; i < 50; i++) {
            const confetti = document.createElement('div');
            confetti.style.position = 'absolute';
            confetti.style.width = '10px';
            confetti.style.height = '10px';
            confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
            confetti.style.left = Math.random() * 100 + '%';
            confetti.style.top = '-10px';
            confetti.style.borderRadius = '50%';
            confetti.style.animation = `confetti-fall ${Math.random() * 3 + 2}s linear forwards`;
            confettiContainer.appendChild(confetti);
        }

        // Remove confetti after animation
        setTimeout(() => {
            confettiContainer.remove();
        }, 5000);
    }

    // Add confetti animation CSS
    const style = document.createElement('style');
    style.textContent = `
        @keyframes confetti-fall {
            to {
                transform: translateY(100vh) rotate(360deg);
            }
        }
    `;
    document.head.appendChild(style);

    // Trigger confetti on page load
    setTimeout(createConfetti, 500);
</script>
@endpush
@endsection