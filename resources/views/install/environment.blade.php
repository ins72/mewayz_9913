@extends('install.layout')

@section('title', 'Environment Setup - Mewayz Installation')

@section('content')
<div class="install-card rounded-2xl shadow-2xl p-8 max-w-4xl mx-auto">
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
        </div>
        <h2 class="text-3xl font-bold text-gray-800 mb-2">Environment Setup</h2>
        <p class="text-gray-600">Configure your application settings</p>
    </div>

    <form id="environmentForm" class="space-y-8">
        <!-- Application Settings -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                Application Settings
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Application Name</label>
                    <input type="text" name="app_name" value="{{ $envConfig['app_name'] }}" 
                           class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Mewayz" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Application URL</label>
                    <input type="url" name="app_url" value="{{ $envConfig['app_url'] }}" 
                           class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="https://yourdomain.com" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Environment</label>
                    <select name="app_env" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="production" {{ $envConfig['app_env'] === 'production' ? 'selected' : '' }}>Production</option>
                        <option value="staging" {{ $envConfig['app_env'] === 'staging' ? 'selected' : '' }}>Staging</option>
                        <option value="development" {{ $envConfig['app_env'] === 'development' ? 'selected' : '' }}>Development</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Debug Mode</label>
                    <select name="app_debug" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="false" {{ !$envConfig['app_debug'] ? 'selected' : '' }}>Disabled (Recommended for production)</option>
                        <option value="true" {{ $envConfig['app_debug'] ? 'selected' : '' }}>Enabled (Only for development)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Mail Settings -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                Mail Configuration
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mail Driver</label>
                    <select name="mail_driver" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="smtp" {{ $envConfig['mail_driver'] === 'smtp' ? 'selected' : '' }}>SMTP</option>
                        <option value="sendmail" {{ $envConfig['mail_driver'] === 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                        <option value="mailgun" {{ $envConfig['mail_driver'] === 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                        <option value="ses" {{ $envConfig['mail_driver'] === 'ses' ? 'selected' : '' }}>Amazon SES</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mail Host</label>
                    <input type="text" name="mail_host" value="{{ $envConfig['mail_host'] }}" 
                           class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="smtp.gmail.com" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mail Port</label>
                    <input type="number" name="mail_port" value="{{ $envConfig['mail_port'] }}" 
                           class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="587" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mail Encryption</label>
                    <select name="mail_encryption" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="tls" {{ $envConfig['mail_encryption'] === 'tls' ? 'selected' : '' }}>TLS</option>
                        <option value="ssl" {{ $envConfig['mail_encryption'] === 'ssl' ? 'selected' : '' }}>SSL</option>
                        <option value="null" {{ $envConfig['mail_encryption'] === 'null' ? 'selected' : '' }}>None</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mail Username</label>
                    <input type="text" name="mail_username" value="{{ $envConfig['mail_username'] }}" 
                           class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="your-email@gmail.com">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mail Password</label>
                    <input type="password" name="mail_password" value="{{ $envConfig['mail_password'] }}" 
                           class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Your mail password">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">From Email Address</label>
                    <input type="email" name="mail_from_address" value="{{ $envConfig['mail_from_address'] }}" 
                           class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="noreply@yourdomain.com" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">From Name</label>
                    <input type="text" name="mail_from_name" value="{{ $envConfig['mail_from_name'] }}" 
                           class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Mewayz" required>
                </div>
            </div>
        </div>

        <!-- Redis & Cache Settings -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path>
                </svg>
                Cache & Queue Configuration
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Broadcast Driver</label>
                    <select name="broadcast_driver" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="redis" {{ $envConfig['broadcast_driver'] === 'redis' ? 'selected' : '' }}>Redis</option>
                        <option value="pusher" {{ $envConfig['broadcast_driver'] === 'pusher' ? 'selected' : '' }}>Pusher</option>
                        <option value="log" {{ $envConfig['broadcast_driver'] === 'log' ? 'selected' : '' }}>Log</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cache Driver</label>
                    <select name="cache_driver" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="redis" {{ $envConfig['cache_driver'] === 'redis' ? 'selected' : '' }}>Redis</option>
                        <option value="file" {{ $envConfig['cache_driver'] === 'file' ? 'selected' : '' }}>File</option>
                        <option value="memcached" {{ $envConfig['cache_driver'] === 'memcached' ? 'selected' : '' }}>Memcached</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Queue Connection</label>
                    <select name="queue_connection" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="redis" {{ $envConfig['queue_connection'] === 'redis' ? 'selected' : '' }}>Redis</option>
                        <option value="database" {{ $envConfig['queue_connection'] === 'database' ? 'selected' : '' }}>Database</option>
                        <option value="sync" {{ $envConfig['queue_connection'] === 'sync' ? 'selected' : '' }}>Sync</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Session Driver</label>
                    <select name="session_driver" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="redis" {{ $envConfig['session_driver'] === 'redis' ? 'selected' : '' }}>Redis</option>
                        <option value="file" {{ $envConfig['session_driver'] === 'file' ? 'selected' : '' }}>File</option>
                        <option value="database" {{ $envConfig['session_driver'] === 'database' ? 'selected' : '' }}>Database</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Redis Host</label>
                    <input type="text" name="redis_host" value="{{ $envConfig['redis_host'] }}" 
                           class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="127.0.0.1" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Redis Port</label>
                    <input type="number" name="redis_port" value="{{ $envConfig['redis_port'] }}" 
                           class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="6379" required>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Redis Password (Optional)</label>
                    <input type="password" name="redis_password" value="{{ $envConfig['redis_password'] }}" 
                           class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Leave empty if no password">
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-between items-center pt-6">
            <button type="button" onclick="window.installer.nextStep('database')" 
                    class="bg-gray-200 text-gray-700 font-semibold py-2 px-6 rounded-lg hover:bg-gray-300 transition-colors">
                Back
            </button>
            
            <button type="submit" 
                    class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold py-2 px-6 rounded-lg shadow-lg hover:from-indigo-600 hover:to-purple-700 transition-all duration-300">
                Save & Continue
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.getElementById('environmentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        saveEnvironmentConfig();
    });

    function saveEnvironmentConfig() {
        const form = document.getElementById('environmentForm');
        const formData = new FormData(form);
        const submitBtn = form.querySelector('[type="submit"]');
        
        window.installer.showLoading(submitBtn, 'Saving...');
        
        axios.post('/install/process/environment', formData)
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
                let errorMessage = 'Failed to save environment configuration';
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
                window.installer.hideLoading(submitBtn, 'Save & Continue');
            });
    }
</script>
@endpush
@endsection