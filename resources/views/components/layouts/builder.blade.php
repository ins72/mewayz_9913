@props(['title' => 'Mewayz Builder'])

<!DOCTYPE html>
<html lang="en" class="auto">
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
<body class="font-sans antialiased">
    {{ $slot }}
    
    @livewireScripts
</body>
</html>