// Mewayz Platform v2 - Advanced Service Worker
const CACHE_NAME = 'mewayz-v2-cache-v1';
const OFFLINE_URL = '/offline';
const NOTIFICATION_ICON = '/images/mewayz-icon-192.png';

// Cache strategies
const CACHE_STRATEGIES = {
    NETWORK_FIRST: 'network-first',
    CACHE_FIRST: 'cache-first',
    STALE_WHILE_REVALIDATE: 'stale-while-revalidate',
    NETWORK_ONLY: 'network-only',
    CACHE_ONLY: 'cache-only'
};

// Routes and their cache strategies
const ROUTE_CACHE_STRATEGIES = {
    '/': CACHE_STRATEGIES.NETWORK_FIRST,
    '/dashboard': CACHE_STRATEGIES.NETWORK_FIRST,
    '/api/health': CACHE_STRATEGIES.NETWORK_FIRST,
    '/api/dashboard/metrics': CACHE_STRATEGIES.STALE_WHILE_REVALIDATE,
    '/api/dashboard/activities': CACHE_STRATEGIES.STALE_WHILE_REVALIDATE,
    '/workspace-setup': CACHE_STRATEGIES.NETWORK_FIRST,
    '/offline': CACHE_STRATEGIES.CACHE_FIRST
};

// Assets to cache on install
const STATIC_ASSETS = [
    '/',
    '/offline',
    '/css/app.css',
    '/js/app.js',
    '/images/mewayz-logo.png',
    '/images/mewayz-icon-192.png',
    '/images/mewayz-icon-512.png',
    '/manifest.json'
];

// API endpoints to cache
const API_ENDPOINTS = [
    '/api/health',
    '/api/dashboard/metrics',
    '/api/user/profile',
    '/api/workspace/current'
];

// Install event - cache static assets
self.addEventListener('install', event => {
    console.log('SW: Installing service worker...');
    
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                console.log('SW: Caching static assets');
                return cache.addAll(STATIC_ASSETS);
            })
            .then(() => {
                console.log('SW: Static assets cached successfully');
                return self.skipWaiting();
            })
            .catch(error => {
                console.error('SW: Error caching static assets:', error);
            })
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', event => {
    console.log('SW: Activating service worker...');
    
    event.waitUntil(
        caches.keys()
            .then(cacheNames => {
                return Promise.all(
                    cacheNames.map(cacheName => {
                        if (cacheName !== CACHE_NAME) {
                            console.log('SW: Deleting old cache:', cacheName);
                            return caches.delete(cacheName);
                        }
                    })
                );
            })
            .then(() => {
                console.log('SW: Service worker activated');
                return self.clients.claim();
            })
    );
});

// Fetch event - handle network requests
self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);
    
    // Only handle same-origin requests
    if (url.origin !== location.origin) {
        return;
    }
    
    // Get cache strategy for this route
    const strategy = getCacheStrategy(url.pathname);
    
    event.respondWith(
        handleRequest(request, strategy)
    );
});

// Background sync event
self.addEventListener('sync', event => {
    console.log('SW: Background sync triggered:', event.tag);
    
    if (event.tag === 'background-sync') {
        event.waitUntil(performBackgroundSync());
    }
});

// Push notification event
self.addEventListener('push', event => {
    console.log('SW: Push notification received');
    
    let notificationData = {
        title: 'Mewayz Platform',
        body: 'You have a new notification',
        icon: NOTIFICATION_ICON,
        badge: NOTIFICATION_ICON,
        data: {}
    };
    
    if (event.data) {
        try {
            const pushData = event.data.json();
            notificationData = {
                ...notificationData,
                ...pushData
            };
        } catch (error) {
            console.error('SW: Error parsing push data:', error);
        }
    }
    
    event.waitUntil(
        self.registration.showNotification(notificationData.title, {
            body: notificationData.body,
            icon: notificationData.icon,
            badge: notificationData.badge,
            data: notificationData.data,
            requireInteraction: true,
            actions: [
                {
                    action: 'view',
                    title: 'View',
                    icon: '/images/view-icon.png'
                },
                {
                    action: 'dismiss',
                    title: 'Dismiss',
                    icon: '/images/dismiss-icon.png'
                }
            ]
        })
    );
});

