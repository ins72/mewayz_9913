<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://cdn.tailwindcss.com"></script>
        @livewireStyles
    </head>
    <body id="app-sandy" app-sandy="wrapper" data-theme="{{ session('theme', 'light') }}">
        <div app-sandy="container" app-sandy-namespace="@yield('namespace')">
            <div class="p-1 text-xs flex items-center justify-between bg-yellow-200 hidden">
                <span>{{ __('BETA') }}</span>
                <livewire:components.theme-toggle />
            </div>
            @yield('content')
        </div>
        
        <!-- Floating Theme Toggle -->
        @include('components.floating-theme-toggle')

        
        

        @livewireScriptConfig

        @vite([
         'resources/js/yenaWire.js'
        ])
    </body>
</html>