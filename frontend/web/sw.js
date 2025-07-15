// Mewayz PWA Service Worker
const CACHE_NAME = 'mewayz-pwa-v1';
const API_CACHE_NAME = 'mewayz-api-v1';
const STATIC_CACHE_NAME = 'mewayz-static-v1';
const OFFLINE_PAGE = '/offline.html';

// Static assets to cache
const STATIC_ASSETS = [
  '/',
  '/offline.html',
  '/manifest.json',
  '/icons/Icon-192.png',
  '/icons/Icon-512.png',
  '/flutter.js',
  '/main.dart.js',
  '/flutter_bootstrap.js'
];

// API endpoints to cache
const API_ENDPOINTS = [
  '/api/health',
  '/api/user/profile',
  '/api/workspaces',
  '/api/social-media/accounts',
  '/api/bio-sites',
  '/api/analytics/overview'
];

// Install event - cache static assets
self.addEventListener('install', event => {
  console.log('Mewayz PWA Service Worker Installing...');
  event.waitUntil(
    Promise.all([
      caches.open(STATIC_CACHE_NAME).then(cache => {
        return cache.addAll(STATIC_ASSETS);
      }),
      caches.open(CACHE_NAME).then(cache => {
        return cache.add(OFFLINE_PAGE);
      })
    ])
  );
  self.skipWaiting();
});

// Activate event - cleanup old caches
self.addEventListener('activate', event => {
  console.log('Mewayz PWA Service Worker Activating...');
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheName !== CACHE_NAME && 
              cacheName !== API_CACHE_NAME && 
              cacheName !== STATIC_CACHE_NAME) {
            console.log('Deleting old cache:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
  self.clients.claim();
});

// Fetch event - network-first for API, cache-first for static assets
self.addEventListener('fetch', event => {
  const { request } = event;
  const url = new URL(request.url);

  // Handle API requests with network-first strategy
  if (url.pathname.startsWith('/api/')) {
    event.respondWith(
      networkFirstStrategy(request, API_CACHE_NAME)
    );
    return;
  }

  // Handle static assets with cache-first strategy
  if (request.destination === 'document' || 
      request.destination === 'script' || 
      request.destination === 'style' ||
      request.destination === 'image') {
    event.respondWith(
      cacheFirstStrategy(request, STATIC_CACHE_NAME)
    );
    return;
  }

  // Default strategy for other requests
  event.respondWith(
    networkFirstStrategy(request, CACHE_NAME)
  );
});

// Network-first strategy with fallback to cache
async function networkFirstStrategy(request, cacheName) {
  try {
    const networkResponse = await fetch(request);
    
    // Clone response before consuming it
    const responseClone = networkResponse.clone();
    
    // Cache successful responses
    if (networkResponse.ok) {
      const cache = await caches.open(cacheName);
      await cache.put(request, responseClone);
    }
    
    return networkResponse;
  } catch (error) {
    console.log('Network failed, trying cache:', request.url);
    
    const cachedResponse = await caches.match(request);
    if (cachedResponse) {
      return cachedResponse;
    }
    
    // Return offline page for navigation requests
    if (request.destination === 'document') {
      return caches.match(OFFLINE_PAGE);
    }
    
    // Return error response for other requests
    return new Response('Network error', { status: 503 });
  }
}

// Cache-first strategy with network fallback
async function cacheFirstStrategy(request, cacheName) {
  const cachedResponse = await caches.match(request);
  
  if (cachedResponse) {
    return cachedResponse;
  }
  
  try {
    const networkResponse = await fetch(request);
    
    if (networkResponse.ok) {
      const cache = await caches.open(cacheName);
      await cache.put(request, networkResponse.clone());
    }
    
    return networkResponse;
  } catch (error) {
    console.log('Cache and network failed:', request.url);
    
    if (request.destination === 'document') {
      return caches.match(OFFLINE_PAGE);
    }
    
    return new Response('Resource not available', { status: 503 });
  }
}

// Background sync for offline actions
self.addEventListener('sync', event => {
  if (event.tag === 'background-sync') {
    event.waitUntil(doBackgroundSync());
  }
});

async function doBackgroundSync() {
  console.log('Performing background sync...');
  
  // Get pending offline actions from IndexedDB
  const pendingActions = await getPendingActions();
  
  for (const action of pendingActions) {
    try {
      await processOfflineAction(action);
      await removePendingAction(action.id);
    } catch (error) {
      console.log('Failed to sync action:', action, error);
    }
  }
}

// Push notification handling
self.addEventListener('push', event => {
  if (event.data) {
    const data = event.data.json();
    
    const options = {
      body: data.body,
      icon: '/icons/Icon-192.png',
      badge: '/icons/Icon-192.png',
      vibrate: [100, 50, 100],
      data: {
        url: data.url || '/',
        action: data.action
      },
      actions: [
        {
          action: 'open',
          title: 'Open',
          icon: '/icons/Icon-192.png'
        },
        {
          action: 'close',
          title: 'Close'
        }
      ]
    };
    
    event.waitUntil(
      self.registration.showNotification(data.title, options)
    );
  }
});

// Notification click handling
self.addEventListener('notificationclick', event => {
  event.notification.close();
  
  if (event.action === 'open' || !event.action) {
    const url = event.notification.data.url || '/';
    
    event.waitUntil(
      clients.matchAll({ type: 'window' }).then(clientList => {
        for (const client of clientList) {
          if (client.url === url && 'focus' in client) {
            return client.focus();
          }
        }
        
        if (clients.openWindow) {
          return clients.openWindow(url);
        }
      })
    );
  }
});

// Message handling from main thread
self.addEventListener('message', event => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
  
  if (event.data && event.data.type === 'CACHE_CLEAR') {
    clearAllCaches();
  }
  
  if (event.data && event.data.type === 'OFFLINE_ACTION') {
    storePendingAction(event.data.action);
  }
});

// Utility functions for IndexedDB operations
async function getPendingActions() {
  // Implementation for getting pending actions from IndexedDB
  return [];
}

async function removePendingAction(actionId) {
  // Implementation for removing action from IndexedDB
  console.log('Removing pending action:', actionId);
}

async function processOfflineAction(action) {
  // Implementation for processing offline actions
  console.log('Processing offline action:', action);
}

async function storePendingAction(action) {
  // Implementation for storing pending actions in IndexedDB
  console.log('Storing pending action:', action);
}

async function clearAllCaches() {
  const cacheNames = await caches.keys();
  await Promise.all(
    cacheNames.map(cacheName => caches.delete(cacheName))
  );
  console.log('All caches cleared');
}

console.log('Mewayz PWA Service Worker loaded successfully');