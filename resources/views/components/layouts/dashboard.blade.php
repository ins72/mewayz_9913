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

                <!-- Workspace Setup -->
                <div class="nav-section">
                    <div class="nav-section-title">Setup</div>
                    
                    <a href="{{ route('dashboard-workspace-index') }}" class="nav-item {{ request()->routeIs('dashboard-workspace-*') ? 'active' : '' }}">
                        <div class="nav-item-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                        </div>
                        <span class="nav-item-text">Workspace Setup</span>
                    </a>
                </div>

                <!-- Content & Sites -->
                <div class="nav-section">
                    <div class="nav-section-title">Content & Sites</div>
                    
                    <a href="{{ route('dashboard-sites-index') }}" class="nav-item {{ request()->routeIs('dashboard-sites-*') ? 'active' : '' }}">
                        <div class="nav-item-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"></path>
                            </svg>
                        </div>
                        <span class="nav-item-text">Sites</span>
                    </a>
                    
                    <a href="{{ route('dashboard-linkinbio-index') }}" class="nav-item {{ request()->routeIs('dashboard-linkinbio-*') ? 'active' : '' }}">
                        <div class="nav-item-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                            </svg>
                        </div>
                        <span class="nav-item-text">Link in Bio</span>
                    </a>
                    
                    <a href="{{ route('dashboard-templates-index') }}" class="nav-item {{ request()->routeIs('dashboard-templates-*') ? 'active' : '' }}">
                        <div class="nav-item-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <span class="nav-item-text">Templates</span>
                    </a>
                </div>

                <!-- Social Media -->
                <div class="nav-section">
                    <div class="nav-section-title">Social Media</div>
                    
                    <a href="{{ route('dashboard-instagram-index') }}" class="nav-item {{ request()->routeIs('dashboard-instagram-*') ? 'active' : '' }}">
                        <div class="nav-item-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <span class="nav-item-text">Instagram</span>
                    </a>
                    
                    <a href="{{ route('dashboard-social-index') }}" class="nav-item {{ request()->routeIs('dashboard-social-*') ? 'active' : '' }}">
                        <div class="nav-item-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                        <span class="nav-item-text">Social Media</span>
                    </a>
                </div>

                <!-- Business Growth -->
                <div class="nav-section">
                    <div class="nav-section-title">Business Growth</div>
                    
                    <a href="{{ route('dashboard-audience-index') }}" class="nav-item {{ request()->routeIs('dashboard-audience-*') ? 'active' : '' }}">
                        <div class="nav-item-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <span class="nav-item-text">Audience</span>
                    </a>
                    
                    <a href="{{ route('dashboard-crm-index') }}" class="nav-item {{ request()->routeIs('dashboard-crm-*') ? 'active' : '' }}">
                        <div class="nav-item-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <span class="nav-item-text">CRM & Leads</span>
                    </a>
                    
                    <a href="{{ route('dashboard-community-index') }}" class="nav-item {{ request()->routeIs('dashboard-community-*') ? 'active' : '' }}">
                        <div class="nav-item-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                        <span class="nav-item-text">Community</span>
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
                    
                    <a href="{{ route('dashboard-booking-index') }}" class="nav-item {{ request()->routeIs('dashboard-booking-*') ? 'active' : '' }}">
                        <div class="nav-item-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <span class="nav-item-text">Booking</span>
                    </a>
                </div>

                <!-- Marketing -->
                <div class="nav-section">
                    <div class="nav-section-title">Marketing</div>
                    
                    <a href="{{ route('dashboard-email-index') }}" class="nav-item {{ request()->routeIs('dashboard-email-*') ? 'active' : '' }}">
                        <div class="nav-item-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <span class="nav-item-text">Email Marketing</span>
                    </a>
                    
                    <a href="{{ route('dashboard-automation-index') }}" class="nav-item {{ request()->routeIs('dashboard-automation-*') ? 'active' : '' }}">
                        <div class="nav-item-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </div>
                        <span class="nav-item-text">Automation</span>
                    </a>
                </div>

                <!-- Analytics -->
                <div class="nav-section">
                    <div class="nav-section-title">Analytics</div>
                    
                    <a href="{{ route('dashboard-analytics-index') }}" class="nav-item {{ request()->routeIs('dashboard-analytics-*') ? 'active' : '' }}">
                        <div class="nav-item-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <span class="nav-item-text">Analytics</span>
                    </a>
                    
                    <a href="{{ route('dashboard-reports-index') }}" class="nav-item {{ request()->routeIs('dashboard-reports-*') ? 'active' : '' }}">
                        <div class="nav-item-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <span class="nav-item-text">Reports</span>
                    </a>
                </div>

                <!-- Business Management -->
                <div class="nav-section">
                    <div class="nav-section-title">Business Management</div>
                    
                    <a href="{{ route('dashboard-wallet-index') }}" class="nav-item {{ request()->routeIs('dashboard-wallet-*') ? 'active' : '' }}">
                        <div class="nav-item-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                        </div>
                        <span class="nav-item-text">Wallet</span>
                    </a>
                    
                    <a href="{{ route('dashboard-invoices-index') }}" class="nav-item {{ request()->routeIs('dashboard-invoices-*') ? 'active' : '' }}">
                        <div class="nav-item-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <span class="nav-item-text">Invoices</span>
                    </a>
                    
                    <a href="{{ route('dashboard-team-index') }}" class="nav-item {{ request()->routeIs('dashboard-team-*') ? 'active' : '' }}">
                        <div class="nav-item-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <span class="nav-item-text">Team</span>
                    </a>
                </div>

                <!-- AI & Tools -->
                <div class="nav-section">
                    <div class="nav-section-title">AI & Tools</div>
                    
                    <a href="{{ route('dashboard-ai-index') }}" class="nav-item {{ request()->routeIs('dashboard-ai-*') ? 'active' : '' }}">
                        <div class="nav-item-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                        </div>
                        <span class="nav-item-text">AI Assistant</span>
                    </a>
                    
                    <a href="{{ route('dashboard-media-index') }}" class="nav-item {{ request()->routeIs('dashboard-media-*') ? 'active' : '' }}">
                        <div class="nav-item-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m0 0V1a1 1 0 011-1h2a1 1 0 011 1v3M7 4H5a1 1 0 00-1 1v14a1 1 0 001 1h14a1 1 0 001-1V5a1 1 0 00-1-1h-2M7 4h10M9 9h6m-6 4h6m-6 4h6"></path>
                            </svg>
                        </div>
                        <span class="nav-item-text">Media Library</span>
                    </a>
                    
                    <a href="{{ route('dashboard-integrations-index') }}" class="nav-item {{ request()->routeIs('dashboard-integrations-*') ? 'active' : '' }}">
                        <div class="nav-item-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"></path>
                            </svg>
                        </div>
                        <span class="nav-item-text">Integrations</span>
                    </a>
                </div>

                <!-- Settings -->
                <div class="nav-section">
                    <div class="nav-section-title">Settings</div>
                    
                    <a href="{{ route('dashboard-settings-index') }}" class="nav-item {{ request()->routeIs('dashboard-settings-*') ? 'active' : '' }}">
                        <div class="nav-item-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <span class="nav-item-text">Settings</span>
                    </a>
                    
                    <a href="{{ route('dashboard-help-index') }}" class="nav-item {{ request()->routeIs('dashboard-help-*') ? 'active' : '' }}">
                        <div class="nav-item-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <span class="nav-item-text">Help & Support</span>
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