// Mewayz Platform Service Worker
// Version 3.0.0 - July 20, 2025
// PWA functionality for offline support and caching

const CACHE_NAME = 'mewayz-v3.0.0';
const API_CACHE_NAME = 'mewayz-api-v3.0.0';

// Files to cache for offline functionality
const STATIC_ASSETS = [
  '/',
  '/dashboard',
  '/login',
  '/register',
  '/manifest.json',
  '/static/js/bundle.js',
  '/static/css/main.css',
  '/icons/icon-192x192.png',
  '/icons/icon-512x512.png'
];

// API endpoints to cache
const API_CACHE_PATTERNS = [
  /^.*\/api\/auth\/profile$/,
  /^.*\/api\/workspaces$/,
  /^.*\/api\/dashboard\/stats$/,
  /^.*\/api\/integrations\/available$/
];

// Install event - cache static assets
self.addEventListener('install', (event) => {
  console.log('[SW] Installing service worker v3.0.0');
  
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => {
        console.log('[SW] Caching static assets');
        return cache.addAll(STATIC_ASSETS);
      })
      .then(() => {
        console.log('[SW] Skip waiting');
        return self.skipWaiting();
      })
      .catch((error) => {
        console.error('[SW] Failed to cache static assets:', error);
      })
  );
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
  console.log('[SW] Activating service worker v3.0.0');
  
  event.waitUntil(
    Promise.all([
      // Delete old caches
      caches.keys().then((cacheNames) => {
        return Promise.all(
          cacheNames.map((cacheName) => {
            if (cacheName !== CACHE_NAME && cacheName !== API_CACHE_NAME) {
              console.log('[SW] Deleting old cache:', cacheName);
              return caches.delete(cacheName);
            }
          })
        );
      }),
      // Take control of all pages
      self.clients.claim()
    ])
  );
});

// Fetch event - network first with cache fallback
self.addEventListener('fetch', (event) => {
  const { request } = event;
  const url = new URL(request.url);

  // Skip non-GET requests
  if (request.method !== 'GET') {
    return;
  }

  // Skip chrome-extension requests
  if (url.protocol === 'chrome-extension:') {
    return;
  }

  // Handle API requests
  if (url.pathname.startsWith('/api/')) {
    event.respondWith(handleApiRequest(request));
    return;
  }

  // Handle static assets and pages
  event.respondWith(handleStaticRequest(request));
});

// Handle API requests with network-first strategy
async function handleApiRequest(request) {
  const url = new URL(request.url);
  
  // Check if this API should be cached
  const shouldCache = API_CACHE_PATTERNS.some(pattern => pattern.test(url.href));
  
  if (!shouldCache) {
    // For non-cacheable APIs, just fetch from network
    try {
      return await fetch(request);
    } catch (error) {
      console.error('[SW] API request failed:', error);
      return new Response(
        JSON.stringify({ error: 'Network unavailable', offline: true }),
        { 
          status: 503, 
          headers: { 'Content-Type': 'application/json' } 
        }
      );
    }
  }

  try {
    // Try network first
    const networkResponse = await fetch(request.clone());
    
    if (networkResponse.ok) {
      // Cache successful response
      const cache = await caches.open(API_CACHE_NAME);
      await cache.put(request.clone(), networkResponse.clone());
    }
    
    return networkResponse;
  } catch (error) {
    console.log('[SW] Network failed, trying cache for API:', request.url);
    
    // Fall back to cache
    const cachedResponse = await caches.match(request);
    if (cachedResponse) {
      // Add offline indicator to cached response
      const cachedData = await cachedResponse.json();
      return new Response(
        JSON.stringify({ ...cachedData, offline: true }),
        {
          status: cachedResponse.status,
          headers: { 'Content-Type': 'application/json' }
        }
      );
    }
    
    // No cache available
    return new Response(
      JSON.stringify({ error: 'No cached data available', offline: true }),
      { 
        status: 503, 
        headers: { 'Content-Type': 'application/json' } 
      }
    );
  }
}

