<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: false }" :data-theme="darkMode ? 'dark' : 'light'">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Mewayz') }} - @yield('title', 'All-in-One Business Platform')</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="@yield('meta_description', 'Professional all-in-one business platform for creators, entrepreneurs, and businesses. Manage social media, create courses, build e-commerce stores, and grow your digital empire.')">
    <meta name="keywords" content="@yield('meta_keywords', 'business platform, social media management, course creation, e-commerce, CRM, digital marketing, creator economy')">
    <meta name="author" content="Mewayz Platform">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="@yield('og_title', config('app.name') . ' - All-in-One Business Platform')">
    <meta property="og:description" content="@yield('og_description', 'Professional all-in-one business platform for creators, entrepreneurs, and businesses.')">
    <meta property="og:image" content="@yield('og_image', asset('images/og-image.jpg'))">
    <meta property="og:url" content="{{ request()->url() }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('twitter_title', config('app.name') . ' - All-in-One Business Platform')">
    <meta name="twitter:description" content="@yield('twitter_description', 'Professional all-in-one business platform for creators, entrepreneurs, and businesses.')">
    <meta name="twitter:image" content="@yield('twitter_image', asset('images/twitter-card.jpg'))">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#1A1A1A">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">
    <meta name="msapplication-TileColor" content="#1A1A1A">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Additional CSS -->
    @stack('styles')
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Heroicons -->
    <script src="https://cdn.jsdelivr.net/npm/heroicons@2.0.18/24/outline/index.js"></script>
    
    <!-- Analytics -->
    @if(config('app.env') === 'production')
    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'GA_MEASUREMENT_ID');
    </script>
    @endif
</head>
<body class="font-sans antialiased" x-init="
    darkMode = localStorage.getItem('darkMode') === 'true' || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches);
    $watch('darkMode', value => localStorage.setItem('darkMode', value));
