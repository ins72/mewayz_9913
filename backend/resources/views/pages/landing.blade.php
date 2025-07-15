<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mewayz - All-in-One Business Platform | Access All Instances</title>
    <meta name="description" content="Access all Mewayz platform instances - Laravel Web App, Flutter Mobile App, Admin Dashboard, and Development Tools">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'mewayz-bg': '#101010',
                        'mewayz-surface': '#191919',
                        'mewayz-border': '#2A2A2A',
                        'mewayz-primary': '#4ECDC4',
                        'mewayz-secondary': '#45B7D1',
                        'mewayz-accent': '#26DE81',
                        'mewayz-warning': '#F9CA24',
                        'mewayz-danger': '#FF4757',
                        'mewayz-text': '#FFFFFF',
                        'mewayz-text-secondary': '#A0A0A0',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .gradient-bg {
            background: linear-gradient(135deg, #4ECDC4 0%, #45B7D1 100%);
        }
        .instance-card {
            transition: all 0.3s ease;
            border: 1px solid #2A2A2A;
        }
        .instance-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(78, 205, 196, 0.1);
            border-color: #4ECDC4;
        }
        .status-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        .status-online {
            background-color: #26DE81;
        }
        .status-offline {
            background-color: #FF4757;
        }
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        .floating-card {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .tech-badge {
            background: linear-gradient(135deg, #4ECDC4 0%, #45B7D1 100%);
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }
    </style>
</head>
<body class="bg-mewayz-bg text-mewayz-text">
    <!-- Navigation -->
    <nav class="bg-mewayz-surface border-b border-mewayz-border">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <div class="w-8 h-8 bg-mewayz-primary rounded-lg flex items-center justify-center mr-3">
                            <span class="text-mewayz-bg font-bold text-lg">M</span>
                        </div>
                        <h1 class="text-xl font-bold text-mewayz-text">Mewayz</h1>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-mewayz-text-secondary">By Mewayz Technologies Inc.</span>
                    <div class="flex items-center space-x-2">
                        <div class="status-indicator status-online"></div>
                        <span class="text-sm text-mewayz-accent">All Systems Online</span>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="py-20 bg-mewayz-bg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="floating-card">
                <h1 class="text-4xl md:text-6xl font-bold mb-6">
                    <span class="gradient-bg bg-clip-text text-transparent">Mewayz Platform</span>
                </h1>
                <p class="text-xl md:text-2xl text-mewayz-text-secondary mb-8 max-w-3xl mx-auto">
                    Your All-in-One Business Platform - Choose Your Instance
                </p>
                <div class="flex flex-wrap justify-center gap-4 mb-12">
                    <span class="tech-badge">Laravel + Flutter</span>
                    <span class="tech-badge">PWA Ready</span>
                    <span class="tech-badge">Multi-Platform</span>
                    <span class="tech-badge">Production Ready</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Platform Instances Grid -->
    <section class="py-16 bg-mewayz-bg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold mb-4">Platform Instances</h2>
                <p class="text-mewayz-text-secondary">Access all available instances of the Mewayz platform</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Flutter Mobile App -->
                <div class="instance-card bg-mewayz-surface rounded-lg p-6 hover:bg-opacity-80">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-mewayz-primary rounded-lg flex items-center justify-center mr-4">
                                <span class="text-mewayz-bg font-bold text-xl">📱</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold">Flutter Mobile App</h3>
                                <p class="text-sm text-mewayz-text-secondary">Cross-platform mobile experience</p>
                            </div>
                        </div>
                        <div class="status-indicator status-online"></div>
                    </div>
                    <div class="mb-4">
                        <p class="text-sm text-mewayz-text-secondary mb-3">Full-featured mobile application with native performance</p>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="text-xs bg-mewayz-border text-mewayz-text px-2 py-1 rounded">Flutter 3.x</span>
                            <span class="text-xs bg-mewayz-border text-mewayz-text px-2 py-1 rounded">Dart</span>
                            <span class="text-xs bg-mewayz-border text-mewayz-text px-2 py-1 rounded">PWA</span>
                        </div>
                        <div class="space-y-2">
                            <a href="/flutter.html" class="block w-full bg-mewayz-primary text-mewayz-bg text-center py-2 rounded-md hover:bg-opacity-90 transition-colors">
                                Launch Flutter App
                            </a>
                            <a href="/app.html" class="block w-full bg-mewayz-border text-mewayz-text text-center py-2 rounded-md hover:bg-opacity-80 transition-colors">
                                Alternative Access
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Laravel Web Application -->
                <div class="instance-card bg-mewayz-surface rounded-lg p-6 hover:bg-opacity-80">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-mewayz-secondary rounded-lg flex items-center justify-center mr-4">
                                <span class="text-white font-bold text-xl">🌐</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold">Laravel Web App</h3>
                                <p class="text-sm text-mewayz-text-secondary">Full-featured web application</p>
                            </div>
                        </div>
                        <div class="status-indicator status-online"></div>
                    </div>
                    <div class="mb-4">
                        <p class="text-sm text-mewayz-text-secondary mb-3">Complete web-based business management platform</p>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="text-xs bg-mewayz-border text-mewayz-text px-2 py-1 rounded">Laravel 10+</span>
                            <span class="text-xs bg-mewayz-border text-mewayz-text px-2 py-1 rounded">PHP 8.2</span>
                            <span class="text-xs bg-mewayz-border text-mewayz-text px-2 py-1 rounded">MySQL</span>
                        </div>
                        <div class="space-y-2">
                            <a href="/index.html" class="block w-full bg-mewayz-secondary text-white text-center py-2 rounded-md hover:bg-opacity-90 transition-colors">
                                Launch Web App
                            </a>
                            <a href="/dashboard.html" class="block w-full bg-mewayz-border text-mewayz-text text-center py-2 rounded-md hover:bg-opacity-80 transition-colors">
                                Direct Dashboard
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Authentication Pages -->
                <div class="instance-card bg-mewayz-surface rounded-lg p-6 hover:bg-opacity-80">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-mewayz-accent rounded-lg flex items-center justify-center mr-4">
                                <span class="text-white font-bold text-xl">🔐</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold">Authentication</h3>
                                <p class="text-sm text-mewayz-text-secondary">Login & Registration</p>
                            </div>
                        </div>
                        <div class="status-indicator status-online"></div>
                    </div>
                    <div class="mb-4">
                        <p class="text-sm text-mewayz-text-secondary mb-3">Secure authentication with 2FA and OAuth support</p>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="text-xs bg-mewayz-border text-mewayz-text px-2 py-1 rounded">OAuth 2.0</span>
                            <span class="text-xs bg-mewayz-border text-mewayz-text px-2 py-1 rounded">2FA</span>
                            <span class="text-xs bg-mewayz-border text-mewayz-text px-2 py-1 rounded">Sanctum</span>
                        </div>
                        <div class="space-y-2">
                            <a href="/login.html" class="block w-full bg-mewayz-accent text-white text-center py-2 rounded-md hover:bg-opacity-90 transition-colors">
                                Login Page
                            </a>
                            <a href="/register.html" class="block w-full bg-mewayz-border text-mewayz-text text-center py-2 rounded-md hover:bg-opacity-80 transition-colors">
                                Register Page
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Feature-Specific Pages -->
                <div class="instance-card bg-mewayz-surface rounded-lg p-6 hover:bg-opacity-80">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-mewayz-warning rounded-lg flex items-center justify-center mr-4">
                                <span class="text-white font-bold text-xl">📊</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold">Feature Pages</h3>
                                <p class="text-sm text-mewayz-text-secondary">Specialized interfaces</p>
                            </div>
                        </div>
                        <div class="status-indicator status-online"></div>
                    </div>
                    <div class="mb-4">
                        <p class="text-sm text-mewayz-text-secondary mb-3">Direct access to specific platform features</p>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="text-xs bg-mewayz-border text-mewayz-text px-2 py-1 rounded">Analytics</span>
                            <span class="text-xs bg-mewayz-border text-mewayz-text px-2 py-1 rounded">Social Media</span>
                            <span class="text-xs bg-mewayz-border text-mewayz-text px-2 py-1 rounded">Bio Sites</span>
                        </div>
                        <div class="space-y-2">
                            <a href="/analytics.html" class="block w-full bg-mewayz-warning text-white text-center py-2 rounded-md hover:bg-opacity-90 transition-colors">
                                Analytics Dashboard
                            </a>
                            <a href="/social-media.html" class="block w-full bg-mewayz-border text-mewayz-text text-center py-2 rounded-md hover:bg-opacity-80 transition-colors">
                                Social Media
                            </a>
                            <a href="/bio-sites.html" class="block w-full bg-mewayz-border text-mewayz-text text-center py-2 rounded-md hover:bg-opacity-80 transition-colors">
                                Bio Sites
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Development Tools -->
                <div class="instance-card bg-mewayz-surface rounded-lg p-6 hover:bg-opacity-80">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-mewayz-danger rounded-lg flex items-center justify-center mr-4">
                                <span class="text-white font-bold text-xl">🔧</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold">Development Tools</h3>
                                <p class="text-sm text-mewayz-text-secondary">Testing & demos</p>
                            </div>
                        </div>
                        <div class="status-indicator status-online"></div>
                    </div>
                    <div class="mb-4">
                        <p class="text-sm text-mewayz-text-secondary mb-3">Development utilities and theme demonstrations</p>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="text-xs bg-mewayz-border text-mewayz-text px-2 py-1 rounded">Dark Theme</span>
                            <span class="text-xs bg-mewayz-border text-mewayz-text px-2 py-1 rounded">Mobile UI</span>
                            <span class="text-xs bg-mewayz-border text-mewayz-text px-2 py-1 rounded">PWA</span>
                        </div>
                        <div class="space-y-2">
                            <a href="/dark-theme-demo.html" class="block w-full bg-mewayz-danger text-white text-center py-2 rounded-md hover:bg-opacity-90 transition-colors">
                                Dark Theme Demo
                            </a>
                            <a href="/mobile.html" class="block w-full bg-mewayz-border text-mewayz-text text-center py-2 rounded-md hover:bg-opacity-80 transition-colors">
                                Mobile Interface
                            </a>
                        </div>
                    </div>
                </div>

                <!-- API & Backend -->
                <div class="instance-card bg-mewayz-surface rounded-lg p-6 hover:bg-opacity-80">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gradient-to-r from-mewayz-primary to-mewayz-secondary rounded-lg flex items-center justify-center mr-4">
                                <span class="text-white font-bold text-xl">⚡</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold">API & Backend</h3>
                                <p class="text-sm text-mewayz-text-secondary">System status & health</p>
                            </div>
                        </div>
                        <div class="status-indicator status-online"></div>
                    </div>
                    <div class="mb-4">
                        <p class="text-sm text-mewayz-text-secondary mb-3">Backend API health and system monitoring</p>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="text-xs bg-mewayz-border text-mewayz-text px-2 py-1 rounded">FastAPI</span>
                            <span class="text-xs bg-mewayz-border text-mewayz-text px-2 py-1 rounded">Port 8001</span>
                            <span class="text-xs bg-mewayz-border text-mewayz-text px-2 py-1 rounded">Production</span>
                        </div>
                        <div class="space-y-2">
                            <a href="/api/health" class="block w-full bg-gradient-to-r from-mewayz-primary to-mewayz-secondary text-white text-center py-2 rounded-md hover:opacity-90 transition-opacity">
                                API Health Check
                            </a>
                            <a href="/api" class="block w-full bg-mewayz-border text-mewayz-text text-center py-2 rounded-md hover:bg-opacity-80 transition-colors">
                                API Documentation
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- System Status -->
    <section class="py-12 bg-mewayz-surface">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold mb-4">System Status</h2>
                <p class="text-mewayz-text-secondary">Real-time platform health monitoring</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-mewayz-bg rounded-lg p-6 text-center">
                    <div class="status-indicator status-online mx-auto mb-3"></div>
                    <h3 class="text-lg font-semibold mb-2">Backend API</h3>
                    <p class="text-mewayz-text-secondary text-sm">Port 8001 - Operational</p>
                </div>

                <div class="bg-mewayz-bg rounded-lg p-6 text-center">
                    <div class="status-indicator status-online mx-auto mb-3"></div>
                    <h3 class="text-lg font-semibold mb-2">Frontend Services</h3>
                    <p class="text-mewayz-text-secondary text-sm">Port 3000 - Operational</p>
                </div>

                <div class="bg-mewayz-bg rounded-lg p-6 text-center">
                    <div class="status-indicator status-online mx-auto mb-3"></div>
                    <h3 class="text-lg font-semibold mb-2">Database</h3>
                    <p class="text-mewayz-text-secondary text-sm">MySQL - Connected</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Actions -->
    <section class="py-12 bg-mewayz-bg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold mb-4">Quick Actions</h2>
                <p class="text-mewayz-text-secondary">Jump directly to common tasks</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="/flutter.html" class="bg-mewayz-surface rounded-lg p-4 text-center hover:bg-opacity-80 transition-colors">
                    <div class="text-2xl mb-2">🚀</div>
                    <p class="text-sm font-medium">Launch App</p>
                </a>

                <a href="/login.html" class="bg-mewayz-surface rounded-lg p-4 text-center hover:bg-opacity-80 transition-colors">
                    <div class="text-2xl mb-2">🔐</div>
                    <p class="text-sm font-medium">Login</p>
                </a>

                <a href="/dashboard.html" class="bg-mewayz-surface rounded-lg p-4 text-center hover:bg-opacity-80 transition-colors">
                    <div class="text-2xl mb-2">📊</div>
                    <p class="text-sm font-medium">Dashboard</p>
                </a>

                <a href="/api/health" class="bg-mewayz-surface rounded-lg p-4 text-center hover:bg-opacity-80 transition-colors">
                    <div class="text-2xl mb-2">⚡</div>
                    <p class="text-sm font-medium">API Status</p>
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-mewayz-surface border-t border-mewayz-border py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center mb-4 md:mb-0">
                    <div class="w-8 h-8 bg-mewayz-primary rounded-lg flex items-center justify-center mr-3">
                        <span class="text-mewayz-bg font-bold text-lg">M</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold">Mewayz</h3>
                        <p class="text-sm text-mewayz-text-secondary">By Mewayz Technologies Inc.</p>
                    </div>
                </div>

                <div class="flex items-center space-x-6">
                    <a href="https://mewayz.com" class="text-mewayz-text-secondary hover:text-mewayz-primary transition-colors">
                        Production Site
                    </a>
                    <a href="/api" class="text-mewayz-text-secondary hover:text-mewayz-primary transition-colors">
                        API Docs
                    </a>
                    <div class="flex items-center space-x-2">
                        <div class="status-indicator status-online"></div>
                        <span class="text-sm text-mewayz-accent">Online</span>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-6 border-t border-mewayz-border text-center">
                <p class="text-mewayz-text-secondary text-sm">
                    © 2024 Mewayz Technologies Inc. All rights reserved. | 
                    <span class="text-mewayz-primary">Creating seamless business solutions for the modern digital world</span>
                </p>
            </div>
        </div>
    </footer>

    <script>
        // Auto-refresh page every 30 seconds to check system status
        setTimeout(() => {
            // Check API health
            fetch('/api/health')
                .then(response => response.json())
                .then(data => {
                    console.log('API Health:', data);
                })
                .catch(error => {
                    console.error('API Health Check Failed:', error);
                });
        }, 5000);

        // Add click tracking for analytics
        document.addEventListener('click', function(e) {
            if (e.target.tagName === 'A' && e.target.href) {
                console.log('Navigation to:', e.target.href);
            }
        });
    </script>
</body>
</html>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .demo-preview {
            border: 2px solid #2A2A2A;
            border-radius: 12px;
            overflow: hidden;
            background: #191919;
        }
        .demo-preview img {
            width: 100%;
            height: auto;
            object-fit: cover;
        }
    </style>
</head>
<body class="bg-mewayz-bg text-mewayz-text">
    <!-- Header -->
    <header class="bg-mewayz-surface border-b border-mewayz-border">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-mewayz-primary rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-lg">M</span>
                    </div>
                    <h1 class="text-2xl font-bold">Mewayz</h1>
                </div>
                <nav class="hidden md:flex space-x-8">
                    <a href="#features" class="text-mewayz-text-secondary hover:text-mewayz-primary transition-colors">Features</a>
                    <a href="#demo" class="text-mewayz-text-secondary hover:text-mewayz-primary transition-colors">Demo</a>
                    <a href="#pricing" class="text-mewayz-text-secondary hover:text-mewayz-primary transition-colors">Pricing</a>
                    <a href="#contact" class="text-mewayz-text-secondary hover:text-mewayz-primary transition-colors">Contact</a>
                </nav>
                <div class="space-x-4">
                    <a href="/login" class="px-4 py-2 text-mewayz-text-secondary hover:text-mewayz-primary transition-colors">Login</a>
                    <a href="/register" class="px-6 py-2 bg-mewayz-primary text-white rounded-lg hover:bg-opacity-90 transition-colors">Get Started</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="py-20 bg-mewayz-bg">
        <div class="container mx-auto px-6">
            <div class="flex flex-col lg:flex-row items-center">
                <div class="lg:w-1/2 lg:pr-12">
                    <h1 class="text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                        All-in-One Business Platform for
                        <span class="gradient-bg bg-clip-text text-transparent">Modern Creators</span>
                    </h1>
                    <p class="text-xl text-mewayz-text-secondary mb-8 leading-relaxed">
                        Manage your social media, create bio sites, track leads, run email campaigns, 
                        sell products, and create courses - all from one powerful platform.
                    </p>
                    <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 mb-8">
                        <a href="/register" class="px-8 py-4 bg-mewayz-primary text-white rounded-lg font-semibold hover:bg-opacity-90 transition-colors text-center">
                            Start Free Trial
                        </a>
                        <a href="#demo" class="px-8 py-4 border border-mewayz-border text-mewayz-text hover:border-mewayz-primary transition-colors rounded-lg font-semibold text-center">
                            Explore Features
                        </a>
                    </div>
                    <div class="flex flex-wrap gap-8">
                        <div class="text-center">
                            <div class="text-2xl font-bold stats-counter">15+</div>
                            <div class="text-mewayz-text-secondary">Integrated Tools</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold stats-counter">99.9%</div>
                            <div class="text-mewayz-text-secondary">Uptime</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold stats-counter">24/7</div>
                            <div class="text-mewayz-text-secondary">Support</div>
                        </div>
                    </div>
                </div>
                <div class="lg:w-1/2 mt-12 lg:mt-0">
                    <div class="floating-card">
                        <div class="demo-preview">
                            <div class="bg-mewayz-surface p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold">Dashboard Preview</h3>
                                    <div class="flex space-x-2">
                                        <div class="w-3 h-3 bg-mewayz-danger rounded-full"></div>
                                        <div class="w-3 h-3 bg-mewayz-warning rounded-full"></div>
                                        <div class="w-3 h-3 bg-mewayz-accent rounded-full"></div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4 mb-6">
                                    <div class="bg-mewayz-bg p-4 rounded-lg">
                                        <div class="text-mewayz-primary mb-2">📊</div>
                                        <div class="text-2xl font-bold">12.4K</div>
                                        <div class="text-mewayz-text-secondary text-sm">Total Views</div>
                                    </div>
                                    <div class="bg-mewayz-bg p-4 rounded-lg">
                                        <div class="text-mewayz-secondary mb-2">🎯</div>
                                        <div class="text-2xl font-bold">3.2K</div>
                                        <div class="text-mewayz-text-secondary text-sm">Link Clicks</div>
                                    </div>
                                </div>
                                <div class="space-y-3">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-mewayz-primary rounded-full flex items-center justify-center">
                                            <span class="text-white text-sm">📱</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium">Social Media Post</div>
                                            <div class="text-xs text-mewayz-text-secondary">2 minutes ago</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-mewayz-accent rounded-full flex items-center justify-center">
                                            <span class="text-white text-sm">🔗</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium">Bio Site Updated</div>
                                            <div class="text-xs text-mewayz-text-secondary">5 minutes ago</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-mewayz-surface">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4">Everything You Need to Grow Your Business</h2>
                <p class="text-xl text-mewayz-text-secondary">Powerful tools designed for modern creators and entrepreneurs</p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Social Media Management -->
                <div class="feature-card bg-mewayz-bg border border-mewayz-border rounded-xl p-8">
                    <div class="w-12 h-12 bg-mewayz-primary bg-opacity-10 rounded-lg flex items-center justify-center mb-6">
                        <span class="text-2xl">📱</span>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">Social Media Management</h3>
                    <p class="text-mewayz-text-secondary mb-6">Schedule posts, manage multiple accounts, and track engagement across all major platforms.</p>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-center"><span class="text-mewayz-accent mr-2">✓</span>Multi-platform posting</li>
                        <li class="flex items-center"><span class="text-mewayz-accent mr-2">✓</span>Content scheduling</li>
                        <li class="flex items-center"><span class="text-mewayz-accent mr-2">✓</span>Analytics & insights</li>
                    </ul>
                </div>

                <!-- Link in Bio -->
                <div class="feature-card bg-mewayz-bg border border-mewayz-border rounded-xl p-8">
                    <div class="w-12 h-12 bg-mewayz-secondary bg-opacity-10 rounded-lg flex items-center justify-center mb-6">
                        <span class="text-2xl">🔗</span>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">Link in Bio Builder</h3>
                    <p class="text-mewayz-text-secondary mb-6">Create stunning bio pages with custom links, themes, and analytics tracking.</p>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-center"><span class="text-mewayz-accent mr-2">✓</span>Custom themes</li>
                        <li class="flex items-center"><span class="text-mewayz-accent mr-2">✓</span>Click tracking</li>
                        <li class="flex items-center"><span class="text-mewayz-accent mr-2">✓</span>QR code generation</li>
                    </ul>
                </div>

                <!-- CRM -->
                <div class="feature-card bg-mewayz-bg border border-mewayz-border rounded-xl p-8">
                    <div class="w-12 h-12 bg-mewayz-accent bg-opacity-10 rounded-lg flex items-center justify-center mb-6">
                        <span class="text-2xl">👥</span>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">CRM & Lead Management</h3>
                    <p class="text-mewayz-text-secondary mb-6">Manage contacts, track leads, and nurture relationships with powerful CRM tools.</p>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-center"><span class="text-mewayz-accent mr-2">✓</span>Contact management</li>
                        <li class="flex items-center"><span class="text-mewayz-accent mr-2">✓</span>Lead scoring</li>
                        <li class="flex items-center"><span class="text-mewayz-accent mr-2">✓</span>Pipeline tracking</li>
                    </ul>
                </div>

                <!-- Email Marketing -->
                <div class="feature-card bg-mewayz-bg border border-mewayz-border rounded-xl p-8">
                    <div class="w-12 h-12 bg-mewayz-warning bg-opacity-10 rounded-lg flex items-center justify-center mb-6">
                        <span class="text-2xl">📧</span>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">Email Marketing</h3>
                    <p class="text-mewayz-text-secondary mb-6">Create, send, and track email campaigns with beautiful templates and automation.</p>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-center"><span class="text-mewayz-accent mr-2">✓</span>Campaign builder</li>
                        <li class="flex items-center"><span class="text-mewayz-accent mr-2">✓</span>Email templates</li>
                        <li class="flex items-center"><span class="text-mewayz-accent mr-2">✓</span>Automation workflows</li>
                    </ul>
                </div>

                <!-- E-commerce -->
                <div class="feature-card bg-mewayz-bg border border-mewayz-border rounded-xl p-8">
                    <div class="w-12 h-12 bg-mewayz-danger bg-opacity-10 rounded-lg flex items-center justify-center mb-6">
                        <span class="text-2xl">🛒</span>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">E-commerce Store</h3>
                    <p class="text-mewayz-text-secondary mb-6">Sell products directly through your bio pages with integrated payment processing.</p>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-center"><span class="text-mewayz-accent mr-2">✓</span>Product catalog</li>
                        <li class="flex items-center"><span class="text-mewayz-accent mr-2">✓</span>Payment processing</li>
                        <li class="flex items-center"><span class="text-mewayz-accent mr-2">✓</span>Order management</li>
                    </ul>
                </div>

                <!-- Course Creation -->
                <div class="feature-card bg-mewayz-bg border border-mewayz-border rounded-xl p-8">
                    <div class="w-12 h-12 bg-mewayz-primary bg-opacity-10 rounded-lg flex items-center justify-center mb-6">
                        <span class="text-2xl">🎓</span>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">Course Creation</h3>
                    <p class="text-mewayz-text-secondary mb-6">Create and sell online courses with video lessons, quizzes, and student tracking.</p>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-center"><span class="text-mewayz-accent mr-2">✓</span>Video hosting</li>
                        <li class="flex items-center"><span class="text-mewayz-accent mr-2">✓</span>Student progress</li>
                        <li class="flex items-center"><span class="text-mewayz-accent mr-2">✓</span>Certificate generation</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Demo Section -->
    <section id="demo" class="py-20 bg-mewayz-bg">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4">See Mewayz in Action</h2>
                <p class="text-xl text-mewayz-text-secondary">Experience the power of our all-in-one platform</p>
            </div>
            <div class="grid md:grid-cols-2 gap-12">
                <!-- Laravel Web App Preview -->
                <div class="text-center">
                    <h3 class="text-2xl font-semibold mb-4">Web Application</h3>
                    <p class="text-mewayz-text-secondary mb-6">Full-featured desktop experience with advanced analytics and management tools.</p>
                    <div class="demo-preview mb-6">
                        <div class="bg-mewayz-surface p-1">
                            <div class="bg-mewayz-bg rounded-lg p-8">
                                <div class="flex items-center justify-between mb-6">
                                    <h4 class="text-lg font-semibold">Dashboard</h4>
                                    <div class="flex space-x-2">
                                        <div class="w-2 h-2 bg-mewayz-primary rounded-full"></div>
                                        <div class="w-2 h-2 bg-mewayz-secondary rounded-full"></div>
                                        <div class="w-2 h-2 bg-mewayz-accent rounded-full"></div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-3 gap-4">
                                    <div class="bg-mewayz-surface p-4 rounded-lg text-center">
                                        <div class="text-lg font-bold text-mewayz-primary">12.4K</div>
                                        <div class="text-xs text-mewayz-text-secondary">Views</div>
                                    </div>
                                    <div class="bg-mewayz-surface p-4 rounded-lg text-center">
                                        <div class="text-lg font-bold text-mewayz-secondary">3.2K</div>
                                        <div class="text-xs text-mewayz-text-secondary">Clicks</div>
                                    </div>
                                    <div class="bg-mewayz-surface p-4 rounded-lg text-center">
                                        <div class="text-lg font-bold text-mewayz-accent">$2.1K</div>
                                        <div class="text-xs text-mewayz-text-secondary">Revenue</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href="/dashboard" class="inline-block px-6 py-3 bg-mewayz-primary text-white rounded-lg hover:bg-opacity-90 transition-colors">
                        Try Web App
                    </a>
                </div>

                <!-- Flutter App Preview -->
                <div class="text-center">
                    <h3 class="text-2xl font-semibold mb-4">Mobile Application</h3>
                    <p class="text-mewayz-text-secondary mb-6">Native mobile experience optimized for creators on the go.</p>
                    <div class="demo-preview mb-6 max-w-sm mx-auto">
                        <div class="bg-mewayz-surface p-1">
                            <div class="bg-mewayz-bg rounded-lg p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="w-8 h-8 bg-mewayz-primary rounded-lg flex items-center justify-center">
                                        <span class="text-white font-bold">M</span>
                                    </div>
                                    <div class="w-6 h-6 bg-mewayz-text-secondary rounded-full"></div>
                                </div>
                                <div class="text-center mb-6">
                                    <h4 class="text-lg font-semibold mb-2">Welcome back</h4>
                                    <p class="text-mewayz-text-secondary text-sm">Sign in to your account</p>
                                </div>
                                <div class="space-y-4">
                                    <div class="bg-mewayz-surface p-3 rounded-lg">
                                        <div class="text-xs text-mewayz-text-secondary">Email</div>
                                    </div>
                                    <div class="bg-mewayz-surface p-3 rounded-lg">
                                        <div class="text-xs text-mewayz-text-secondary">Password</div>
                                    </div>
                                    <div class="bg-mewayz-primary p-3 rounded-lg text-center">
                                        <div class="text-white font-medium">Sign In</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href="/app" class="inline-block px-6 py-3 bg-mewayz-secondary text-white rounded-lg hover:bg-opacity-90 transition-colors">
                        Try Mobile App
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-20 bg-mewayz-surface">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4">Simple, Transparent Pricing</h2>
                <p class="text-xl text-mewayz-text-secondary">Choose the perfect plan for your business needs</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Starter Plan -->
                <div class="bg-mewayz-bg border border-mewayz-border rounded-xl p-8">
                    <div class="text-center mb-8">
                        <h3 class="text-2xl font-bold mb-2">Starter</h3>
                        <div class="text-4xl font-bold mb-2">$29<span class="text-lg text-mewayz-text-secondary">/month</span></div>
                        <p class="text-mewayz-text-secondary">Perfect for individuals</p>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center"><span class="text-mewayz-accent mr-2">✓</span>5 Bio Sites</li>
                        <li class="flex items-center"><span class="text-mewayz-accent mr-2">✓</span>3 Social Accounts</li>
                        <li class="flex items-center"><span class="text-mewayz-accent mr-2">✓</span>Basic Analytics</li>
                        <li class="flex items-center"><span class="text-mewayz-accent mr-2">✓</span>Email Support</li>
                    </ul>
                    <button class="w-full py-3 border border-mewayz-border rounded-lg hover:border-mewayz-primary transition-colors">
                        Get Started
                    </button>
                </div>

                <!-- Professional Plan -->
                <div class="bg-mewayz-bg border-2 border-mewayz-primary rounded-xl p-8 relative">
                    <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 bg-mewayz-primary px-4 py-1 rounded-full text-sm font-medium">
                        Most Popular
                    </div>
                    <div class="text-center mb-8">
                        <h3 class="text-2xl font-bold mb-2">Professional</h3>
                        <div class="text-4xl font-bold mb-2">$79<span class="text-lg text-mewayz-text-secondary">/month</span></div>
                        <p class="text-mewayz-text-secondary">For growing businesses</p>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center"><span class="text-mewayz-accent mr-2">✓</span>25 Bio Sites</li>
                        <li class="flex items-center"><span class="text-mewayz-accent mr-2">✓</span>10 Social Accounts</li>
                        <li class="flex items-center"><span class="text-mewayz-accent mr-2">✓</span>Advanced Analytics</li>
                        <li class="flex items-center"><span class="text-mewayz-accent mr-2">✓</span>Email Marketing</li>
                        <li class="flex items-center"><span class="text-mewayz-accent mr-2">✓</span>CRM & Leads</li>
                        <li class="flex items-center"><span class="text-mewayz-accent mr-2">✓</span>Priority Support</li>
                    </ul>
                    <button class="w-full py-3 bg-mewayz-primary text-white rounded-lg hover:bg-opacity-90 transition-colors">
                        Get Started
                    </button>
                </div>

                <!-- Enterprise Plan -->
                <div class="bg-mewayz-bg border border-mewayz-border rounded-xl p-8">
                    <div class="text-center mb-8">
                        <h3 class="text-2xl font-bold mb-2">Enterprise</h3>
                        <div class="text-4xl font-bold mb-2">$199<span class="text-lg text-mewayz-text-secondary">/month</span></div>
                        <p class="text-mewayz-text-secondary">For large organizations</p>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center"><span class="text-mewayz-accent mr-2">✓</span>Unlimited Bio Sites</li>
                        <li class="flex items-center"><span class="text-mewayz-accent mr-2">✓</span>Unlimited Social Accounts</li>
                        <li class="flex items-center"><span class="text-mewayz-accent mr-2">✓</span>Custom Analytics</li>
                        <li class="flex items-center"><span class="text-mewayz-accent mr-2">✓</span>White-label Options</li>
                        <li class="flex items-center"><span class="text-mewayz-accent mr-2">✓</span>API Access</li>
                        <li class="flex items-center"><span class="text-mewayz-accent mr-2">✓</span>24/7 Support</li>
                    </ul>
                    <button class="w-full py-3 border border-mewayz-border rounded-lg hover:border-mewayz-primary transition-colors">
                        Contact Sales
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-mewayz-bg">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-4xl font-bold mb-4">Ready to Transform Your Business?</h2>
            <p class="text-xl text-mewayz-text-secondary mb-8">Join thousands of creators who trust Mewayz to grow their online presence</p>
            <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                <a href="/register" class="px-8 py-4 bg-mewayz-primary text-white rounded-lg font-semibold hover:bg-opacity-90 transition-colors">
                    Start Free Trial
                </a>
                <a href="#demo" class="px-8 py-4 border border-mewayz-border text-mewayz-text hover:border-mewayz-primary transition-colors rounded-lg font-semibold">
                    Book a Demo
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-mewayz-surface border-t border-mewayz-border py-12">
        <div class="container mx-auto px-6">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-8 h-8 bg-mewayz-primary rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold">M</span>
                        </div>
                        <span class="text-xl font-bold">Mewayz</span>
                    </div>
                    <p class="text-mewayz-text-secondary">The all-in-one platform for modern creators and entrepreneurs.</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Product</h4>
                    <ul class="space-y-2 text-mewayz-text-secondary">
                        <li><a href="#" class="hover:text-mewayz-primary">Features</a></li>
                        <li><a href="#" class="hover:text-mewayz-primary">Pricing</a></li>
                        <li><a href="#" class="hover:text-mewayz-primary">Integrations</a></li>
                        <li><a href="#" class="hover:text-mewayz-primary">API</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Support</h4>
                    <ul class="space-y-2 text-mewayz-text-secondary">
                        <li><a href="#" class="hover:text-mewayz-primary">Help Center</a></li>
                        <li><a href="#" class="hover:text-mewayz-primary">Contact Us</a></li>
                        <li><a href="#" class="hover:text-mewayz-primary">Status</a></li>
                        <li><a href="#" class="hover:text-mewayz-primary">Community</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Company</h4>
                    <ul class="space-y-2 text-mewayz-text-secondary">
                        <li><a href="#" class="hover:text-mewayz-primary">About</a></li>
                        <li><a href="#" class="hover:text-mewayz-primary">Blog</a></li>
                        <li><a href="#" class="hover:text-mewayz-primary">Careers</a></li>
                        <li><a href="#" class="hover:text-mewayz-primary">Privacy</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-mewayz-border mt-8 pt-8 text-center text-mewayz-text-secondary">
                <p>&copy; 2025 Mewayz. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>