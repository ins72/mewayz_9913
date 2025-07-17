<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ $title ?? 'Dashboard - Mewayz' }}</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    
    <!-- PWA Meta Tags -->
    <meta name="application-name" content="Mewayz">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Mewayz">
    <meta name="description" content="Professional business platform dashboard">
    <meta name="format-detection" content="telephone=no">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#101010">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/sass/dashboard.scss'])
    @livewireStyles
    
    <style>
        /* Custom Dashboard Styles */
        :root {
            --background: #101010;
            --card-bg: #191919;
            --primary-text: #F1F1F1;
            --secondary-text: #7B7B7B;
            --border-color: #282828;
            --hover-bg: #222222;
            --success: #10B981;
            --error: #EF4444;
            --warning: #F59E0B;
            --info: #3B82F6;
        }
        
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--background);
            color: var(--primary-text);
        }
        
        /* Dashboard specific styles */
        .dashboard-sidebar {
            background-color: var(--card-bg);
            border-right: 1px solid var(--border-color);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            width: 260px;
            overflow-y: auto;
            z-index: 100;
        }
        
        .dashboard-main {
            margin-left: 260px;
            min-height: 100vh;
            background-color: var(--background);
        }
        
        .dashboard-header {
            background-color: var(--card-bg);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .dashboard-content {
            padding: 2rem;
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .dashboard-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .dashboard-card:hover {
            background-color: var(--hover-bg);
            transform: translateY(-1px);
        }
        
        .dashboard-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }
        
        .dashboard-card-title {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--secondary-text);
        }
        
        .dashboard-card-icon {
            width: 1.5rem;
            height: 1.5rem;
            color: var(--info);
        }
        
        .dashboard-card-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-text);
            margin-bottom: 0.5rem;
        }
        
        .dashboard-card-change {
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .dashboard-card-change.positive {
            color: var(--success);
        }
        
        .dashboard-card-change.negative {
            color: var(--error);
        }
        
        .dashboard-table {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            overflow: hidden;
        }
        
        .dashboard-table-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .dashboard-table-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--primary-text);
        }
        
        .sidebar-nav {
            padding: 1rem;
        }
        
        .sidebar-nav-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            margin-bottom: 0.5rem;
            border-radius: 8px;
            color: var(--secondary-text);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .sidebar-nav-item:hover {
            background-color: var(--hover-bg);
            color: var(--primary-text);
        }
        
        .sidebar-nav-item.active {
            background-color: var(--info);
            color: white;
        }
        
        .sidebar-nav-item svg {
            width: 1.25rem;
            height: 1.25rem;
            margin-right: 0.75rem;
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th,
        .table td {
            padding: 1rem 1.5rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        
        .table th {
            font-weight: 600;
            color: var(--primary-text);
            background-color: var(--background);
        }
        
        .table td {
            color: var(--secondary-text);
        }
        
        .table tbody tr:hover {
            background-color: var(--hover-bg);
        }
        
        .text-success { color: var(--success); }
        .text-error { color: var(--error); }
        .text-warning { color: var(--warning); }
        .text-info { color: var(--info); }
        
        /* Mobile responsive */
        @media (max-width: 768px) {
            .dashboard-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .dashboard-sidebar.open {
                transform: translateX(0);
            }
            
            .dashboard-main {
                margin-left: 0;
            }
            
            .dashboard-header {
                padding: 1rem;
            }
            
            .dashboard-content {
                padding: 1rem;
            }
            
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="antialiased">
    <div class="flex h-screen bg-background">
        <!-- Sidebar -->
        <div class="dashboard-sidebar" id="sidebar">
            <!-- Logo -->
            <div class="p-6 border-b border-border-color">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-info rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-primary-text">Mewayz</h1>
                        <p class="text-xs text-secondary-text">Business Platform</p>
                    </div>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="sidebar-nav">
                <a href="{{ route('dashboard-index') }}" class="sidebar-nav-item {{ request()->routeIs('dashboard-index') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h2a2 2 0 012 2v2H8V5z"/>
                    </svg>
                    Dashboard
                </a>
                
                <a href="{{ route('dashboard-instagram-index') }}" class="sidebar-nav-item {{ request()->routeIs('dashboard-instagram-index') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Instagram
                </a>
                
                <a href="{{ route('dashboard-linkinbio-index') }}" class="sidebar-nav-item {{ request()->routeIs('dashboard-linkinbio-index') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                    Link in Bio
                </a>
                
                <a href="{{ route('dashboard-courses-index') }}" class="sidebar-nav-item {{ request()->routeIs('dashboard-courses-index') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    Courses
                </a>
                
                <a href="{{ route('dashboard-store-index') }}" class="sidebar-nav-item {{ request()->routeIs('dashboard-store-index') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    Store
                </a>
                
                <a href="{{ route('dashboard-crm-index') }}" class="sidebar-nav-item {{ request()->routeIs('dashboard-crm-index') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    CRM
                </a>
                
                <a href="{{ route('dashboard-email-index') }}" class="sidebar-nav-item {{ request()->routeIs('dashboard-email-index') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Email Marketing
                </a>
                
                <a href="{{ route('dashboard-analytics-index') }}" class="sidebar-nav-item {{ request()->routeIs('dashboard-analytics-index') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Analytics
                </a>
                
                <a href="{{ route('dashboard-settings-index') }}" class="sidebar-nav-item {{ request()->routeIs('dashboard-settings-index') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Settings
                </a>
            </nav>
        </div>
        
        <!-- Main Content -->
        <div class="dashboard-main">
            <!-- Header -->
            <div class="dashboard-header">
                <div class="flex items-center">
                    <button class="mr-4 lg:hidden" onclick="toggleSidebar()">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <h1 class="text-xl font-semibold text-primary-text">{{ $pageTitle ?? 'Dashboard' }}</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <button class="relative">
                        <svg class="w-6 h-6 text-secondary-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-3.595-3.595a.908.908 0 00-1.28 0L15 17z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 17h3"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 13h.01"/>
                        </svg>
                        <span class="absolute -top-2 -right-2 bg-error text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">3</span>
                    </button>
                    <div class="relative">
                        <button class="flex items-center space-x-2 text-secondary-text hover:text-primary-text">
                            <div class="w-8 h-8 bg-info rounded-full flex items-center justify-center">
                                <span class="text-white font-medium">{{ substr(auth()->user()->name ?? 'U', 0, 1) }}</span>
                            </div>
                            <span>{{ auth()->user()->name ?? 'User' }}</span>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Content -->
            <div class="dashboard-content">
                {{ $slot }}
            </div>
        </div>
    </div>
    
    <!-- Mobile Sidebar Overlay -->
    <div class="fixed inset-0 bg-black bg-opacity-50 z-50 lg:hidden hidden" id="sidebar-overlay" onclick="closeSidebar()"></div>
    
    <!-- Scripts -->
    @vite(['resources/js/app.js'])
    @livewireScripts
    
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            
            sidebar.classList.toggle('open');
            overlay.classList.toggle('hidden');
        }
        
        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            
            sidebar.classList.remove('open');
            overlay.classList.add('hidden');
        }
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            
            if (window.innerWidth <= 768 && !sidebar.contains(e.target) && !e.target.closest('button')) {
                closeSidebar();
            }
        });
    </script>
</body>
</html>