">
    <!-- Loading Spinner -->
    <div id="loading-spinner" class="fixed inset-0 bg-white/80 backdrop-blur-sm flex items-center justify-center z-50" style="display: none;">
        <div class="loading-spinner w-8 h-8"></div>
    </div>
    
    <!-- Skip Link for Accessibility -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 bg-blue-600 text-white px-4 py-2 rounded-md z-50">
        Skip to main content
    </a>
    
    <!-- Navigation -->
    @include('layouts.navigation')
    
    <!-- Main Content -->
    <main id="main-content" class="min-h-screen">
        <!-- Flash Messages -->
        @if (session('success'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition class="notification-success">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button @click="show = false" class="ml-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        @endif
        
        @if (session('error'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition class="notification-error">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button @click="show = false" class="ml-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        @endif
        
        @if (session('warning'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition class="notification-warning">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <span>{{ session('warning') }}</span>
                </div>
                <button @click="show = false" class="ml-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        @endif
        
        <!-- Page Content -->
        @yield('content')
    </main>
    
    <!-- Footer -->
    @include('layouts.footer')
    
    <!-- Notification Container -->
    <div id="notification-container" class="fixed top-4 right-4 z-50 space-y-2"></div>
    
    <!-- Modal Container -->
    <div id="modal-container"></div>
    
    <!-- JavaScript -->
    <script>
        // Global JavaScript utilities
        window.Mewayz = {
            // Show notification
            notify: function(message, type = 'info', duration = 5000) {
                const container = document.getElementById('notification-container');
                const notification = document.createElement('div');
                notification.className = `notification notification-${type} show`;
                notification.innerHTML = `
                    <div class="flex items-center">
                        <span>${message}</span>
                        <button onclick="this.parentElement.parentElement.classList.add('hide')" class="ml-auto">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                `;
                
                container.appendChild(notification);
                
                setTimeout(() => {
                    notification.classList.add('hide');
                    setTimeout(() => {
                        if (notification.parentElement) {
                            notification.parentElement.removeChild(notification);
                        }
                    }, 300);
                }, duration);
            },
            
            // Show loading spinner
            showLoading: function() {
                document.getElementById('loading-spinner').style.display = 'flex';
            },
            
            // Hide loading spinner
            hideLoading: function() {
                document.getElementById('loading-spinner').style.display = 'none';
            },
            
            // API request helper
            api: async function(url, options = {}) {
                this.showLoading();
                
                try {
                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const response = await fetch(url, {
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json',
                            ...options.headers
                        },
                        ...options
                    });
                    
                    const data = await response.json();
                    
                    if (!response.ok) {
                        throw new Error(data.message || 'Request failed');
                    }
                    
                    return data;
                } catch (error) {
                    this.notify(error.message, 'error');
                    throw error;
                } finally {
                    this.hideLoading();
                }
            },
            
            // Confirm dialog
            confirm: function(message, callback) {
                if (confirm(message)) {
                    callback();
                }
            },
            
            // Copy to clipboard
            copyToClipboard: function(text) {
                navigator.clipboard.writeText(text).then(() => {
                    this.notify('Copied to clipboard!', 'success');
                }).catch(() => {
                    this.notify('Failed to copy to clipboard', 'error');
                });
            },
            
            // Format number
            formatNumber: function(num) {
                return new Intl.NumberFormat().format(num);
            },
            
            // Format currency
            formatCurrency: function(amount, currency = 'USD') {
                return new Intl.NumberFormat('en-US', {
                    style: 'currency',
                    currency: currency
                }).format(amount);
            },
            
            // Format date
            formatDate: function(date) {
                return new Intl.DateTimeFormat('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                }).format(new Date(date));
            },
            
            // Format relative time
            formatRelativeTime: function(date) {
                const rtf = new Intl.RelativeTimeFormat('en', { numeric: 'auto' });
                const diff = new Date(date) - new Date();
                const diffDays = Math.floor(diff / (1000 * 60 * 60 * 24));
                
                if (Math.abs(diffDays) < 1) {
                    const diffHours = Math.floor(diff / (1000 * 60 * 60));
                    if (Math.abs(diffHours) < 1) {
                        const diffMinutes = Math.floor(diff / (1000 * 60));
                        return rtf.format(diffMinutes, 'minute');
                    }
                    return rtf.format(diffHours, 'hour');
                }
                
                return rtf.format(diffDays, 'day');
            }
        };
        
        // Initialize app
        document.addEventListener('DOMContentLoaded', function() {
            // Hide loading spinner on page load
            Mewayz.hideLoading();
            
            // Initialize tooltips
            const tooltipTriggers = document.querySelectorAll('[data-tooltip]');
            tooltipTriggers.forEach(trigger => {
                trigger.addEventListener('mouseenter', function() {
                    const tooltip = document.createElement('div');
                    tooltip.className = 'tooltip show';
                    tooltip.textContent = this.getAttribute('data-tooltip');
                    tooltip.style.left = '50%';
                    tooltip.style.transform = 'translateX(-50%)';
                    tooltip.style.bottom = '100%';
                    tooltip.style.marginBottom = '5px';
                    this.style.position = 'relative';
                    this.appendChild(tooltip);
                });
                
                trigger.addEventListener('mouseleave', function() {
                    const tooltip = this.querySelector('.tooltip');
                    if (tooltip) {
                        tooltip.remove();
                    }
                });
            });
            
            // Initialize dropdowns
            const dropdowns = document.querySelectorAll('.dropdown');
            dropdowns.forEach(dropdown => {
                const trigger = dropdown.querySelector('[data-dropdown-toggle]');
                const menu = dropdown.querySelector('.dropdown-menu');
                
                if (trigger && menu) {
                    trigger.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        menu.classList.toggle('show');
                    });
                    
                    // Close on outside click
                    document.addEventListener('click', function(e) {
                        if (!dropdown.contains(e.target)) {
                            menu.classList.remove('show');
                        }
                    });
                }
            });
            
            // Initialize tabs
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Remove active class from all tabs
                    tabs.forEach(t => t.classList.remove('active'));
                    
                    // Add active class to clicked tab
                    this.classList.add('active');
                    
                    // Show corresponding tab content
                    const targetId = this.getAttribute('data-tab');
                    const allTabContents = document.querySelectorAll('.tab-content');
                    allTabContents.forEach(content => {
                        content.style.display = 'none';
                    });
                    
                    const targetContent = document.getElementById(targetId);
                    if (targetContent) {
                        targetContent.style.display = 'block';
                    }
                });
            });
            
            // Initialize form validation
            const forms = document.querySelectorAll('form[data-validate]');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const requiredFields = form.querySelectorAll('[required]');
                    let isValid = true;
                    
                    requiredFields.forEach(field => {
                        if (!field.value.trim()) {
                            field.classList.add('form-error');
                            isValid = false;
                        } else {
                            field.classList.remove('form-error');
                        }
                    });
                    
                    if (!isValid) {
                        e.preventDefault();
                        Mewayz.notify('Please fill in all required fields', 'error');
                    }
                });
            });
            
            // Auto-hide alerts
            const alerts = document.querySelectorAll('.alert[data-auto-hide]');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    setTimeout(() => {
                        alert.remove();
                    }, 300);
                }, 5000);
            });
            
            // Initialize lazy loading for images
            const lazyImages = document.querySelectorAll('img[data-src]');
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });
            
            lazyImages.forEach(img => {
                imageObserver.observe(img);
            });
        });
        
        // PWA install prompt
        let deferredPrompt;
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            
            // Show install button
            const installButton = document.getElementById('pwa-install-button');
            if (installButton) {
                installButton.style.display = 'block';
                installButton.addEventListener('click', () => {
                    deferredPrompt.prompt();
                    deferredPrompt.userChoice.then((choiceResult) => {
                        if (choiceResult.outcome === 'accepted') {
                            Mewayz.notify('App installed successfully!', 'success');
                        }
                        deferredPrompt = null;
                    });
                });
            }
        });
        
        // Service Worker registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js')
                    .then(function(registration) {
                        console.log('ServiceWorker registration successful');
                    })
                    .catch(function(error) {
                        console.log('ServiceWorker registration failed');
                    });
            });
        }
    </script>
    
    <!-- Additional JavaScript -->
    @stack('scripts')
</body>
</html>