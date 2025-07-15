<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="csrf_token" value="{{ csrf_token() }}"/>
        @php
            $appName = config('app.name', 'Laravel');
        @endphp
        <title>{{ isset($title) ? "$appName - $title" : $appName }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Figtree:ital,wght@0,300..900;1,300..900&family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">


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
            'resources/js/app.js',
            'resources/js/moreUtils.js',
        ])


        @livewireStyles
    </head>
    <body class="font-sans text-gray-900 antialiased bg-[var(--color-neutral-05)]" x-data="appData">
        
        <div>
            <livewire:components.mediakit.layout.application :site="__s()" :key="uukey('builder', 'mediakit::application')" />
        </div>
        
        @persist('app-toolbar')
            <livewire:components.console.utils.toast :key="uukey('builder', 'toaster')">
        @endpersist

        {{-- @include('includes.thumbstyle') --}}

        <script>
            var object = {
                mediaUrl: "{{ gs('media/mediakit/images') }}",
                baseUrl: "{{ config('app.url') }}",
                currentBaseUrl: "{{ url('/') }}",
                sitePrefix: "{{ config('app.mediakit_prefix') }}",
                copiedText: "{{ __('Copied') }}",
                logoBranding: [
                    '{{ logo_branding() }}',
                    '{{ logo_branding('dark') }}',
                ]
            };

            window.builderObject = object;
        </script>
        @livewireScriptConfig
        
        @vite([
         'resources/js/yenaWire.js'
        ])
    </body>
</html>
