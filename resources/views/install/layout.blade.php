<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Mewayz Installation')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900" rel="stylesheet" />
    
    <!-- Styles -->
    <style>
        :root {
            /* Installation Theme */
            --install-primary: #3B82F6;
            --install-secondary: #10B981;
            --install-accent: #8B5CF6;
            --install-warning: #F59E0B;
            --install-error: #EF4444;
            --install-success: #10B981;
            
            /* Base Colors */
            --bg-primary: #0F172A;
            --bg-secondary: #1E293B;
            --bg-tertiary: #334155;
            --text-primary: #F8FAFC;
            --text-secondary: #CBD5E1;
            --text-muted: #64748B;
            --border-primary: #334155;
            
            /* Shadows */
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
            background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-secondary) 100%);
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .install-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-secondary) 100%);
            z-index: -2;
        }

        .install-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 30% 20%, rgba(59, 130, 246, 0.1) 0%, transparent 50%),
                        radial-gradient(circle at 70% 80%, rgba(16, 185, 129, 0.1) 0%, transparent 50%);
            z-index: -1;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .install-header {
            padding: 2rem 0;
            text-align: center;
            margin-bottom: 2rem;
        }

        .install-logo {
            width: 4rem;
            height: 4rem;
            background: linear-gradient(135deg, var(--install-primary), var(--install-secondary));
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
            color: white;
            font-weight: 700;
            box-shadow: var(--shadow-lg);
        }

        .install-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .install-subtitle {
            font-size: 1.125rem;
            color: var(--text-secondary);
        }

        .install-progress {
            background: var(--bg-secondary);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid var(--border-primary);
            box-shadow: var(--shadow-md);
        }

        .progress-steps {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            position: relative;
        }

        .step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 1.5rem;
            right: -50%;
            width: 100%;
            height: 2px;
            background: var(--border-primary);
        }

        .step.completed:not(:last-child)::after {
            background: var(--install-success);
        }

        .step.active:not(:last-child)::after {
            background: linear-gradient(90deg, var(--install-primary) 50%, var(--border-primary) 50%);
        }

        .step-indicator {
            width: 3rem;
            height: 3rem;
            border-radius: 50%;
            border: 2px solid var(--border-primary);
            background: var(--bg-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }

        .step.active .step-indicator {
            background: var(--install-primary);
            border-color: var(--install-primary);
            color: white;
            transform: scale(1.1);
        }

        .step.completed .step-indicator {
            background: var(--install-success);
            border-color: var(--install-success);
            color: white;
        }

        .step-label {
            font-size: 0.75rem;
            color: var(--text-secondary);
            text-align: center;
            font-weight: 500;
        }

        .step.active .step-label {
            color: var(--install-primary);
        }

        .step.completed .step-label {
            color: var(--install-success);
        }

        .install-card {
            background: var(--bg-secondary);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: var(--shadow-xl);
            border: 1px solid var(--border-primary);
            margin-bottom: 2rem;
        }

        .card-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .card-subtitle {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-primary);
            border-radius: 8px;
            background: var(--bg-tertiary);
            color: var(--text-primary);
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--install-primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-select {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-primary);
            border-radius: 8px;
            background: var(--bg-tertiary);
            color: var(--text-primary);
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }

        .form-select:focus {
            outline: none;
            border-color: var(--install-primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
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
            background: var(--install-primary);
            color: white;
            border-color: var(--install-primary);
        }

        .btn-primary:hover {
            background: #2563eb;
            transform: translateY(-1px);
            box-shadow: var(--shadow-lg);
        }

        .btn-secondary {
            background: var(--bg-tertiary);
            color: var(--text-primary);
            border-color: var(--border-primary);
        }

        .btn-secondary:hover {
            background: var(--bg-primary);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .btn-success {
            background: var(--install-success);
            color: white;
            border-color: var(--install-success);
        }

        .btn-warning {
            background: var(--install-warning);
            color: white;
            border-color: var(--install-warning);
        }

        .btn-error {
            background: var(--install-error);
            color: white;
            border-color: var(--install-error);
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .btn-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border-primary);
        }

        .spinner {
            width: 1rem;
            height: 1rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            border: 1px solid;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border-color: var(--install-success);
            color: var(--install-success);
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border-color: var(--install-error);
            color: var(--install-error);
        }

        .alert-warning {
            background: rgba(245, 158, 11, 0.1);
            border-color: var(--install-warning);
            color: var(--install-warning);
        }

        .alert-info {
            background: rgba(59, 130, 246, 0.1);
            border-color: var(--install-primary);
            color: var(--install-primary);
        }

        .grid {
            display: grid;
            gap: 1.5rem;
        }

        .grid-cols-2 {
            grid-template-columns: repeat(2, 1fr);
        }

        .grid-cols-3 {
            grid-template-columns: repeat(3, 1fr);
        }

        .text-center {
            text-align: center;
        }

        .text-success {
            color: var(--install-success);
        }

        .text-error {
            color: var(--install-error);
        }

        .text-warning {
            color: var(--install-warning);
        }

        .text-muted {
            color: var(--text-muted);
        }

        .mb-2 {
            margin-bottom: 0.5rem;
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

        .p-4 {
            padding: 1rem;
        }

        .rounded-lg {
            border-radius: 8px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 0.75rem;
            }
            
            .install-card {
                padding: 1.5rem;
            }
            
            .install-title {
                font-size: 1.5rem;
            }
            
            .grid-cols-2 {
                grid-template-columns: 1fr;
            }
            
            .grid-cols-3 {
                grid-template-columns: 1fr;
            }
            
            .btn-actions {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body class="h-full">
    <div class="install-bg"></div>
    
    <div class="min-h-screen py-8">
        <div class="container">
            <div class="install-header">
                <div class="install-logo">M</div>
                <h1 class="install-title">Mewayz Installation</h1>
                <p class="install-subtitle">Set up your ultimate business platform</p>
            </div>
            
            @if(isset($installSteps) && isset($currentStep))
            <div class="install-progress">
                <div class="progress-steps">
                    @foreach($installSteps as $stepKey => $stepName)
                        @php
                            $stepIndex = array_search($stepKey, array_keys($installSteps));
                            $currentIndex = array_search($currentStep, array_keys($installSteps));
                            $isCompleted = $stepIndex < $currentIndex;
                            $isActive = $stepKey === $currentStep;
                        @endphp
                        
                        <div class="step {{ $isCompleted ? 'completed' : ($isActive ? 'active' : '') }}">
                            <div class="step-indicator">
                                @if($isCompleted)
                                    ✓
                                @else
                                    {{ $stepIndex + 1 }}
                                @endif
                            </div>
                            <div class="step-label">{{ $stepName }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
            
            <div class="install-card">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Set up CSRF token for all requests
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Global installer utilities
        window.installer = {
            showLoading: function(element, text = 'Processing...') {
                element.innerHTML = `
                    <div class="spinner"></div>
                    <span>${text}</span>
                `;
                element.disabled = true;
            },
            
            hideLoading: function(element, text = 'Continue') {
                element.innerHTML = text;
                element.disabled = false;
            },
            
            showAlert: function(message, type = 'info') {
                const alertDiv = document.createElement('div');
                alertDiv.className = `alert alert-${type}`;
                alertDiv.innerHTML = message;
                
                const container = document.querySelector('.install-card');
                container.insertBefore(alertDiv, container.firstChild);
                
                setTimeout(() => {
                    alertDiv.remove();
                }, 5000);
            },
            
            nextStep: function(step) {
                window.location.href = `/install/step/${step}`;
            },
            
            makeRequest: function(url, data = {}) {
                return fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
            }
        };
    </script>
    
    @stack('scripts')
</body>
</html>