<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ $title ?? 'Dashboard - Mewayz' }}</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/sass/app.scss', 'resources/sass/dashboard.scss'])
    @livewireStyles
</head>
<body class="font-sans antialiased" x-data="dashboard">
    <div class="dashboard-container">
        <!-- Sidebar -->
        <nav class="dashboard-sidebar" :class="{ 'open': sidebarOpen }">
            <div class="sidebar-header">
                <div class="sidebar-logo">M</div>
                <h2 class="sidebar-title">Mewayz</h2>
            </div>
            
            <div class="sidebar-nav">
                <!-- Main Navigation -->
                <div class="nav-section">
                    <a href="{{ route('dashboard-index') }}" class="nav-item {{ request()->routeIs('dashboard-index') ? 'active' : '' }}">
                        <div class="nav-item-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            </svg>
                        </div>
                        <span class="nav-item-text">Dashboard</span>
                    </a>
                </div>

                <!-- Business Growth -->
                <div class="nav-section">
                    <div class="nav-section-title">Business Growth</div>
                    
                    <a href="{{ route('dashboard-sites-index') }}" class="nav-item {{ request()->routeIs('dashboard-sites-*') ? 'active' : '' }}">
                        <div class="nav-item-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"></path>
                            </svg>
                        </div>
                        <span class="nav-item-text">Sites</span>
                    </a>
                    
                    <a href="{{ route('dashboard-audience-index') }}" class="nav-item {{ request()->routeIs('dashboard-audience-*') ? 'active' : '' }}">
                        <div class="nav-item-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <span class="nav-item-text">Audience</span>
                    </a>
                </div>

                <!-- Monetization -->
                <div class="nav-section">
                    <div class="nav-section-title">Monetization</div>
                    
                    <a href="{{ route('dashboard-store-index') }}" class="nav-item {{ request()->routeIs('dashboard-store-*') ? 'active' : '' }}">
                        <div class="nav-item-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                        </div>
                        <span class="nav-item-text">Store</span>
                    </a>
                    
                    <a href="{{ route('dashboard-courses-index') }}" class="nav-item {{ request()->routeIs('dashboard-courses-*') ? 'active' : '' }}">
                        <div class="nav-item-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <span class="nav-item-text">Courses</span>
                    </a>
                    
                    <a href="{{ route('dashboard-wallet-index') }}" class="nav-item {{ request()->routeIs('dashboard-wallet-*') ? 'active' : '' }}">
                        <div class="nav-item-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                        </div>
                        <span class="nav-item-text">Wallet</span>
                    </a>
                </div>

                <!-- Settings -->
                <div class="nav-section">
                    <div class="nav-section-title">Settings</div>
                    
                    <a href="#" class="nav-item">
                        <div class="nav-item-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <span class="nav-item-text">Settings</span>
                    </a>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="dashboard-main" :class="{ 'full-width': !sidebarOpen }">
            <!-- Header -->
            <header class="dashboard-header">
                <div class="dashboard-header-left">
                    <button class="mobile-menu-toggle" @click="toggleSidebar">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <h1 class="page-title">{{ $pageTitle ?? 'Dashboard' }}</h1>
                </div>
                
                <div class="dashboard-header-right">
                    <!-- User Menu -->
                    <div class="user-menu" x-data="dropdown">
                        <button class="user-menu-trigger" @click="toggle">
                            <div class="user-avatar">
                                {{ substr(auth()->user()->name ?? 'User', 0, 1) }}
                            </div>
                            <span class="hidden md:block">{{ auth()->user()->name ?? 'User' }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <div class="user-menu-dropdown" x-show="open" @click.away="close" x-transition>
                            <a href="#" class="user-menu-item">
                                <svg class="user-menu-item-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Profile
                            </a>
                            <a href="#" class="user-menu-item">
                                <svg class="user-menu-item-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Settings
                            </a>
                            <hr class="border-border-color my-2">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="user-menu-item w-full text-left">
                                    <svg class="user-menu-item-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="dashboard-content">
                {{ $slot }}
            </div>
        </main>
    </div>
    
    <!-- Scripts -->
    @vite(['resources/js/app.js'])
    @livewireScripts
</body>
</html>