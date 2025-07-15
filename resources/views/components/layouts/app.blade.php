@props(['title' => 'Mewayz Dashboard'])

<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ $title }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite([
        'resources/css/app.css',
        'resources/sass/app.scss',
        'resources/sass/dashboard/dashboard.scss',
        'resources/sass/dashboard/dashboard.sidebar.scss',
        'resources/sass/dashboard/dashboard.placeholder.scss',
        'resources/sass/auth/auth.scss',
        'resources/sass/builder.scss',
        'resources/sass/create.scss',
        'resources/sass/site.scss',
        'resources/sass/dashboard/community.scss',
        'resources/js/app.js',
        'resources/js/moreUtils.js',
        'resources/js/exportUtils.js',
        'resources/js/yenaWire.js',
    ])
    
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-900 text-white">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-800 shadow-lg transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0">
            <div class="flex flex-col h-full">
                <livewire:components.dashboard.sidebar.menu />
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col lg:ml-0">
            <!-- Mobile menu button -->
            <div class="lg:hidden fixed top-4 left-4 z-50">
                <button type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white" @click="$store.app.toggleSidebar()">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
            
            <!-- Page Content -->
            <main class="flex-1 p-6 bg-gray-900 min-h-screen">
                {{ $slot }}
            </main>
        </div>
    </div>
    
    @livewireScripts
</body>
</html>