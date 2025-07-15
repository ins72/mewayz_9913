<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

        @php
            $appName = config('app.name', 'Laravel');
        @endphp
        <title>{{ isset($title) ? "$appName - $title" : $appName }}</title>

        <!-- Favicon -->
        <link href="{{ favicon() }}" rel="shortcut icon" type="image/png" />

        <!-- Scripts -->

        {!! isset($meta) ? $meta : '' !!}
        @vite([
            'resources/css/app.css',
            'resources/sass/app.scss',
            'resources/sass/auth/auth.scss',
            'resources/js/app.js'
        ])

        <script type="text/javascript" src="{{ gs('assets/js/navigate.turbo.js') }}" defer></script>
        @livewireStyles
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gray-50 dark:bg-gray-900 dark:text-gray-100">
        
        <div>
            
            <div id="yenaApp">
                {{ $slot }}
            </div>
        </div>
        
        @persist('app-utils')
            <div wire:ignore>
                <livewire:components.console.utils.toast lazy="on-load">
            </div>
        @endpersist


        @livewireScriptConfig

        @vite([
         'resources/js/yenaWire.js'
        ])
{{-- 
        <script>
            document.addEventListener('navigateTurbo:ready', () => {
                navigateTurbo.init({

                    ...window.turboNavigate,
                    routes: [
                        '/login',
                        '/register',
                        '/forgot-password',
                    ],
                    prefetch: [
                        '/login',
                        '/register',
                        '/forgot-password',
                    ]
                })
            })
        </script> --}}
    </body>
</html>