// Notification click event
self.addEventListener('notificationclick', event => {
    console.log('SW: Notification clicked:', event.notification.data);
    
    event.notification.close();
    
    if (event.action === 'view') {
        event.waitUntil(
            clients.openWindow(event.notification.data.url || '/')
        );
    } else if (event.action === 'dismiss') {
        // Just close the notification
        return;
    } else {
        // Default click action
        event.waitUntil(
            clients.openWindow('/')
        );
    }
});

// Message event - handle messages from main thread
self.addEventListener('message', event => {
    console.log('SW: Message received:', event.data);
    
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
    
    if (event.data && event.data.type === 'CACHE_ASSETS') {
        event.waitUntil(
            cacheAssets(event.data.assets)
        );
    }
    
    if (event.data && event.data.type === 'SYNC_DATA') {
        event.waitUntil(
            syncOfflineData(event.data.data)
        );
    }
});

// Helper functions
function getCacheStrategy(pathname) {
    // Check for exact match first
    if (ROUTE_CACHE_STRATEGIES[pathname]) {
        return ROUTE_CACHE_STRATEGIES[pathname];
    }
    
    // Check for API routes
    if (pathname.startsWith('/api/')) {
        return CACHE_STRATEGIES.NETWORK_FIRST;
    }
    
    // Check for static assets
    if (pathname.match(/\.(css|js|png|jpg|jpeg|gif|svg|woff|woff2|ttf|eot)$/)) {
        return CACHE_STRATEGIES.CACHE_FIRST;
    }
    
    // Default strategy
    return CACHE_STRATEGIES.NETWORK_FIRST;
}

async function handleRequest(request, strategy) {
    try {
        switch (strategy) {
            case CACHE_STRATEGIES.NETWORK_FIRST:
                return await networkFirst(request);
            case CACHE_STRATEGIES.CACHE_FIRST:
                return await cacheFirst(request);
            case CACHE_STRATEGIES.STALE_WHILE_REVALIDATE:
                return await staleWhileRevalidate(request);
            case CACHE_STRATEGIES.NETWORK_ONLY:
                return await fetch(request);
            case CACHE_STRATEGIES.CACHE_ONLY:
                return await caches.match(request);
            default:
                return await networkFirst(request);
        }
    } catch (error) {
        console.error('SW: Error handling request:', error);
        return await handleOfflineRequest(request);
    }
}

async function networkFirst(request) {
    try {
        const response = await fetch(request);
        
        if (response.ok) {
            const cache = await caches.open(CACHE_NAME);
            cache.put(request, response.clone());
        }
        
        return response;
    } catch (error) {
        console.log('SW: Network failed, trying cache:', request.url);
        return await caches.match(request) || await handleOfflineRequest(request);
    }
}

async function cacheFirst(request) {
    const cachedResponse = await caches.match(request);
    
    if (cachedResponse) {
        return cachedResponse;
    }
    
    try {
        const response = await fetch(request);
        
        if (response.ok) {
            const cache = await caches.open(CACHE_NAME);
            cache.put(request, response.clone());
        }
        
        return response;
    } catch (error) {
        return await handleOfflineRequest(request);
    }
}

async function staleWhileRevalidate(request) {
    const cachedResponse = await caches.match(request);
    
    const fetchPromise = fetch(request).then(response => {
        if (response.ok) {
            const cache = caches.open(CACHE_NAME);
            cache.then(c => c.put(request, response.clone()));
        }
        return response;
    }).catch(error => {
        console.log('SW: Network error in stale-while-revalidate:', error);
        return null;
    });
    
    return cachedResponse || fetchPromise;
}

async function handleOfflineRequest(request) {
    const url = new URL(request.url);
    
    // If it's a navigation request, serve the offline page
    if (request.mode === 'navigate') {
        return await caches.match(OFFLINE_URL) || new Response('Offline', { status: 200 });
    }
    
    // If it's an API request, return offline data if available
    if (url.pathname.startsWith('/api/')) {
        const offlineData = await getOfflineData(url.pathname);
        if (offlineData) {
            return new Response(JSON.stringify(offlineData), {
                headers: { 'Content-Type': 'application/json' }
            });
        }
    }
    
    // Default offline response
    return new Response('Offline', { status: 503 });
}

