<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @php
            $appName = config('app.name', 'Laravel');
        @endphp
        <title>{{ isset($title) ? "$appName - $title" : $appName }}</title>


        <!-- Favicon -->
        <link href="{{ favicon() }}" rel="shortcut icon" type="image/png" />
        <!-- Scripts -->
        @vite([
            'resources/sass/app.scss',
        ])
        @livewireStyles
    </head>
    <body class="font-sans text-gray-900 antialiased">
        
        <div>
            
            {{ $slot }}
        </div>
        
        @livewireScripts
    </body>
</html>