// Handle static requests with cache-first strategy for assets, network-first for pages
async function handleStaticRequest(request) {
  const url = new URL(request.url);
  
  // For assets (JS, CSS, images), use cache-first
  if (url.pathname.includes('/static/') || 
      url.pathname.includes('/icons/') || 
      url.pathname.endsWith('.js') ||
      url.pathname.endsWith('.css') ||
      url.pathname.endsWith('.png') ||
      url.pathname.endsWith('.jpg') ||
      url.pathname.endsWith('.svg')) {
    
    const cachedResponse = await caches.match(request);
    if (cachedResponse) {
      return cachedResponse;
    }
    
    try {
      const networkResponse = await fetch(request);
      if (networkResponse.ok) {
        const cache = await caches.open(CACHE_NAME);
        await cache.put(request, networkResponse.clone());
      }
      return networkResponse;
    } catch (error) {
      console.error('[SW] Failed to fetch asset:', request.url, error);
      return new Response('Asset unavailable offline', { status: 503 });
    }
  }
  
  // For pages, use network-first with cache fallback
  try {
    const networkResponse = await fetch(request);
    
    if (networkResponse.ok) {
      // Cache successful page responses
      const cache = await caches.open(CACHE_NAME);
      await cache.put(request, networkResponse.clone());
    }
    
    return networkResponse;
  } catch (error) {
    console.log('[SW] Network failed, trying cache for page:', request.url);
    
    const cachedResponse = await caches.match(request);
    if (cachedResponse) {
      return cachedResponse;
    }
    
    // For SPA routing, fall back to cached index.html
    if (url.pathname.startsWith('/dashboard') || 
        url.pathname.startsWith('/login') || 
        url.pathname.startsWith('/register')) {
      const indexCache = await caches.match('/');
      if (indexCache) {
        return indexCache;
      }
    }
    
    // No cache available
    return new Response(
      `
      <!DOCTYPE html>
      <html>
        <head>
          <title>Mewayz - Offline</title>
          <meta name="viewport" content="width=device-width, initial-scale=1">
          <style>
            body { 
              font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
              background: #101010; 
              color: #f1f1f1; 
              text-align: center; 
              padding: 50px 20px;
              margin: 0;
            }
            .container { max-width: 400px; margin: 0 auto; }
            .icon { font-size: 60px; margin-bottom: 20px; }
            h1 { color: #3b82f6; margin-bottom: 10px; }
            p { color: #7b7b7b; line-height: 1.5; }
            button {
              background: #3b82f6;
              color: white;
              border: none;
              padding: 12px 24px;
              border-radius: 8px;
              font-size: 16px;
              cursor: pointer;
              margin-top: 20px;
            }
            button:hover { opacity: 0.9; }
          </style>
        </head>
        <body>
          <div class="container">
            <div class="icon">📱</div>
            <h1>Mewayz</h1>
            <h2>You're Offline</h2>
            <p>This page isn't available offline. Please check your internet connection and try again.</p>
            <button onclick="window.location.reload()">Try Again</button>
          </div>
        </body>
      </html>
      `,
      { 
        status: 503,
        headers: { 'Content-Type': 'text/html' }
      }
    );
  }
}

// Background sync for offline actions
self.addEventListener('sync', (event) => {
  console.log('[SW] Background sync:', event.tag);
  
  if (event.tag === 'background-sync-posts') {
    event.waitUntil(syncPendingPosts());
  }
  
  if (event.tag === 'background-sync-analytics') {
    event.waitUntil(syncPendingAnalytics());
  }
});

// Push notification handling
self.addEventListener('push', (event) => {
  console.log('[SW] Push notification received');
  
  if (!event.data) {
    return;
  }
  
  const data = event.data.json();
  const options = {
    body: data.body || 'New notification from Mewayz',
    icon: '/icons/icon-192x192.png',
    badge: '/icons/icon-96x96.png',
    image: data.image,
    data: data.data,
    actions: data.actions || [
      { action: 'view', title: 'View' },
      { action: 'dismiss', title: 'Dismiss' }
    ],
    requireInteraction: data.requireInteraction || false,
    silent: data.silent || false,
    tag: data.tag || 'mewayz-notification',
    timestamp: Date.now(),
    vibrate: data.vibrate || [200, 100, 200]
  };
  
  event.waitUntil(
    self.registration.showNotification(data.title || 'Mewayz', options)
  );
});

