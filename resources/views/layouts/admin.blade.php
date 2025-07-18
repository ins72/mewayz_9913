<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - Mewayz Platform</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/mewayz-icon-32.png') }}">
    
    <!-- CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    <!-- Additional Styles -->
    @stack('styles')
</head>
<body class="bg-app-bg text-primary-text">
    <!-- Admin Header -->
    <header class="bg-card-bg border-b border-border-color sticky top-0 z-40">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <img src="{{ asset('images/mewayz-logo.png') }}" alt="Mewayz" class="h-8 w-8">
                    <h1 class="text-xl font-bold text-primary-text">Admin Panel</h1>
                    <div class="px-2 py-1 bg-error/20 text-error rounded text-xs font-medium">
                        ADMIN
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <!-- Search -->
                    <div class="relative">
                        <input type="text" 
                               placeholder="Search users, workspaces..."
                               class="w-64 px-4 py-2 bg-background border border-border-color rounded-lg text-primary-text focus:outline-none focus:ring-2 focus:ring-info focus:border-transparent">
                        <button class="absolute right-2 top-1/2 transform -translate-y-1/2">
                            <x-icon name="search" size="sm" class="text-secondary-text" alt="Search" />
                        </button>
                    </div>
                    
                    <!-- Notifications -->
                    <button class="relative p-2 text-secondary-text hover:text-primary-text">
                        <x-icon name="notification" size="md" alt="Notifications" />
                        <span class="absolute -top-1 -right-1 bg-error text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">3</span>
                    </button>
                    
                    <!-- Admin Profile -->
                    <div class="relative">
                        <button class="flex items-center space-x-2 text-secondary-text hover:text-primary-text">
                            <div class="w-8 h-8 bg-info rounded-full flex items-center justify-center">
                                <span class="text-white font-medium text-sm">{{ substr(auth()->user()->name ?? 'A', 0, 1) }}</span>
                            </div>
                            <span class="hidden md:block">{{ auth()->user()->name ?? 'Admin' }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="flex">
        <!-- Admin Sidebar -->
        <aside class="w-64 bg-card-bg border-r border-border-color min-h-screen">
            <nav class="p-4">
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('admin.dashboard') }}" 
                           class="flex items-center space-x-3 px-4 py-3 text-secondary-text hover:text-primary-text hover:bg-hover-bg rounded-lg transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-info/20 text-info' : '' }}">
                            <x-icon name="dashboard" size="sm" alt="Dashboard" />
                            <span>Dashboard</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('admin.users.index') }}" 
                           class="flex items-center space-x-3 px-4 py-3 text-secondary-text hover:text-primary-text hover:bg-hover-bg rounded-lg transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'bg-info/20 text-info' : '' }}">
                            <x-icon name="users" size="sm" alt="Users" />
                            <span>Users</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('admin.workspaces.index') }}" 
                           class="flex items-center space-x-3 px-4 py-3 text-secondary-text hover:text-primary-text hover:bg-hover-bg rounded-lg transition-all duration-200 {{ request()->routeIs('admin.workspaces.*') ? 'bg-info/20 text-info' : '' }}">
                            <x-icon name="dashboard" size="sm" alt="Workspaces" />
                            <span>Workspaces</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('admin.subscriptions.index') }}" 
                           class="flex items-center space-x-3 px-4 py-3 text-secondary-text hover:text-primary-text hover:bg-hover-bg rounded-lg transition-all duration-200 {{ request()->routeIs('admin.subscriptions.*') ? 'bg-info/20 text-info' : '' }}">
                            <x-icon name="dollar" size="sm" alt="Subscriptions" />
                            <span>Subscriptions</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('admin.plans.index') }}" 
                           class="flex items-center space-x-3 px-4 py-3 text-secondary-text hover:text-primary-text hover:bg-hover-bg rounded-lg transition-all duration-200 {{ request()->routeIs('admin.plans.*') ? 'bg-info/20 text-info' : '' }}">
                            <x-icon name="settings" size="sm" alt="Plans" />
                            <span>Plans & Pricing</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('admin.features.index') }}" 
                           class="flex items-center space-x-3 px-4 py-3 text-secondary-text hover:text-primary-text hover:bg-hover-bg rounded-lg transition-all duration-200 {{ request()->routeIs('admin.features.*') ? 'bg-info/20 text-info' : '' }}">
                            <x-icon name="lightbulb" size="sm" alt="Features" />
                            <span>Features</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('admin.transactions.index') }}" 
                           class="flex items-center space-x-3 px-4 py-3 text-secondary-text hover:text-primary-text hover:bg-hover-bg rounded-lg transition-all duration-200 {{ request()->routeIs('admin.transactions.*') ? 'bg-info/20 text-info' : '' }}">
                            <x-icon name="chart" size="sm" alt="Transactions" />
                            <span>Transactions</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('admin.analytics.index') }}" 
                           class="flex items-center space-x-3 px-4 py-3 text-secondary-text hover:text-primary-text hover:bg-hover-bg rounded-lg transition-all duration-200 {{ request()->routeIs('admin.analytics.*') ? 'bg-info/20 text-info' : '' }}">
                            <x-icon name="trending" size="sm" alt="Analytics" />
                            <span>Analytics</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('admin.system.index') }}" 
                           class="flex items-center space-x-3 px-4 py-3 text-secondary-text hover:text-primary-text hover:bg-hover-bg rounded-lg transition-all duration-200 {{ request()->routeIs('admin.system.*') ? 'bg-info/20 text-info' : '' }}">
                            <x-icon name="settings" size="sm" alt="System" />
                            <span>System</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="{{ route('admin.audit.index') }}" 
                           class="flex items-center space-x-3 px-4 py-3 text-secondary-text hover:text-primary-text hover:bg-hover-bg rounded-lg transition-all duration-200 {{ request()->routeIs('admin.audit.*') ? 'bg-info/20 text-info' : '' }}">
                            <x-icon name="shield" size="sm" alt="Audit" />
                            <span>Audit Logs</span>
                        </a>
                    </li>
                </ul>
                
                <!-- Divider -->
                <div class="my-6 border-t border-border-color"></div>
                
                <!-- Quick Actions -->
                <div class="space-y-2">
                    <p class="text-xs font-medium text-secondary-text uppercase tracking-wider px-4">Quick Actions</p>
                    
                    <button class="w-full flex items-center space-x-3 px-4 py-2 text-sm text-secondary-text hover:text-primary-text hover:bg-hover-bg rounded-lg transition-all duration-200">
                        <x-icon name="plus" size="xs" alt="Add" />
                        <span>Create User</span>
                    </button>
                    
                    <button class="w-full flex items-center space-x-3 px-4 py-2 text-sm text-secondary-text hover:text-primary-text hover:bg-hover-bg rounded-lg transition-all duration-200">
                        <x-icon name="mail" size="xs" alt="Email" />
                        <span>Send Broadcast</span>
                    </button>
                    
                    <button class="w-full flex items-center space-x-3 px-4 py-2 text-sm text-secondary-text hover:text-primary-text hover:bg-hover-bg rounded-lg transition-all duration-200">
                        <x-icon name="download" size="xs" alt="Export" />
                        <span>Export Data</span>
                    </button>
                </div>
                
                <!-- Return to Main App -->
                <div class="mt-6 pt-4 border-t border-border-color">
                    <a href="{{ route('dashboard-index') }}" 
                       class="flex items-center space-x-3 px-4 py-3 text-secondary-text hover:text-primary-text hover:bg-hover-bg rounded-lg transition-all duration-200">
                        <x-icon name="back" size="sm" alt="Back" />
                        <span>Back to App</span>
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    
    <!-- Additional Scripts -->
    @stack('scripts')
</body>
</html>