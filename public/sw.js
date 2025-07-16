// Mewayz PWA Service Worker
// Version: 1.0.0
// Last Updated: January 2025

const CACHE_NAME = 'mewayz-pwa-v1.0.0';
const STATIC_CACHE_NAME = 'mewayz-static-v1.0.0';
const DYNAMIC_CACHE_NAME = 'mewayz-dynamic-v1.0.0';

// Files to cache immediately
const STATIC_FILES = [
  '/',
  '/login',
  '/register',
  '/dashboard',
  '/dashboard/instagram',
  '/dashboard/email',
  '/dashboard/analytics',
  '/dashboard/crm',
  '/dashboard/courses',
  '/dashboard/store',
  '/dashboard/bio-sites',
  '/dashboard/team',
  '/offline',
  '/build/assets/app.css',
  '/build/assets/app.js',
  '/images/icon-192x192.png',
  '/images/icon-512x512.png',
  '/manifest.json'
];

// API endpoints to cache
const API_CACHE_PATTERNS = [
  '/api/auth/me',
  '/api/workspace-setup/status',
  '/api/analytics',
  '/api/ai/services',
  '/api/oauth/providers'
];

// Files that should always be fetched from network
const NETWORK_ONLY = [
  '/api/auth/login',
  '/api/auth/register',
  '/api/auth/logout',
  '/api/payments/',
  '/api/webhook/'
];

// Install event - cache static files
self.addEventListener('install', (event) => {
  console.log('🚀 Service Worker installing...');
  
  event.waitUntil(
    caches.open(STATIC_CACHE_NAME)
      .then((cache) => {
        console.log('📦 Caching static files...');
        return cache.addAll(STATIC_FILES);
      })
      .then(() => {
        console.log('✅ Static files cached successfully');
        self.skipWaiting();
      })
      .catch((error) => {
        console.error('❌ Failed to cache static files:', error);
      })
  );
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
  console.log('🔄 Service Worker activating...');
  
  event.waitUntil(
    caches.keys()
      .then((cacheNames) => {
        return Promise.all(
          cacheNames
            .filter((cacheName) => {
              return cacheName !== STATIC_CACHE_NAME && 
                     cacheName !== DYNAMIC_CACHE_NAME &&
                     cacheName !== CACHE_NAME;
            })
            .map((cacheName) => {
              console.log('🗑️ Deleting old cache:', cacheName);
              return caches.delete(cacheName);
            })
        );
      })
      .then(() => {
        console.log('✅ Service Worker activated');
        self.clients.claim();
      })
  );
});

// Fetch event - handle network requests
self.addEventListener('fetch', (event) => {
  const { request } = event;
  const url = new URL(request.url);
  
  // Skip non-http requests
  if (!request.url.startsWith('http')) {
    return;
  }
  
  // Network-only requests
  if (NETWORK_ONLY.some(pattern => request.url.includes(pattern))) {
    event.respondWith(fetch(request));
    return;
  }
  
  // Handle different request types
  if (request.method === 'GET') {
    if (url.pathname.startsWith('/api/')) {
      // API requests - network first, then cache
      event.respondWith(networkFirstStrategy(request));
    } else if (url.pathname.startsWith('/build/') || 
               url.pathname.startsWith('/images/') ||
               url.pathname.endsWith('.css') ||
               url.pathname.endsWith('.js')) {
      // Static assets - cache first
      event.respondWith(cacheFirstStrategy(request));
    } else {
      // HTML pages - network first, then cache
      event.respondWith(networkFirstStrategy(request));
    }
  } else {
    // Non-GET requests - network only
    event.respondWith(fetch(request));
  }
});

// Network First Strategy - for dynamic content
async function networkFirstStrategy(request) {
  try {
    const networkResponse = await fetch(request);
    
    // If network request is successful, cache it
    if (networkResponse.ok) {
      const cache = await caches.open(DYNAMIC_CACHE_NAME);
      cache.put(request, networkResponse.clone());
    }
    
    return networkResponse;
  } catch (error) {
    // Network failed, try cache
    const cachedResponse = await caches.match(request);
    if (cachedResponse) {
      return cachedResponse;
    }
    
    // If it's a page request and no cache, return offline page
    if (request.destination === 'document') {
      return caches.match('/offline');
    }
    
    throw error;
  }
}

