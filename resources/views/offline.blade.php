<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offline - Mewayz Platform</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        .offline-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #101010 0%, #1a1a1a 100%);
            padding: 20px;
        }
        
        .offline-content {
            text-align: center;
            max-width: 500px;
            padding: 40px;
            background: #191919;
            border-radius: 16px;
            border: 1px solid #282828;
            box-shadow: 0 20px 40px rgba(0,0,0,0.5);
        }
        
        .offline-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .offline-title {
            font-size: 24px;
            font-weight: 700;
            color: #F1F1F1;
            margin-bottom: 12px;
        }
        
        .offline-message {
            font-size: 16px;
            color: #7B7B7B;
            margin-bottom: 30px;
            line-height: 1.5;
        }
        
        .offline-features {
            background: #101010;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #282828;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            color: #F1F1F1;
        }
        
        .feature-item:last-child {
            margin-bottom: 0;
        }
        
        .feature-icon {
            width: 20px;
            height: 20px;
            margin-right: 12px;
            color: #10B981;
        }
        
        .retry-button {
            background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-right: 12px;
        }
        
        .retry-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
        }
        
        .cache-button {
            background: transparent;
            color: #7B7B7B;
            border: 1px solid #282828;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .cache-button:hover {
            background: #282828;
            color: #F1F1F1;
        }
        
        .network-status {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            z-index: 1000;
        }
        
        .network-status.offline {
            background: #EF4444;
            color: white;
        }
        
        .network-status.online {
            background: #10B981;
            color: white;
        }
        
        .loading-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid #3B82F6;
            border-top: 2px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="network-status offline">
        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m-2.829-2.829a5 5 0 010-7.07M12 12a1 1 0 10-2 0 1 1 0 002 0zm-3.536-3.536a5 5 0 017.07 0m-7.07 7.07a5 5 0 007.07 0M5.636 5.636a9 9 0 0012.728 0"/>
        </svg>
        Offline
    </div>

    <div class="offline-container">
        <div class="offline-content">
            <div class="offline-icon">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m-2.829-2.829a5 5 0 010-7.07M12 12a1 1 0 10-2 0 1 1 0 002 0zm-3.536-3.536a5 5 0 017.07 0m-7.07 7.07a5 5 0 007.07 0M5.636 5.636a9 9 0 0012.728 0"/>
                </svg>
            </div>
            
            <h1 class="offline-title">You're Offline</h1>
            <p class="offline-message">
                Don't worry! You can still access some features while offline. Your data will sync automatically when you're back online.
            </p>
            
            <div class="offline-features">
                <div class="feature-item">
                    <svg class="feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>View cached dashboard data</span>
                </div>
                <div class="feature-item">
                    <svg class="feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Create and edit content</span>
                </div>
                <div class="feature-item">
                    <svg class="feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Access saved templates</span>
                </div>
                <div class="feature-item">
                    <svg class="feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Review analytics data</span>
                </div>
            </div>
            
            <div class="flex items-center justify-center">
                <button class="retry-button" onclick="retryConnection()">
                    <div class="loading-spinner"></div>
                    <span>Try Again</span>
                </button>
                <button class="cache-button" onclick="viewCachedData()">
                    View Cached Data
                </button>
            </div>
        </div>
    </div>

    <script>
        // Network status monitoring
        function updateNetworkStatus() {
            const statusElement = document.querySelector('.network-status');
            const isOnline = navigator.onLine;
            
            if (isOnline) {
                statusElement.className = 'network-status online';
                statusElement.innerHTML = `
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Online
                `;
                
                // Automatically redirect when back online
                setTimeout(() => {
                    window.location.href = '/dashboard';
                }, 2000);
            } else {
                statusElement.className = 'network-status offline';
                statusElement.innerHTML = `
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m-2.829-2.829a5 5 0 010-7.07M12 12a1 1 0 10-2 0 1 1 0 002 0zm-3.536-3.536a5 5 0 017.07 0m-7.07 7.07a5 5 0 007.07 0M5.636 5.636a9 9 0 0012.728 0"/>
                    </svg>
                    Offline
                `;
            }
        }
        
        // Retry connection
        function retryConnection() {
            const button = document.querySelector('.retry-button');
            const spinner = button.querySelector('.loading-spinner');
            const text = button.querySelector('span');
            
            spinner.style.display = 'inline-block';
            text.textContent = 'Checking...';
            button.disabled = true;
            
            // Check connection
            fetch('/api/health', { 
                method: 'GET',
                cache: 'no-cache'
            })
            .then(response => {
                if (response.ok) {
                    text.textContent = 'Connected!';
                    window.location.href = '/dashboard';
                } else {
                    throw new Error('Connection failed');
                }
            })
            .catch(error => {
                spinner.style.display = 'none';
                text.textContent = 'Still Offline';
                button.disabled = false;
                
                setTimeout(() => {
                    text.textContent = 'Try Again';
                }, 2000);
            });
        }
        
        // View cached data
        function viewCachedData() {
            // Try to access cached dashboard
            window.location.href = '/dashboard';
        }
        
        // Event listeners
        window.addEventListener('online', updateNetworkStatus);
        window.addEventListener('offline', updateNetworkStatus);
        
        // Initial status check
        updateNetworkStatus();
        
        // Periodic connection check
        setInterval(() => {
            if (navigator.onLine) {
                fetch('/api/health', { 
                    method: 'GET',
                    cache: 'no-cache'
                })
                .then(response => {
                    if (response.ok) {
                        window.location.href = '/dashboard';
                    }
                })
                .catch(error => {
                    console.log('Still offline');
                });
            }
        }, 30000); // Check every 30 seconds
    </script>
</body>
</html>