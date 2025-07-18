<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Mewayz</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900" rel="stylesheet" />
    
    <!-- Styles -->
    <style>
        :root {
            /* Light Theme */
            --bg-primary: #FAFAFA;
            --bg-secondary: #FFFFFF;
            --text-primary: #1A1A1A;
            --text-secondary: #6B6B6B;
            --border-primary: #E5E5E5;
            --btn-primary-bg: #1A1A1A;
            --btn-primary-text: #FFFFFF;
            --btn-secondary-bg: #FFFFFF;
            --btn-secondary-text: #1A1A1A;
            --btn-secondary-border: #E5E5E5;
            --accent-primary: #3B82F6;
            --accent-secondary: #10B981;
            --accent-warning: #F59E0B;
            --accent-error: #EF4444;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        }
        
        [data-theme="dark"] {
            /* Dark Theme */
            --bg-primary: #101010;
            --bg-secondary: #191919;
            --text-primary: #F1F1F1;
            --text-secondary: #7B7B7B;
            --border-primary: #282828;
            --btn-primary-bg: #FDFDFD;
            --btn-primary-text: #141414;
            --btn-secondary-bg: #191919;
            --btn-secondary-text: #F1F1F1;
            --btn-secondary-border: #282828;
            --accent-primary: #3B82F6;
            --accent-secondary: #10B981;
            --accent-warning: #F59E0B;
            --accent-error: #EF4444;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.3);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.3), 0 2px 4px -2px rgb(0 0 0 / 0.3);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.3), 0 4px 6px -4px rgb(0 0 0 / 0.3);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.3), 0 8px 10px -6px rgb(0 0 0 / 0.3);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .dashboard-layout {
            display: grid;
            grid-template-columns: 250px 1fr;
            min-height: 100vh;
        }

        .sidebar {
            background-color: var(--bg-secondary);
            border-right: 1px solid var(--border-primary);
            padding: 1.5rem;
            overflow-y: auto;
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-primary);
        }

        .logo {
            width: 2rem;
            height: 2rem;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: white;
            font-size: 1.25rem;
        }

        .sidebar-nav {
            list-style: none;
        }

        .sidebar-nav li {
            margin-bottom: 0.5rem;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.2s ease;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .sidebar-nav a:hover {
            background-color: var(--bg-primary);
            color: var(--text-primary);
        }

        .sidebar-nav a.active {
            background-color: var(--accent-primary);
            color: white;
        }

        .main-content {
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .header {
            background-color: var(--bg-secondary);
            border-bottom: 1px solid var(--border-primary);
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .header-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .content {
            flex: 1;
            padding: 1.5rem;
            overflow-y: auto;
        }

        .card {
            background-color: var(--bg-secondary);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-primary);
            margin-bottom: 1.5rem;
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-primary);
        }

        .card-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background-color: var(--bg-secondary);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-primary);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 8px;
            border: 1px solid transparent;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            gap: 0.5rem;
        }

        .btn-primary {
            background-color: var(--btn-primary-bg);
            color: var(--btn-primary-text);
            border-color: var(--btn-primary-bg);
        }

        .btn-primary:hover {
            opacity: 0.9;
            transform: translateY(-1px);
            box-shadow: var(--shadow-lg);
        }

        .btn-secondary {
            background-color: var(--btn-secondary-bg);
            color: var(--btn-secondary-text);
            border-color: var(--btn-secondary-border);
        }

        .btn-secondary:hover {
            background-color: var(--bg-primary);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.8125rem;
        }

        .theme-toggle {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 1000;
            background: var(--bg-secondary);
            border: 1px solid var(--border-primary);
            border-radius: 50%;
            width: 3rem;
            height: 3rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: var(--shadow-md);
        }

        .theme-toggle:hover {
            transform: scale(1.1);
            box-shadow: var(--shadow-lg);
        }

        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--text-primary);
            cursor: pointer;
            font-size: 1.5rem;
        }

        @media (max-width: 768px) {
            .dashboard-layout {
                grid-template-columns: 1fr;
            }
            
            .sidebar {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 250px;
                height: 100vh;
                z-index: 1000;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar.active {
                display: block;
                transform: translateX(0);
            }
            
            .mobile-menu-toggle {
                display: block;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="h-full">
    <div class="theme-toggle" onclick="toggleTheme()">
        <span id="theme-icon">🌙</span>
    </div>
    
    <div class="dashboard-layout">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo">M</div>
                <span style="font-weight: 600; font-size: 1.125rem;">Mewayz</span>
            </div>
            
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <span>📊</span> Dashboard
                    </a></li>
                    <li><a href="{{ route('dashboard-workspace-index') }}" class="{{ request()->routeIs('dashboard-workspace-index') ? 'active' : '' }}">
                        <span>🏢</span> Workspace
                    </a></li>
                    <li><a href="{{ route('dashboard-sites-index') }}" class="{{ request()->routeIs('dashboard-sites-index') ? 'active' : '' }}">
                        <span>🌐</span> Sites
                    </a></li>
                    <li><a href="{{ route('dashboard-linkinbio-index') }}" class="{{ request()->routeIs('dashboard-linkinbio-index') ? 'active' : '' }}">
                        <span>🔗</span> Link in Bio
                    </a></li>
                    <li><a href="{{ route('dashboard-social-index') }}" class="{{ request()->routeIs('dashboard-social-index') ? 'active' : '' }}">
                        <span>📱</span> Social Media
                    </a></li>
                    <li><a href="{{ route('dashboard-store-index') }}" class="{{ request()->routeIs('dashboard-store-index') ? 'active' : '' }}">
                        <span>🛍️</span> Store
                    </a></li>
                    <li><a href="{{ route('dashboard-courses-index') }}" class="{{ request()->routeIs('dashboard-courses-index') ? 'active' : '' }}">
                        <span>📚</span> Courses
                    </a></li>
                    <li><a href="{{ route('dashboard-email-index') }}" class="{{ request()->routeIs('dashboard-email-index') ? 'active' : '' }}">
                        <span>✉️</span> Email Marketing
                    </a></li>
                    <li><a href="{{ route('dashboard-analytics-index') }}" class="{{ request()->routeIs('dashboard-analytics-index') ? 'active' : '' }}">
                        <span>📈</span> Analytics
                    </a></li>
                    <li><a href="{{ route('dashboard-ai-index') }}" class="{{ request()->routeIs('dashboard-ai-index') ? 'active' : '' }}">
                        <span>🤖</span> AI Assistant
                    </a></li>
                    <li><a href="{{ route('dashboard-booking-index') }}" class="{{ request()->routeIs('dashboard-booking-index') ? 'active' : '' }}">
                        <span>📅</span> Booking
                    </a></li>
                    <li><a href="{{ route('dashboard-team-index') }}" class="{{ request()->routeIs('dashboard-team-index') ? 'active' : '' }}">
                        <span>👥</span> Team
                    </a></li>
                    <li><a href="{{ route('dashboard-settings-index') }}" class="{{ request()->routeIs('dashboard-settings-index') ? 'active' : '' }}">
                        <span>⚙️</span> Settings
                    </a></li>
                </ul>
            </nav>
        </aside>
        
        <main class="main-content">
            <header class="header">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <button class="mobile-menu-toggle" onclick="toggleSidebar()">☰</button>
                    <h1 class="header-title">@yield('title', 'Dashboard')</h1>
                </div>
                
                <div class="header-actions">
                    <a href="{{ route('dashboard-upgrade-index') }}" class="btn btn-primary btn-sm">Upgrade</a>
                    <a href="{{ route('dashboard-help-index') }}" class="btn btn-secondary btn-sm">Help</a>
                </div>
            </header>
            
            <div class="content">
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const icon = document.getElementById('theme-icon');
            
            if (html.getAttribute('data-theme') === 'dark') {
                html.removeAttribute('data-theme');
                icon.textContent = '🌙';
                localStorage.setItem('theme', 'light');
            } else {
                html.setAttribute('data-theme', 'dark');
                icon.textContent = '☀️';
                localStorage.setItem('theme', 'dark');
            }
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
        }

        // Initialize theme
        const savedTheme = localStorage.getItem('theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        
        if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
            document.documentElement.setAttribute('data-theme', 'dark');
            document.getElementById('theme-icon').textContent = '☀️';
        }
    </script>
    
    @stack('scripts')
</body>
</html>