// Notification click handling
self.addEventListener('notificationclick', (event) => {
  console.log('[SW] Notification click:', event.action);
  
  event.notification.close();
  
  const action = event.action;
  const data = event.notification.data;
  
  if (action === 'dismiss') {
    return;
  }
  
  let url = '/dashboard';
  
  if (data && data.url) {
    url = data.url;
  } else if (action === 'view' && data && data.type) {
    switch (data.type) {
      case 'social_post':
        url = '/dashboard/social-media';
        break;
      case 'course_enrollment':
        url = '/dashboard/courses';
        break;
      case 'new_order':
        url = '/dashboard/ecommerce';
        break;
      case 'analytics_report':
        url = '/dashboard/analytics';
        break;
      default:
        url = '/dashboard';
    }
  }
  
  event.waitUntil(
    clients.matchAll({ type: 'window' }).then((clientList) => {
      // If there's already a window open, focus it and navigate
      for (const client of clientList) {
        if (client.url.includes(self.location.origin)) {
          client.focus();
          return client.navigate(url);
        }
      }
      
      // Otherwise, open a new window
      return clients.openWindow(url);
    })
  );
});

// Sync pending posts when back online
async function syncPendingPosts() {
  console.log('[SW] Syncing pending posts...');
  
  try {
    // Get pending posts from IndexedDB or localStorage
    const pendingPosts = await getPendingPosts();
    
    for (const post of pendingPosts) {
      try {
        const response = await fetch('/api/social/posts', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${post.token}`
          },
          body: JSON.stringify(post.data)
        });
        
        if (response.ok) {
          await removePendingPost(post.id);
          console.log('[SW] Successfully synced post:', post.id);
        }
      } catch (error) {
        console.error('[SW] Failed to sync post:', post.id, error);
      }
    }
  } catch (error) {
    console.error('[SW] Failed to sync pending posts:', error);
  }
}

// Sync pending analytics when back online
async function syncPendingAnalytics() {
  console.log('[SW] Syncing pending analytics...');
  
  try {
    const pendingEvents = await getPendingAnalytics();
    
    for (const event of pendingEvents) {
      try {
        const response = await fetch('/api/analytics/events', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${event.token}`
          },
          body: JSON.stringify(event.data)
        });
        
        if (response.ok) {
          await removePendingAnalytics(event.id);
          console.log('[SW] Successfully synced analytics event:', event.id);
        }
      } catch (error) {
        console.error('[SW] Failed to sync analytics event:', event.id, error);
      }
    }
  } catch (error) {
    console.error('[SW] Failed to sync pending analytics:', error);
  }
}

// Utility functions for offline data management
async function getPendingPosts() {
  // In a real implementation, this would use IndexedDB
  const stored = localStorage.getItem('mewayz_pending_posts');
  return stored ? JSON.parse(stored) : [];
}

async function removePendingPost(postId) {
  const pending = await getPendingPosts();
  const filtered = pending.filter(p => p.id !== postId);
  localStorage.setItem('mewayz_pending_posts', JSON.stringify(filtered));
}

async function getPendingAnalytics() {
  const stored = localStorage.getItem('mewayz_pending_analytics');
  return stored ? JSON.parse(stored) : [];
}

async function removePendingAnalytics(eventId) {
  const pending = await getPendingAnalytics();
  const filtered = pending.filter(e => e.id !== eventId);
  localStorage.setItem('mewayz_pending_analytics', JSON.stringify(filtered));
}

// Message handling from the main thread
self.addEventListener('message', (event) => {
  console.log('[SW] Message received:', event.data);
  
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
  
  if (event.data && event.data.type === 'GET_VERSION') {
    event.ports[0].postMessage({ version: CACHE_NAME });
  }
});

// Error handling
self.addEventListener('error', (event) => {
  console.error('[SW] Service worker error:', event.error);
});

self.addEventListener('unhandledrejection', (event) => {
  console.error('[SW] Service worker unhandled rejection:', event.reason);
});

console.log('[SW] Mewayz Service Worker v3.0.0 loaded successfully');