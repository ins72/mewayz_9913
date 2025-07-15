<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Page Not Found' }} - {{ config('app.name') }}</title>
    <link rel="shortcut icon" href="{{ favicon() }}" type="image/x-icon">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900">
    <div class="min-h-screen flex flex-col justify-center items-center px-6 py-12">
        <div class="max-w-md w-full">
            <!-- Logo -->
            <div class="flex justify-center mb-8">
                <a href="{{ url('/') }}" class="logo text-6xl font-bold text-gradient">
                    {{ config('app.name') }}
                </a>
            </div>

            <!-- Error Card -->
            <div class="card bg-white dark:bg-gray-800 rounded-xl shadow-xl p-8 text-center">
                <div class="mb-6">
                    <div class="w-24 h-24 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">
                        {{ $code ?? '404' }}
                    </h1>
                    <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-4">
                        {{ $title ?? 'Page Not Found' }}
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        {{ $message ?? 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.' }}
                    </p>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ url('/') }}" class="btn btn-primary inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m0 0V11a1 1 0 011-1h2a1 1 0 011 1v10m0 0h3a1 1 0 001-1V10M9 21h6"></path>
                        </svg>
                        Back to Home
                    </a>
                    <button onclick="history.back()" class="btn btn-secondary inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Go Back
                    </button>
                </div>
            </div>

            <!-- Help Section -->
            <div class="mt-8 text-center">
                <p class="text-gray-500 dark:text-gray-400 text-sm mb-4">
                    Still having trouble? We're here to help.
                </p>
                <div class="flex justify-center space-x-4">
                    <a href="mailto:{{ config('app.APP_EMAIL') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-medium">
                        Contact Support
                    </a>
                    <a href="{{ config('app.HELPCENTER_URL') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-medium">
                        Help Center
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>