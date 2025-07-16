@extends('layouts.app')

@section('title', 'Offline - Mewayz')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-gray-800 rounded-2xl shadow-2xl p-8 text-center border border-gray-700">
        <!-- Offline Icon -->
        <div class="mb-6">
            <svg class="w-20 h-20 mx-auto text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path>
            </svg>
        </div>

        <!-- Title -->
        <h1 class="text-3xl font-bold text-white mb-4">
            You're Offline
        </h1>

        <!-- Message -->
        <p class="text-gray-300 mb-6 leading-relaxed">
            No internet connection detected. Don't worry, you can still access cached content and features of Mewayz.
        </p>

        <!-- Retry Button -->
        <button 
            onclick="retryConnection()" 
            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 mb-6 w-full"
        >
            🔄 Try Again
        </button>

        <!-- Connection Status -->
        <div id="connectionStatus" class="p-3 rounded-lg mb-6 bg-red-900 border border-red-700">
            <span class="text-red-300">🔴 Offline</span>
        </div>

        <!-- Available Features -->
        <div class="text-left">
            <h3 class="text-lg font-semibold text-white mb-4">📦 Available Offline</h3>
            <div class="space-y-2">
                <a href="/dashboard" class="block bg-gray-700 hover:bg-gray-600 p-3 rounded-lg transition-colors duration-200">
                    <span class="text-blue-400">📊</span>
                    <span class="text-white ml-2">Dashboard</span>
                </a>
                <a href="/dashboard/instagram" class="block bg-gray-700 hover:bg-gray-600 p-3 rounded-lg transition-colors duration-200">
                    <span class="text-pink-400">📸</span>
                    <span class="text-white ml-2">Instagram Management</span>
                </a>
                <a href="/dashboard/email" class="block bg-gray-700 hover:bg-gray-600 p-3 rounded-lg transition-colors duration-200">
                    <span class="text-green-400">📧</span>
                    <span class="text-white ml-2">Email Marketing</span>
                </a>
                <a href="/dashboard/analytics" class="block bg-gray-700 hover:bg-gray-600 p-3 rounded-lg transition-colors duration-200">
                    <span class="text-yellow-400">📈</span>
                    <span class="text-white ml-2">Analytics</span>
                </a>
            </div>
        </div>

        <!-- PWA Info -->
        <div class="mt-6 pt-6 border-t border-gray-700">
            <p class="text-xs text-gray-400">
                💡 Add Mewayz to your home screen for a native app experience
            </p>
        </div>
    </div>
</div>

<script>
    // Connection status management
    function updateConnectionStatus() {
        const statusElement = document.getElementById('connectionStatus');
        
        if (navigator.onLine) {
            statusElement.className = 'p-3 rounded-lg mb-6 bg-green-900 border border-green-700';
            statusElement.innerHTML = '<span class="text-green-300">🟢 Online</span>';
        } else {
            statusElement.className = 'p-3 rounded-lg mb-6 bg-red-900 border border-red-700';
            statusElement.innerHTML = '<span class="text-red-300">🔴 Offline</span>';
        }
    }

    // Retry connection
    function retryConnection() {
        if (navigator.onLine) {
            window.location.reload();
        } else {
            // Show toast message
            showToast('Still offline. Check your internet connection.', 'error');
        }
    }

    // Show toast notification
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
            type === 'error' ? 'bg-red-600 text-white' : 'bg-blue-600 text-white'
        }`;
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }

    // Listen for connection changes
    window.addEventListener('online', () => {
        updateConnectionStatus();
        showToast('Connection restored! Reloading...', 'success');
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    });

    window.addEventListener('offline', () => {
        updateConnectionStatus();
        showToast('Connection lost. You\'re now offline.', 'error');
    });

    // Update status on load
    updateConnectionStatus();

    // Auto-retry every 30 seconds
    setInterval(() => {
        if (navigator.onLine) {
            console.log('Connection restored, reloading...');
            window.location.reload();
        }
    }, 30000);

    // Service Worker registration
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js')
            .then(registration => {
                console.log('Service Worker registered:', registration);
            })
            .catch(error => {
                console.log('Service Worker registration failed:', error);
            });
    }
</script>
@endsection