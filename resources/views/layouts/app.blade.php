<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Mewayz - The Ultimate All-in-One Business Platform')</title>
    
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

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .card {
            background-color: var(--bg-secondary);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-primary);
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

        .btn-accent {
            background-color: var(--accent-primary);
            color: white;
            border-color: var(--accent-primary);
        }

        .btn-accent:hover {
            background-color: #2563eb;
            transform: translateY(-1px);
            box-shadow: var(--shadow-lg);
        }

        .btn-success {
            background-color: var(--accent-secondary);
            color: white;
            border-color: var(--accent-secondary);
        }

        .btn-warning {
            background-color: var(--accent-warning);
            color: white;
            border-color: var(--accent-warning);
        }

        .btn-error {
            background-color: var(--accent-error);
            color: white;
            border-color: var(--accent-error);
        }

        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-primary);
            border-radius: 8px;
            background-color: var(--bg-secondary);
            color: var(--text-primary);
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .text-secondary {
            color: var(--text-secondary);
        }

        .text-accent {
            color: var(--accent-primary);
        }

        .text-success {
            color: var(--accent-secondary);
        }

        .text-warning {
            color: var(--accent-warning);
        }

        .text-error {
            color: var(--accent-error);
        }

        .grid {
            display: grid;
            gap: 1.5rem;
        }

        .grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
        .grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .grid-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }

        .flex {
            display: flex;
        }

        .items-center {
            align-items: center;
        }

        .justify-center {
            justify-content: center;
        }

        .justify-between {
            justify-content: space-between;
        }

        .gap-4 {
            gap: 1rem;
        }

        .gap-6 {
            gap: 1.5rem;
        }

        .space-y-4 > * + * {
            margin-top: 1rem;
        }

        .space-y-6 > * + * {
            margin-top: 1.5rem;
        }

        .mb-4 {
            margin-bottom: 1rem;
        }

        .mb-6 {
            margin-bottom: 1.5rem;
        }

        .mt-4 {
            margin-top: 1rem;
        }

        .mt-6 {
            margin-top: 1.5rem;
        }

        .p-4 {
            padding: 1rem;
        }

        .p-6 {
            padding: 1.5rem;
        }

        .py-4 {
            padding-top: 1rem;
            padding-bottom: 1rem;
        }

        .py-6 {
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
        }

        .px-4 {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .px-6 {
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }

        .rounded-lg {
            border-radius: 8px;
        }

        .rounded-xl {
            border-radius: 12px;
        }

        .shadow-sm {
            box-shadow: var(--shadow-sm);
        }

        .shadow-md {
            box-shadow: var(--shadow-md);
        }

        .shadow-lg {
            box-shadow: var(--shadow-lg);
        }

        .shadow-xl {
            box-shadow: var(--shadow-xl);
        }

        .w-full {
            width: 100%;
        }

        .h-full {
            height: 100%;
        }

        .min-h-screen {
            min-height: 100vh;
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

        @media (max-width: 768px) {
            .container {
                padding: 0 0.75rem;
            }
            
            .grid-cols-2 {
                grid-template-columns: repeat(1, minmax(0, 1fr));
            }
            
            .grid-cols-3 {
                grid-template-columns: repeat(1, minmax(0, 1fr));
            }
            
            .grid-cols-4 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 480px) {
            .card {
                padding: 1rem;
            }
            
            .btn {
                padding: 0.625rem 1.25rem;
                font-size: 0.8125rem;
            }
        }
    </style>
</head>
<body class="h-full">
    <div class="theme-toggle" onclick="toggleTheme()">
        <span id="theme-icon">🌙</span>
    </div>
    
    <div class="min-h-screen">
        @yield('content')
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