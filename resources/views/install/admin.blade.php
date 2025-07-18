@extends('install.layout')

@section('title', 'Admin User Creation - Mewayz Installation')

@section('content')
<div class="install-card rounded-2xl shadow-2xl p-8 max-w-2xl mx-auto">
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
        </div>
        <h2 class="text-3xl font-bold text-gray-800 mb-2">Create Admin User</h2>
        <p class="text-gray-600">Set up your administrator account</p>
    </div>

    <form id="adminForm" class="space-y-6">
        <!-- Full Name -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
            <input type="text" name="name" 
                   class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                   placeholder="Enter your full name" required>
        </div>

        <!-- Email Address -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
            <input type="email" name="email" 
                   class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                   placeholder="admin@yourdomain.com" required>
        </div>

        <!-- Password -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
            <input type="password" name="password" 
                   class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                   placeholder="Enter a secure password" required>
            <div class="mt-2 text-sm text-gray-600">
                Password must be at least 8 characters long
            </div>
        </div>

        <!-- Confirm Password -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
            <input type="password" name="password_confirmation" 
                   class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                   placeholder="Confirm your password" required>
        </div>

        <!-- Admin Permissions Info -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center space-x-2 mb-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h4 class="font-medium text-blue-800">Admin Account Privileges</h4>
            </div>
            <ul class="text-sm text-blue-700 space-y-1">
                <li>• Full access to all platform features</li>
                <li>• User management and subscription control</li>
                <li>• System settings and environment variables</li>
                <li>• Database management and analytics</li>
                <li>• API key and integration management</li>
            </ul>
        </div>

        <!-- Security Notice -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-center space-x-2 mb-2">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.314 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <h4 class="font-medium text-yellow-800">Security Reminder</h4>
            </div>
            <p class="text-sm text-yellow-700">
                This admin account will have complete control over your Mewayz platform. 
                Choose a strong password and keep these credentials secure.
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-between items-center pt-6">
            <button type="button" onclick="window.installer.nextStep('environment')" 
                    class="bg-gray-200 text-gray-700 font-semibold py-2 px-6 rounded-lg hover:bg-gray-300 transition-colors">
                Back
            </button>
            
            <button type="submit" 
                    class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold py-2 px-6 rounded-lg shadow-lg hover:from-indigo-600 hover:to-purple-700 transition-all duration-300">
                Create Admin Account
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.getElementById('adminForm').addEventListener('submit', function(e) {
        e.preventDefault();
        createAdminUser();
    });

    function createAdminUser() {
        const form = document.getElementById('adminForm');
        const formData = new FormData(form);
        const submitBtn = form.querySelector('[type="submit"]');
        
        // Check if passwords match
        const password = formData.get('password');
        const confirmPassword = formData.get('password_confirmation');
        
        if (password !== confirmPassword) {
            window.installer.showError('Passwords do not match');
            return;
        }
        
        if (password.length < 8) {
            window.installer.showError('Password must be at least 8 characters long');
            return;
        }
        
        window.installer.showLoading(submitBtn, 'Creating Admin User...');
        
        axios.post('/install/process/admin', formData)
            .then(response => {
                if (response.data.success) {
                    window.installer.showSuccess(response.data.message);
                    setTimeout(() => {
                        window.installer.nextStep(response.data.nextStep);
                    }, 1000);
                } else {
                    window.installer.showError(response.data.message);
                }
            })
            .catch(error => {
                let errorMessage = 'Failed to create admin user';
                if (error.response && error.response.data) {
                    errorMessage = error.response.data.message || errorMessage;
                    
                    // Show validation errors
                    if (error.response.data.errors) {
                        const errors = Object.values(error.response.data.errors).flat();
                        errorMessage = errors.join(', ');
                    }
                }
                window.installer.showError(errorMessage);
            })
            .finally(() => {
                window.installer.hideLoading(submitBtn, 'Create Admin Account');
            });
    }
</script>
@endpush
@endsection