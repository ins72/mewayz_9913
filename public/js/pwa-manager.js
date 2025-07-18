// Mewayz Platform v2 - PWA Manager
class PWAManager {
    constructor() {
        this.swRegistration = null;
        this.isOnline = navigator.onLine;
        this.installPrompt = null;
        this.notificationPermission = 'default';
        
        this.init();
    }
    
    async init() {
        console.log('PWA Manager: Initializing...');
        
        // Register service worker
        await this.registerServiceWorker();
        
        // Set up event listeners
        this.setupEventListeners();
        
        // Check for app update
        this.checkForUpdate();
        
        // Initialize notifications
        this.initializeNotifications();
        
        // Handle install prompt
        this.handleInstallPrompt();
        
        // Set up background sync
        this.setupBackgroundSync();
        
        console.log('PWA Manager: Initialized successfully');
    }
    
    async registerServiceWorker() {
        if ('serviceWorker' in navigator) {
            try {
                const registration = await navigator.serviceWorker.register('/sw.js');
                this.swRegistration = registration;
                
                console.log('PWA Manager: Service worker registered:', registration.scope);
                
                // Listen for service worker updates
                registration.addEventListener('updatefound', () => {
                    console.log('PWA Manager: Service worker update found');
                    this.handleServiceWorkerUpdate(registration);
                });
                
                return registration;
            } catch (error) {
                console.error('PWA Manager: Service worker registration failed:', error);
                return null;
            }
        } else {
            console.warn('PWA Manager: Service worker not supported');
            return null;
        }
    }
    