// Cache First Strategy - for static assets
async function cacheFirstStrategy(request) {
  const cachedResponse = await caches.match(request);
  
  if (cachedResponse) {
    return cachedResponse;
  }
  
  try {
    const networkResponse = await fetch(request);
    
    if (networkResponse.ok) {
      const cache = await caches.open(STATIC_CACHE_NAME);
      cache.put(request, networkResponse.clone());
    }
    
    return networkResponse;
  } catch (error) {
    console.error('Failed to fetch:', request.url, error);
    throw error;
  }
}

// Background sync for offline actions
self.addEventListener('sync', (event) => {
  console.log('🔄 Background sync triggered:', event.tag);
  
  if (event.tag === 'background-sync') {
    event.waitUntil(doBackgroundSync());
  }
});

async function doBackgroundSync() {
  console.log('📡 Performing background sync...');
  
  try {
    // Get pending actions from IndexedDB
    const pendingActions = await getPendingActions();
    
    for (const action of pendingActions) {
      try {
        await fetch(action.url, {
          method: action.method,
          headers: action.headers,
          body: action.body
        });
        
        // Remove from pending actions
        await removePendingAction(action.id);
        console.log('✅ Synced action:', action.id);
      } catch (error) {
        console.error('❌ Failed to sync action:', action.id, error);
      }
    }
  } catch (error) {
    console.error('❌ Background sync failed:', error);
  }
}

// Push notification handling
self.addEventListener('push', (event) => {
  console.log('📬 Push notification received');
  
  const options = {
    body: 'You have new updates in your Mewayz dashboard',
    icon: '/images/icon-192x192.png',
    badge: '/images/badge-72x72.png',
    vibrate: [100, 50, 100],
    data: {
      dateOfArrival: Date.now(),
      primaryKey: 1
    },
    actions: [
      {
        action: 'explore',
        title: 'View Dashboard',
        icon: '/images/checkmark.png'
      },
      {
        action: 'close',
        title: 'Close',
        icon: '/images/xmark.png'
      }
    ]
  };
  
  if (event.data) {
    const data = event.data.json();
    options.body = data.body || options.body;
    options.title = data.title || 'Mewayz';
    options.data = { ...options.data, ...data };
  }
  
  event.waitUntil(
    self.registration.showNotification('Mewayz', options)
  );
});

// Notification click handling
self.addEventListener('notificationclick', (event) => {
  console.log('🔔 Notification clicked:', event.action);
  
  event.notification.close();
  
  if (event.action === 'explore') {
    event.waitUntil(
      clients.openWindow('/dashboard')
    );
  } else if (event.action === 'close') {
    // Just close the notification
    return;
  } else {
    // Default action - open the app
    event.waitUntil(
      clients.openWindow('/')
    );
  }
});

// Message handling for communication with main thread
self.addEventListener('message', (event) => {
  console.log('📨 Message received:', event.data);
  
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
  
  if (event.data && event.data.type === 'GET_VERSION') {
    event.ports[0].postMessage({
      type: 'VERSION',
      version: CACHE_NAME
    });
  }
});

// Utility functions for IndexedDB operations
async function getPendingActions() {
  // This would typically use IndexedDB
  // For now, return empty array
  return [];
}

async function removePendingAction(id) {
  // This would typically remove from IndexedDB
  console.log('Removing pending action:', id);
}

// Error handling
self.addEventListener('error', (event) => {
  console.error('❌ Service Worker error:', event.error);
});

self.addEventListener('unhandledrejection', (event) => {
  console.error('❌ Unhandled promise rejection:', event.reason);
});

console.log('🚀 Mewayz Service Worker loaded successfully');