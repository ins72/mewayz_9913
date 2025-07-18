@extends('install.layout')

@section('title', 'Database Configuration - Mewayz Installation')

@section('content')
<div class="install-card rounded-2xl shadow-2xl p-8 max-w-3xl mx-auto">
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
            </svg>
        </div>
        <h2 class="text-3xl font-bold text-gray-800 mb-2">Database Configuration</h2>
        <p class="text-gray-600">Configure your database connection settings</p>
    </div>

    <form id="databaseForm" class="space-y-6">
        <!-- Database Type -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Database Type</label>
            <select name="connection" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="mysql" {{ $dbConfig['connection'] === 'mysql' ? 'selected' : '' }}>MySQL</option>
                <option value="mariadb" {{ $dbConfig['connection'] === 'mariadb' ? 'selected' : '' }}>MariaDB</option>
                <option value="pgsql" {{ $dbConfig['connection'] === 'pgsql' ? 'selected' : '' }}>PostgreSQL</option>
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Database Host -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Database Host</label>
                <input type="text" name="host" value="{{ $dbConfig['host'] }}" 
                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                       placeholder="localhost" required>
            </div>

            <!-- Database Port -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Database Port</label>
                <input type="number" name="port" value="{{ $dbConfig['port'] }}" 
                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                       placeholder="3306" required>
            </div>
        </div>

        <!-- Database Name -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Database Name</label>
            <input type="text" name="database" value="{{ $dbConfig['database'] }}" 
                   class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                   placeholder="mewayz" required>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Database Username -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Database Username</label>
                <input type="text" name="username" value="{{ $dbConfig['username'] }}" 
                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                       placeholder="root" required>
            </div>

            <!-- Database Password -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Database Password</label>
                <input type="password" name="password" value="{{ $dbConfig['password'] }}" 
                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                       placeholder="Enter password">
            </div>
        </div>

        <!-- Connection Test Result -->
        <div id="connectionResult" class="hidden"></div>

        <!-- Database Info -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center space-x-2 mb-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h4 class="font-medium text-blue-800">Database Requirements</h4>
            </div>
            <ul class="text-sm text-blue-700 space-y-1">
                <li>• The database should already exist on your server</li>
                <li>• Make sure the user has full permissions (CREATE, ALTER, DROP, INSERT, UPDATE, DELETE)</li>
                <li>• For production, create a dedicated database user with limited permissions</li>
                <li>• UTF8 character set is recommended for proper Unicode support</li>
            </ul>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-between items-center pt-6">
            <button type="button" onclick="window.installer.nextStep('requirements')" 
                    class="bg-gray-200 text-gray-700 font-semibold py-2 px-6 rounded-lg hover:bg-gray-300 transition-colors">
                Back
            </button>
            
            <div class="flex space-x-4">
                <button type="button" onclick="testConnection()" 
                        class="bg-blue-500 text-white font-semibold py-2 px-6 rounded-lg hover:bg-blue-600 transition-colors">
                    Test Connection
                </button>
                
                <button type="submit" 
                        class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold py-2 px-6 rounded-lg shadow-lg hover:from-indigo-600 hover:to-purple-700 transition-all duration-300">
                    Save & Continue
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.getElementById('databaseForm').addEventListener('submit', function(e) {
        e.preventDefault();
        saveDatabaseConfig();
    });

    function testConnection() {
        const form = document.getElementById('databaseForm');
        const formData = new FormData(form);
        const testBtn = document.querySelector('[onclick="testConnection()"]');
        const resultDiv = document.getElementById('connectionResult');
        
        window.installer.showLoading(testBtn, 'Testing...');
        
        axios.post('/install/process/database', formData)
            .then(response => {
                if (response.data.success) {
                    showConnectionResult(true, 'Database connection successful!');
                } else {
                    showConnectionResult(false, response.data.message);
                }
            })
            .catch(error => {
                let errorMessage = 'Connection failed';
                if (error.response && error.response.data) {
                    errorMessage = error.response.data.message || errorMessage;
                }
                showConnectionResult(false, errorMessage);
            })
            .finally(() => {
                window.installer.hideLoading(testBtn, 'Test Connection');
            });
    }

    function saveDatabaseConfig() {
        const form = document.getElementById('databaseForm');
        const formData = new FormData(form);
        const submitBtn = form.querySelector('[type="submit"]');
        
        window.installer.showLoading(submitBtn, 'Saving...');
        
        axios.post('/install/process/database', formData)
            .then(response => {
                if (response.data.success) {
                    showConnectionResult(true, response.data.message);
                    setTimeout(() => {
                        window.installer.nextStep(response.data.nextStep);
                    }, 1000);
                } else {
                    showConnectionResult(false, response.data.message);
                }
            })
            .catch(error => {
                let errorMessage = 'Failed to save database configuration';
                if (error.response && error.response.data) {
                    errorMessage = error.response.data.message || errorMessage;
                    
                    // Show validation errors
                    if (error.response.data.errors) {
                        const errors = Object.values(error.response.data.errors).flat();
                        errorMessage = errors.join(', ');
                    }
                }
                showConnectionResult(false, errorMessage);
            })
            .finally(() => {
                window.installer.hideLoading(submitBtn, 'Save & Continue');
            });
    }

    function showConnectionResult(success, message) {
        const resultDiv = document.getElementById('connectionResult');
        resultDiv.className = `p-4 rounded-lg border mb-4 ${success ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'}`;
        resultDiv.innerHTML = `
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 ${success ? 'text-green-600' : 'text-red-600'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    ${success ? 
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>' : 
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                    }
                </svg>
                <span class="font-medium ${success ? 'text-green-800' : 'text-red-800'}">${message}</span>
            </div>
        `;
        resultDiv.classList.remove('hidden');
    }
</script>
@endpush
@endsection