    setupEventListeners() {
        // Online/offline events
        window.addEventListener('online', () => {
            this.isOnline = true;
            this.handleOnlineStatus(true);
        });
        
        window.addEventListener('offline', () => {
            this.isOnline = false;
            this.handleOnlineStatus(false);
        });
        
        // Visibility change (for background sync)
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden && this.isOnline) {
                this.syncOfflineData();
            }
        });
        
        // Before unload (save data)
        window.addEventListener('beforeunload', () => {
            this.saveCurrentState();
        });
    }
    
    handleServiceWorkerUpdate(registration) {
        const newWorker = registration.installing;
        
        newWorker.addEventListener('statechange', () => {
            if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                // New service worker is available
                this.showUpdateNotification();
            }
        });
    }
    
    showUpdateNotification() {
        const notification = document.createElement('div');
        notification.className = 'pwa-update-notification';
        notification.innerHTML = `
            <div class="flex items-center justify-between p-4 bg-info text-white rounded-lg shadow-lg">
                <div>
                    <h4 class="font-semibold">Update Available</h4>
                    <p class="text-sm">A new version of Mewayz is available.</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove(); window.pwaManager.updateApp()" 
                        class="px-4 py-2 bg-white text-info rounded hover:bg-gray-100">
                    Update
                </button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-remove after 10 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 10000);
    }
    
    updateApp() {
        if (this.swRegistration && this.swRegistration.waiting) {
            this.swRegistration.waiting.postMessage({ type: 'SKIP_WAITING' });
            window.location.reload();
        }
    }
    
    handleOnlineStatus(online) {
        const statusIndicator = document.querySelector('.network-status-indicator');
        
        if (statusIndicator) {
            if (online) {
                statusIndicator.classList.remove('offline');
                statusIndicator.classList.add('online');
                statusIndicator.textContent = 'Online';
            } else {
                statusIndicator.classList.remove('online');
                statusIndicator.classList.add('offline');
                statusIndicator.textContent = 'Offline';
            }
        }
        
        // Show notification
        this.showNetworkNotification(online);
        
        // Sync data if back online
        if (online) {
            this.syncOfflineData();
        }
    }
    
    showNetworkNotification(online) {
        const notification = document.createElement('div');
        notification.className = 'network-notification';
        notification.innerHTML = `
            <div class="flex items-center p-3 ${online ? 'bg-success' : 'bg-warning'} text-white rounded-lg shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    ${online ? 
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>' :
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'
                    }
                </svg>
                <span>${online ? 'Back online' : 'You are offline'}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-remove after 3 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 3000);
    }
    
    async initializeNotifications() {
        if ('Notification' in window) {
            this.notificationPermission = Notification.permission;
            
            if (this.notificationPermission === 'default') {
                this.showNotificationPermissionPrompt();
            } else if (this.notificationPermission === 'granted') {
                await this.setupPushNotifications();
            }
        }
    }
    
    showNotificationPermissionPrompt() {
        const prompt = document.createElement('div');
        prompt.className = 'notification-permission-prompt';
        prompt.innerHTML = `
            <div class="fixed top-4 right-4 max-w-sm p-4 bg-card-bg border border-border-color rounded-lg shadow-lg z-50">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-info mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-3.595-3.595a.908.908 0 00-1.28 0L15 17z"/>
                    </svg>
                    <div class="flex-1">
                        <h4 class="font-medium text-primary-text">Stay Updated</h4>
                        <p class="text-sm text-secondary-text mt-1">Enable notifications to receive important updates about your workspace.</p>
                        <div class="mt-3 flex space-x-2">
                            <button onclick="window.pwaManager.requestNotificationPermission()" 
                                    class="px-3 py-1 bg-info text-white rounded text-sm hover:bg-blue-600">
                                Allow
                            </button>
                            <button onclick="this.closest('.notification-permission-prompt').remove()" 
                                    class="px-3 py-1 bg-secondary-bg text-secondary-text rounded text-sm hover:bg-hover-bg">
                                Later
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(prompt);
    }
    
    async requestNotificationPermission() {
        const permission = await Notification.requestPermission();
        this.notificationPermission = permission;
        
        // Remove permission prompt
        const prompt = document.querySelector('.notification-permission-prompt');
        if (prompt) {
            prompt.remove();
        }
        
        if (permission === 'granted') {
            await this.setupPushNotifications();
            this.showNotification('Notifications Enabled', 'You will now receive important updates from Mewayz.');
        }
    }
    
    async setupPushNotifications() {
        if (this.swRegistration && 'pushManager' in this.swRegistration) {
            try {
                const subscription = await this.swRegistration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: this.urlB64ToUint8Array(window.vapidPublicKey || '')
                });
                
                // Send subscription to server
                await this.sendSubscriptionToServer(subscription);
                
                console.log('PWA Manager: Push notifications set up successfully');
            } catch (error) {
                console.error('PWA Manager: Error setting up push notifications:', error);
            }
        }
    }
    
    async sendSubscriptionToServer(subscription) {
        try {
            const response = await fetch('/api/push-notifications/subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    subscription: subscription,
                    user_agent: navigator.userAgent
                })
            });
            
            if (response.ok) {
                console.log('PWA Manager: Subscription sent to server');
            } else {
                console.error('PWA Manager: Failed to send subscription to server');
            }
        } catch (error) {
            console.error('PWA Manager: Error sending subscription to server:', error);
        }
    }
    
    showNotification(title, body, options = {}) {
        if (this.notificationPermission === 'granted') {
            const notification = new Notification(title, {
                body,
                icon: '/images/mewayz-icon-192.png',
                badge: '/images/mewayz-icon-192.png',
                ...options
            });
            
            notification.onclick = () => {
                window.focus();
                notification.close();
            };
            
            return notification;
        }
    }
    
    handleInstallPrompt() {
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            this.installPrompt = e;
            this.showInstallPrompt();
        });
        
        window.addEventListener('appinstalled', () => {
            console.log('PWA Manager: App installed');
            this.installPrompt = null;
            this.hideInstallPrompt();
        });
    }
    
    showInstallPrompt() {
        const prompt = document.createElement('div');
        prompt.className = 'install-prompt';
        prompt.innerHTML = `
            <div class="fixed bottom-4 left-4 right-4 md:left-auto md:right-4 md:max-w-sm p-4 bg-card-bg border border-border-color rounded-lg shadow-lg z-50">
                <div class="flex items-start">
                    <img src="/images/mewayz-icon-192.png" alt="Mewayz" class="w-12 h-12 rounded-lg mr-3">
                    <div class="flex-1">
                        <h4 class="font-medium text-primary-text">Install Mewayz</h4>
                        <p class="text-sm text-secondary-text mt-1">Add Mewayz to your home screen for quick access and a better experience.</p>
                        <div class="mt-3 flex space-x-2">
                            <button onclick="window.pwaManager.installApp()" 
                                    class="px-3 py-1 bg-info text-white rounded text-sm hover:bg-blue-600">
                                Install
                            </button>
                            <button onclick="this.closest('.install-prompt').remove()" 
                                    class="px-3 py-1 bg-secondary-bg text-secondary-text rounded text-sm hover:bg-hover-bg">
                                Later
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(prompt);
    }
    
    hideInstallPrompt() {
        const prompt = document.querySelector('.install-prompt');
        if (prompt) {
            prompt.remove();
        }
    }
    
    async installApp() {
        if (this.installPrompt) {
            this.installPrompt.prompt();
            const result = await this.installPrompt.userChoice;
            
            if (result.outcome === 'accepted') {
                console.log('PWA Manager: User accepted the install prompt');
            } else {
                console.log('PWA Manager: User dismissed the install prompt');
            }
            
            this.installPrompt = null;
        }
    }
    
    setupBackgroundSync() {
        if ('serviceWorker' in navigator && 'sync' in window.ServiceWorkerRegistration.prototype) {
            console.log('PWA Manager: Background sync is supported');
            
            // Register background sync
            navigator.serviceWorker.ready.then(registration => {
                return registration.sync.register('background-sync');
            }).catch(error => {
                console.error('PWA Manager: Background sync registration failed:', error);
            });
        } else {
            console.warn('PWA Manager: Background sync not supported');
        }
    }
    
    async syncOfflineData() {
        if (this.swRegistration && this.isOnline) {
            try {
                const offlineData = await this.getOfflineData();
                
                if (offlineData && offlineData.length > 0) {
                    // Send data to service worker for syncing
                    if (this.swRegistration.active) {
                        this.swRegistration.active.postMessage({
                            type: 'SYNC_DATA',
                            data: offlineData
                        });
                    }
                }
            } catch (error) {
                console.error('PWA Manager: Error syncing offline data:', error);
            }
        }
    }
    
    async getOfflineData() {
        try {
            const db = await this.openDB();
            const transaction = db.transaction(['offlineData'], 'readonly');
            const store = transaction.objectStore('offlineData');
            const data = await store.getAll();
            return data;
        } catch (error) {
            console.error('PWA Manager: Error getting offline data:', error);
            return [];
        }
    }
    
    async saveOfflineData(data) {
        try {
            const db = await this.openDB();
            const transaction = db.transaction(['offlineData'], 'readwrite');
            const store = transaction.objectStore('offlineData');
            
            const offlineItem = {
                id: Date.now() + Math.random(),
                url: data.url,
                method: data.method || 'POST',
                data: data.data,
                timestamp: new Date().toISOString()
            };
            
            await store.add(offlineItem);
            console.log('PWA Manager: Data saved for offline sync');
        } catch (error) {
            console.error('PWA Manager: Error saving offline data:', error);
        }
    }
    
    saveCurrentState() {
        // Save current form data, etc.
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);
            
            if (Object.keys(data).length > 0) {
                localStorage.setItem(`form_${form.id || 'unnamed'}`, JSON.stringify(data));
            }
        });
    }
    
    restoreState() {
        // Restore form data, etc.
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            const savedData = localStorage.getItem(`form_${form.id || 'unnamed'}`);
            
            if (savedData) {
                try {
                    const data = JSON.parse(savedData);
                    
                    Object.entries(data).forEach(([key, value]) => {
                        const input = form.querySelector(`[name="${key}"]`);
                        if (input) {
                            input.value = value;
                        }
                    });
                } catch (error) {
                    console.error('PWA Manager: Error restoring form state:', error);
                }
            }
        });
    }
    
    async openDB() {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open('MewayzOfflineDB', 1);
            
            request.onerror = () => reject(request.error);
            request.onsuccess = () => resolve(request.result);
            
            request.onupgradeneeded = event => {
                const db = event.target.result;
                
                if (!db.objectStoreNames.contains('offlineData')) {
                    const store = db.createObjectStore('offlineData', { keyPath: 'id' });
                    store.createIndex('timestamp', 'timestamp', { unique: false });
                }
            };
        });
    }
    
    urlB64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);
        
        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        
        return outputArray;
    }
    
    checkForUpdate() {
        if (this.swRegistration) {
            setInterval(() => {
                this.swRegistration.update();
            }, 60000); // Check every minute
        }
    }
}

// Initialize PWA Manager when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.pwaManager = new PWAManager();
});