async function cacheAssets(assets) {
    const cache = await caches.open(CACHE_NAME);
    
    for (const asset of assets) {
        try {
            await cache.add(asset);
            console.log('SW: Cached asset:', asset);
        } catch (error) {
            console.error('SW: Error caching asset:', asset, error);
        }
    }
}

async function performBackgroundSync() {
    console.log('SW: Performing background sync...');
    
    try {
        // Get offline data from IndexedDB
        const offlineData = await getOfflineDataFromDB();
        
        if (offlineData && offlineData.length > 0) {
            // Sync offline data with server
            for (const item of offlineData) {
                await syncOfflineItem(item);
            }
            
            // Clear offline data after successful sync
            await clearOfflineDataFromDB();
        }
        
        console.log('SW: Background sync completed');
    } catch (error) {
        console.error('SW: Background sync failed:', error);
    }
}

async function syncOfflineData(data) {
    console.log('SW: Syncing offline data...');
    
    try {
        const response = await fetch('/api/sync/offline-data', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Service-Worker': 'true'
            },
            body: JSON.stringify(data)
        });
        
        if (response.ok) {
            console.log('SW: Offline data synced successfully');
            return true;
        } else {
            console.error('SW: Failed to sync offline data:', response.status);
            return false;
        }
    } catch (error) {
        console.error('SW: Error syncing offline data:', error);
        return false;
    }
}

async function syncOfflineItem(item) {
    try {
        const response = await fetch(item.url, {
            method: item.method || 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Service-Worker': 'true'
            },
            body: JSON.stringify(item.data)
        });
        
        if (response.ok) {
            console.log('SW: Offline item synced:', item.id);
        } else {
            console.error('SW: Failed to sync offline item:', item.id);
        }
    } catch (error) {
        console.error('SW: Error syncing offline item:', error);
        // Re-queue for later sync
        await saveOfflineDataToDB(item);
    }
}

async function getOfflineData(endpoint) {
    // Get offline data from IndexedDB for specific endpoint
    try {
        const db = await openDB();
        const transaction = db.transaction(['offlineData'], 'readonly');
        const store = transaction.objectStore('offlineData');
        const data = await store.get(endpoint);
        return data ? data.value : null;
    } catch (error) {
        console.error('SW: Error getting offline data:', error);
        return null;
    }
}

async function getOfflineDataFromDB() {
    try {
        const db = await openDB();
        const transaction = db.transaction(['offlineData'], 'readonly');
        const store = transaction.objectStore('offlineData');
        const data = await store.getAll();
        return data;
    } catch (error) {
        console.error('SW: Error getting offline data from DB:', error);
        return [];
    }
}

async function saveOfflineDataToDB(data) {
    try {
        const db = await openDB();
        const transaction = db.transaction(['offlineData'], 'readwrite');
        const store = transaction.objectStore('offlineData');
        await store.put(data);
    } catch (error) {
        console.error('SW: Error saving offline data to DB:', error);
    }
}

async function clearOfflineDataFromDB() {
    try {
        const db = await openDB();
        const transaction = db.transaction(['offlineData'], 'readwrite');
        const store = transaction.objectStore('offlineData');
        await store.clear();
    } catch (error) {
        console.error('SW: Error clearing offline data from DB:', error);
    }
}

async function openDB() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open('MewayzOfflineDB', 1);
        
        request.onerror = () => reject(request.error);
        request.onsuccess = () => resolve(request.result);
        
        request.onupgradeneeded = event => {
            const db = event.target.result;
            
            if (!db.objectStoreNames.contains('offlineData')) {
                const store = db.createObjectStore('offlineData', { keyPath: 'id' });
                store.createIndex('endpoint', 'endpoint', { unique: false });
                store.createIndex('timestamp', 'timestamp', { unique: false });
            }
        };
    });
}

console.log('SW: Service worker loaded successfully');