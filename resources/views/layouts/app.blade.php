<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ $title ?? 'Mewayz - Professional Business Platform' }}</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    
    <!-- PWA Meta Tags -->
    <meta name="application-name" content="Mewayz">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Mewayz">
    <meta name="description" content="Professional business platform for modern creators - manage social media, courses, e-commerce, CRM, and marketing in one place">
    <meta name="format-detection" content="telephone=no">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="msapplication-config" content="/icons/browserconfig.xml">
    <meta name="msapplication-TileColor" content="#3b82f6">
    <meta name="msapplication-tap-highlight" content="no">
    <meta name="theme-color" content="#3b82f6">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">
    
    <!-- PWA Icons -->
    <link rel="apple-touch-icon" href="/images/icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/images/icon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/images/icon-16x16.png">
    <link rel="mask-icon" href="/images/safari-pinned-tab.svg" color="#3b82f6">
    <link rel="shortcut icon" href="/favicon.ico">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/sass/app.scss'])
    @livewireStyles
</head>
<body class="font-sans antialiased">
    {{ $slot }}
    
    <!-- PWA Install Banner -->
    <div id="pwa-install-banner" class="fixed bottom-4 left-4 right-4 bg-blue-600 text-white p-4 rounded-lg shadow-lg z-50 hidden">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                <div>
                    <p class="font-semibold">Install Mewayz</p>
                    <p class="text-sm opacity-90">Get the app experience on your device</p>
                </div>
            </div>
            <div class="flex space-x-2">
                <button id="pwa-install-btn" class="bg-white text-blue-600 px-4 py-2 rounded font-medium hover:bg-gray-100 transition-colors">
                    Install
                </button>
                <button id="pwa-dismiss-btn" class="text-white/80 hover:text-white p-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    @vite(['resources/js/app.js'])
    @livewireScripts
    
    <!-- PWA Scripts -->
    <script>
        // Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js')
                    .then(function(registration) {
                        console.log('Service Worker registered with scope:', registration.scope);
                        
                        // Check for updates
                        registration.addEventListener('updatefound', function() {
                            console.log('New service worker found, installing...');
                            const newWorker = registration.installing;
                            
                            newWorker.addEventListener('statechange', function() {
                                if (newWorker.state === 'installed') {
                                    if (navigator.serviceWorker.controller) {
                                        console.log('New content available, refresh to update');
                                        showUpdateAvailable();
                                    } else {
                                        console.log('Content cached for offline use');
                                    }
                                }
                            });
                        });
                    })
                    .catch(function(error) {
                        console.log('Service Worker registration failed:', error);
                    });
            });
        }
        
        // PWA Installation
        let deferredPrompt;
        const installBanner = document.getElementById('pwa-install-banner');
        const installBtn = document.getElementById('pwa-install-btn');
        const dismissBtn = document.getElementById('pwa-dismiss-btn');
        
        window.addEventListener('beforeinstallprompt', (e) => {
            // Prevent the mini-infobar from appearing on mobile
            e.preventDefault();
            // Stash the event so it can be triggered later
            deferredPrompt = e;
            // Show the install banner
            installBanner.classList.remove('hidden');
        });
        
        installBtn.addEventListener('click', async () => {
            if (deferredPrompt) {
                // Show the install prompt
                deferredPrompt.prompt();
                // Wait for the user to respond to the prompt
                const { outcome } = await deferredPrompt.userChoice;
                console.log(`User response to the install prompt: ${outcome}`);
                // Clear the stored prompt
                deferredPrompt = null;
                // Hide the banner
                installBanner.classList.add('hidden');
            }
        });
        
        dismissBtn.addEventListener('click', () => {
            installBanner.classList.add('hidden');
            // Store dismissal in localStorage
            localStorage.setItem('pwa-install-dismissed', 'true');
        });
        
        // Check if user has dismissed the banner
        if (localStorage.getItem('pwa-install-dismissed') === 'true') {
            installBanner.classList.add('hidden');
        }
        
        // App installed
        window.addEventListener('appinstalled', () => {
            console.log('PWA was installed');
            installBanner.classList.add('hidden');
            showToast('App installed successfully!', 'success');
        });
        
        // Show update available notification
        function showUpdateAvailable() {
            const updateBanner = document.createElement('div');
            updateBanner.className = 'fixed top-4 right-4 bg-green-600 text-white p-4 rounded-lg shadow-lg z-50';
            updateBanner.innerHTML = `
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-semibold">Update Available</p>
                        <p class="text-sm opacity-90">New version of Mewayz is ready</p>
                    </div>
                    <button onclick="window.location.reload()" class="bg-white text-green-600 px-3 py-1 rounded font-medium hover:bg-gray-100 transition-colors ml-4">
                        Refresh
                    </button>
                </div>
            `;
            document.body.appendChild(updateBanner);
            
            setTimeout(() => {
                updateBanner.remove();
            }, 10000);
        }
        
        // Toast notification utility
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
                type === 'success' ? 'bg-green-600 text-white' : 
                type === 'error' ? 'bg-red-600 text-white' : 
                'bg-blue-600 text-white'
            }`;
            toast.textContent = message;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 3000);
        }
        
        // Connection status monitoring
        window.addEventListener('online', () => {
            showToast('Connection restored', 'success');
        });
        
        window.addEventListener('offline', () => {
            showToast('You are offline', 'error');
        });
    </script>
</body>
</html>