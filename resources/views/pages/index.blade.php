<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Mewayz - Professional Business Platform for Modern Creators</title>
    <meta name="description" content="Transform your creative business with Mewayz - the all-in-one platform for sites, courses, audience management, and monetization.">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/sass/app.scss'])
    @livewireStyles
</head>
<body class="font-sans antialiased">
    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-app-bg/80 backdrop-blur-sm border-b border-border-color">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <div class="text-2xl font-bold text-primary-text">
                        <span class="text-gradient">Mewayz</span>
                    </div>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-secondary-text hover:text-primary-text transition-colors">Features</a>
                    <a href="#pricing" class="text-secondary-text hover:text-primary-text transition-colors">Pricing</a>
                    <a href="#about" class="text-secondary-text hover:text-primary-text transition-colors">About</a>
                    <a href="#contact" class="text-secondary-text hover:text-primary-text transition-colors">Contact</a>
                </div>

                <!-- Auth Links -->
                <div class="hidden md:flex items-center space-x-4">
                    @guest
                        <a href="{{ route('login') }}" class="text-secondary-text hover:text-primary-text transition-colors">Sign In</a>
                        <a href="{{ route('register') }}" class="btn btn-primary">Get Started</a>
                    @else
                        <a href="{{ route('dashboard-index') }}" class="btn btn-primary">Dashboard</a>
                    @endguest
                </div>

                <!-- Mobile Menu Button -->
                <button class="md:hidden text-primary-text" id="mobile-menu-toggle">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div class="md:hidden hidden" id="mobile-menu">
            <div class="px-4 pt-2 pb-3 space-y-1 bg-card-bg border-t border-border-color">
                <a href="#features" class="block px-3 py-2 text-secondary-text hover:text-primary-text transition-colors">Features</a>
                <a href="#pricing" class="block px-3 py-2 text-secondary-text hover:text-primary-text transition-colors">Pricing</a>
                <a href="#about" class="block px-3 py-2 text-secondary-text hover:text-primary-text transition-colors">About</a>
                <a href="#contact" class="block px-3 py-2 text-secondary-text hover:text-primary-text transition-colors">Contact</a>
                @guest
                    <a href="{{ route('login') }}" class="block px-3 py-2 text-secondary-text hover:text-primary-text transition-colors">Sign In</a>
                    <a href="{{ route('register') }}" class="block px-3 py-2 text-info hover:text-blue-400 transition-colors">Get Started</a>
                @else
                    <a href="{{ route('dashboard-index') }}" class="block px-3 py-2 text-info hover:text-blue-400 transition-colors">Dashboard</a>
                @endguest
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-24 pb-16 bg-gradient-to-b from-app-bg via-app-bg to-card-bg">
        <div class="container mx-auto px-4">
            <div class="text-center max-w-4xl mx-auto">
                <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold text-primary-text mb-6">
                    Build Your <span class="text-gradient">Creative Empire</span>
                </h1>
                <p class="text-xl md:text-2xl text-secondary-text mb-8 leading-relaxed">
                    The all-in-one platform for modern creators. Build stunning sites, sell courses, manage your audience, and monetize your passion.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('register') }}" class="btn btn-primary text-lg px-8 py-4">
                        Start Free Trial
                    </a>
                    <a href="#features" class="btn btn-secondary text-lg px-8 py-4">
                        See Features
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-app-bg">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-primary-text mb-4">Everything You Need</h2>
                <p class="text-xl text-secondary-text max-w-2xl mx-auto">
                    Powerful tools designed for creators, entrepreneurs, and businesses ready to scale.
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="card text-center">
                    <div class="w-16 h-16 bg-info/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-primary-text mb-2">AI-Powered Sites</h3>
                    <p class="text-secondary-text">Create stunning websites with AI assistance. No coding required.</p>
                </div>

                <!-- Feature 2 -->
                <div class="card text-center">
                    <div class="w-16 h-16 bg-success/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-primary-text mb-2">Course Creation</h3>
                    <p class="text-secondary-text">Build and sell online courses with built-in payment processing.</p>
                </div>

                <!-- Feature 3 -->
                <div class="card text-center">
                    <div class="w-16 h-16 bg-warning/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-primary-text mb-2">Audience Management</h3>
                    <p class="text-secondary-text">Track, engage, and grow your audience with powerful analytics.</p>
                </div>

                <!-- Feature 4 -->
                <div class="card text-center">
                    <div class="w-16 h-16 bg-error/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-error" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-primary-text mb-2">E-commerce Store</h3>
                    <p class="text-secondary-text">Sell digital and physical products with integrated payment solutions.</p>
                </div>

                <!-- Feature 5 -->
                <div class="card text-center">
                    <div class="w-16 h-16 bg-info/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-primary-text mb-2">Wallet & Payments</h3>
                    <p class="text-secondary-text">Manage earnings, track payments, and handle invoicing seamlessly.</p>
                </div>

                <!-- Feature 6 -->
                <div class="card text-center">
                    <div class="w-16 h-16 bg-success/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-primary-text mb-2">Analytics & Insights</h3>
                    <p class="text-secondary-text">Make data-driven decisions with comprehensive analytics.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-info/10 via-transparent to-success/10">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold text-primary-text mb-4">Ready to Start Building?</h2>
            <p class="text-xl text-secondary-text mb-8 max-w-2xl mx-auto">
                Join thousands of creators who have transformed their passion into profitable businesses with Mewayz.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" class="btn btn-primary text-lg px-8 py-4">
                    Start Your Free Trial
                </a>
                <a href="#contact" class="btn btn-secondary text-lg px-8 py-4">
                    Contact Sales
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-card-bg py-12 border-t border-border-color">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div class="col-span-1">
                    <div class="text-2xl font-bold text-primary-text mb-4">
                        <span class="text-gradient">Mewayz</span>
                    </div>
                    <p class="text-secondary-text mb-4">
                        Empowering creators and entrepreneurs to build successful online businesses.
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="font-semibold text-primary-text mb-4">Product</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-secondary-text hover:text-primary-text transition-colors">Features</a></li>
                        <li><a href="#" class="text-secondary-text hover:text-primary-text transition-colors">Pricing</a></li>
                        <li><a href="#" class="text-secondary-text hover:text-primary-text transition-colors">Templates</a></li>
                        <li><a href="#" class="text-secondary-text hover:text-primary-text transition-colors">Integrations</a></li>
                    </ul>
                </div>

                <!-- Resources -->
                <div>
                    <h4 class="font-semibold text-primary-text mb-4">Resources</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-secondary-text hover:text-primary-text transition-colors">Documentation</a></li>
                        <li><a href="#" class="text-secondary-text hover:text-primary-text transition-colors">Blog</a></li>
                        <li><a href="#" class="text-secondary-text hover:text-primary-text transition-colors">Help Center</a></li>
                        <li><a href="#" class="text-secondary-text hover:text-primary-text transition-colors">Community</a></li>
                    </ul>
                </div>

                <!-- Company -->
                <div>
                    <h4 class="font-semibold text-primary-text mb-4">Company</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-secondary-text hover:text-primary-text transition-colors">About</a></li>
                        <li><a href="#" class="text-secondary-text hover:text-primary-text transition-colors">Careers</a></li>
                        <li><a href="#" class="text-secondary-text hover:text-primary-text transition-colors">Privacy</a></li>
                        <li><a href="#" class="text-secondary-text hover:text-primary-text transition-colors">Terms</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-border-color mt-8 pt-8 text-center">
                <p class="text-secondary-text">
                    © {{ date('Y') }} Mewayz. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    @vite(['resources/js/app.js'])
    @livewireScripts

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-toggle').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>