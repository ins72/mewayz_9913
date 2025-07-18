<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Mewayz Installation')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        .install-progress {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .install-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .step-indicator {
            transition: all 0.3s ease;
        }
        .step-indicator.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .step-indicator.completed {
            background: linear-gradient(135deg, #4ade80 0%, #22c55e 100%);
            color: white;
        }
        .spinner {
            border: 2px solid #f3f3f3;
            border-top: 2px solid #667eea;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .install-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }
        .install-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="install-bg"></div>
    
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-white bg-opacity-10 backdrop-blur-md border-b border-white border-opacity-20">
            <div class="max-w-4xl mx-auto px-4 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h1 class="text-2xl font-bold text-white">Mewayz</h1>
                    </div>
                    <div class="text-sm text-white opacity-75">
                        Installation Wizard
                    </div>
                </div>
            </div>
        </header>

        <!-- Progress Bar -->
        @if(isset($installSteps) && isset($currentStep))
        <div class="bg-white bg-opacity-10 backdrop-blur-md border-b border-white border-opacity-20">
            <div class="max-w-4xl mx-auto px-4 py-4">
                <div class="flex items-center justify-between">
                    @foreach($installSteps as $stepKey => $stepName)
                        @php
                            $stepIndex = array_search($stepKey, array_keys($installSteps));
                            $currentIndex = array_search($currentStep, array_keys($installSteps));
                            $isCompleted = $stepIndex < $currentIndex;
                            $isActive = $stepKey === $currentStep;
                        @endphp
                        
                        <div class="flex items-center {{ !$loop->last ? 'flex-1' : '' }}">
                            <div class="step-indicator w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium border-2 border-white 
                                {{ $isCompleted ? 'completed' : ($isActive ? 'active' : 'bg-white text-gray-400') }}">
                                @if($isCompleted)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                @else
                                    {{ $stepIndex + 1 }}
                                @endif
                            </div>
                            <div class="ml-2 text-sm font-medium text-white opacity-75">
                                {{ $stepName }}
                            </div>
                            @if(!$loop->last)
                                <div class="flex-1 h-0.5 bg-white bg-opacity-20 mx-4"></div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Main Content -->
        <main class="flex-1 flex items-center justify-center p-4">
            <div class="w-full max-w-4xl">
                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white bg-opacity-10 backdrop-blur-md border-t border-white border-opacity-20">
            <div class="max-w-4xl mx-auto px-4 py-4">
                <div class="flex items-center justify-between text-sm text-white opacity-75">
                    <div>© 2025 Mewayz. All rights reserved.</div>
                    <div>Version 2.0.0</div>
                </div>
            </div>
        </footer>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        // Set up CSRF token for all requests
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Global installer utilities
        window.installer = {
            showLoading: function(element, text = 'Processing...') {
                element.innerHTML = `
                    <div class="flex items-center justify-center">
                        <div class="spinner mr-2"></div>
                        <span>${text}</span>
                    </div>
                `;
                element.disabled = true;
            },
            
            hideLoading: function(element, text = 'Continue') {
                element.innerHTML = text;
                element.disabled = false;
            },
            
            showError: function(message) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
                errorDiv.innerHTML = message;
                document.body.appendChild(errorDiv);
                
                setTimeout(() => {
                    errorDiv.remove();
                }, 5000);
            },
            
            showSuccess: function(message) {
                const successDiv = document.createElement('div');
                successDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
                successDiv.innerHTML = message;
                document.body.appendChild(successDiv);
                
                setTimeout(() => {
                    successDiv.remove();
                }, 5000);
            },
            
            nextStep: function(step) {
                window.location.href = `/install/step/${step}`;
            }
        };
    </script>
    
    @stack('scripts')
</body>
</html>