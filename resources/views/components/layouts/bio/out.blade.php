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

        @if (!isset($seo))
        <title>{{ isset($title) ? "$appName - $title" : $appName }}</title>
        @endif
        {{-- <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Figtree:ital,wght@0,300..900;1,300..900&family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet"> --}}

        {!! isset($seo) ? $seo : '' !!}

        <!-- Favicon -->
        <link href="{{ favicon() }}" rel="shortcut icon" type="image/png" />

        <!-- Scripts -->
        @vite([
            'resources/css/app.css',
            'resources/sass/app.scss',
            'resources/sass/dashboard/dashboard.sidebar.scss',
            'resources/sass/dashboard/dashboard.placeholder.scss',
            'resources/sass/site.scss',
            'resources/sass/create.scss',
            'resources/sass/builder/builder.scss',
            'resources/js/moreUtils.js',
            'resources/js/app.js',
        ])


        @livewireStyles
    </head>
    <body class="font-sans text-gray-900 antialiased" data-theme="light">
        
        <div>
            {{ $slot }}
        </div>

        @stack('scripts')

        <script>
            var object = {
                mediaUrl: "{{ gs('media/bio/images') }}",
                baseUrl: "{{ url('/') }}",
                copiedText: "{{ __('Copied') }}",
            };

            window.builderObject = object;

            window.dark_theme = true;
        </script>
        @livewireScriptConfig
        
        @include('includes.thumbstyle')
        
        <script src="{{ gs('assets/js/zuck.js') }}"></script>
        <script src="{{ gs('assets/js/flipdown.js') }}"></script>

        @vite([
         'resources/js/yenaWire.js'
        ])
    </body>
</html>
