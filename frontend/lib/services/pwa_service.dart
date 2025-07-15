import 'dart:html' as html;
import 'dart:js' as js;
import 'dart:js_util';
import 'dart:typed_data';
import 'package:flutter/foundation.dart';

class PWAService {
  static final PWAService _instance = PWAService._internal();
  factory PWAService() => _instance;
  PWAService._internal();

  bool _isServiceWorkerSupported = false;
  bool _isServiceWorkerRegistered = false;
  bool _isNotificationSupported = false;
  bool _isNotificationPermissionGranted = false;
  html.ServiceWorkerRegistration? _registration;

  // Initialize PWA features
  Future<void> initialize() async {
    if (!kIsWeb) return;

    try {
      // Check service worker support
      _isServiceWorkerSupported = html.window.navigator.serviceWorker != null;
      
      if (_isServiceWorkerSupported) {
        await _registerServiceWorker();
      }

      // Check notification support
      _isNotificationSupported = html.Notification.supported;
      
      if (_isNotificationSupported) {
        await _requestNotificationPermission();
      }

      // Setup online/offline listeners
      _setupConnectivityListeners();
      
      // Initialize background sync
      _setupBackgroundSync();

      print('PWA Service initialized successfully');
    } catch (e) {
      print('Error initializing PWA Service: $e');
    }
  }

  // Register service worker
  Future<void> _registerServiceWorker() async {
    try {
      final registration = await html.window.navigator.serviceWorker!.register('/sw.js');
      _registration = registration;
      _isServiceWorkerRegistered = true;
      
      print('Service Worker registered successfully');
      
      // Listen for updates
      registration.addEventListener('updatefound', (event) {
        print('Service Worker update found');
        _handleServiceWorkerUpdate();
      });
      
      // Check for waiting service worker
      if (registration.waiting != null) {
        _showUpdateAvailableNotification();
      }
    } catch (e) {
      print('Service Worker registration failed: $e');
    }
  }

  // Handle service worker updates
  void _handleServiceWorkerUpdate() {
    if (_registration?.waiting != null) {
      _showUpdateAvailableNotification();
    }
  }

  // Show update available notification
  void _showUpdateAvailableNotification() {
    // You can implement a custom UI notification here
    print('App update available. Restart to get the latest version.');
  }

  // Skip waiting and activate new service worker
  void skipWaiting() {
    if (_registration?.waiting != null) {
      _registration!.waiting!.postMessage({'type': 'SKIP_WAITING'});
    }
  }

  // Request notification permission
  Future<void> _requestNotificationPermission() async {
    try {
      final permission = await html.Notification.requestPermission();
      _isNotificationPermissionGranted = permission == 'granted';
      
      if (_isNotificationPermissionGranted) {
        print('Notification permission granted');
      } else {
        print('Notification permission denied');
      }
    } catch (e) {
      print('Error requesting notification permission: $e');
    }
  }

  // Show local notification
  Future<void> showNotification({
    required String title,
    required String body,
    String? icon,
    String? tag,
    String? url,
    Map<String, dynamic>? data,
  }) async {
    if (!_isNotificationSupported || !_isNotificationPermissionGranted) {
      print('Notifications not supported or permission not granted');
      return;
    }

    try {
      final options = {
        'body': body,
        'icon': icon ?? '/icons/Icon-192.png',
        'badge': '/icons/Icon-192.png',
        'tag': tag ?? 'mewayz-notification',
        'requireInteraction': true,
        'vibrate': [100, 50, 100],
        'data': data ?? {},
      };

      final notification = html.Notification(title, options);
      
      notification.onClick.listen((event) {
        notification.close();
        if (url != null) {
          html.window.open(url, '_blank');
        }
      });

      // Auto-close after 5 seconds
      Future.delayed(Duration(seconds: 5), () {
        notification.close();
      });
      
      print('Local notification shown: $title');
    } catch (e) {
      print('Error showing notification: $e');
    }
  }

  // Subscribe to push notifications
  Future<String?> subscribeToPushNotifications() async {
    if (!_isServiceWorkerRegistered || _registration == null) {
      print('Service worker not registered');
      return null;
    }

    try {
      final pushManager = _registration!.pushManager;
      final subscription = await pushManager.subscribe({
        'userVisibleOnly': true,
        'applicationServerKey': _urlBase64ToUint8Array(_getVapidPublicKey()),
      });

      final subscriptionData = {
        'endpoint': subscription.endpoint,
        'keys': {
          'p256dh': subscription.getKey('p256dh'),
          'auth': subscription.getKey('auth'),
        },
      };

      print('Push subscription created: $subscriptionData');
      return subscription.endpoint;
    } catch (e) {
      print('Error subscribing to push notifications: $e');
      return null;
    }
  }

  // Setup connectivity listeners
  void _setupConnectivityListeners() {
    html.window.addEventListener('online', (event) {
      print('App is online');
      _handleOnlineEvent();
    });

    html.window.addEventListener('offline', (event) {
      print('App is offline');
      _handleOfflineEvent();
    });
  }

  // Handle online event
  void _handleOnlineEvent() {
    // Sync pending offline actions
    _syncPendingActions();
    
    // Update UI to show online status
    _updateConnectivityStatus(true);
  }

  // Handle offline event
  void _handleOfflineEvent() {
    // Update UI to show offline status
    _updateConnectivityStatus(false);
  }

  // Update connectivity status in UI
  void _updateConnectivityStatus(bool isOnline) {
    // This can be implemented to update a global connectivity state
    print('Connectivity status: ${isOnline ? 'Online' : 'Offline'}');
  }

  // Setup background sync
  void _setupBackgroundSync() {
    if (!_isServiceWorkerRegistered) return;

    // Register for background sync
    js.context.callMethod('eval', ['''
      if ('serviceWorker' in navigator && 'sync' in window.ServiceWorkerRegistration.prototype) {
        navigator.serviceWorker.ready.then(function(registration) {
          registration.sync.register('background-sync');
        });
      }
    ''']);
  }

  // Add offline action for background sync
  void addOfflineAction(Map<String, dynamic> action) {
    if (!_isServiceWorkerRegistered) return;

    _registration?.active?.postMessage({
      'type': 'OFFLINE_ACTION',
      'action': action,
    });
  }

  // Sync pending actions
  void _syncPendingActions() {
    if (!_isServiceWorkerRegistered) return;

    // Trigger background sync
    js.context.callMethod('eval', ['''
      if ('serviceWorker' in navigator && 'sync' in window.ServiceWorkerRegistration.prototype) {
        navigator.serviceWorker.ready.then(function(registration) {
          registration.sync.register('background-sync');
        });
      }
    ''']);
  }

  // Clear all caches
  void clearAllCaches() {
    if (!_isServiceWorkerRegistered) return;

    _registration?.active?.postMessage({
      'type': 'CACHE_CLEAR',
    });
  }

  // Install app to home screen
  void installApp() {
    js.context.callMethod('eval', ['''
      if (window.deferredPrompt) {
        window.deferredPrompt.prompt();
        window.deferredPrompt.userChoice.then(function(choiceResult) {
          if (choiceResult.outcome === 'accepted') {
            console.log('User accepted the install prompt');
          } else {
            console.log('User dismissed the install prompt');
          }
        });
      }
    ''']);
  }

  // Check if app is installed
  bool get isAppInstalled {
    return js.context.hasProperty('matchMedia') && 
           js.context.callMethod('matchMedia', ['(display-mode: standalone)']);
  }

  // Get connectivity status
  bool get isOnline => html.window.navigator.onLine ?? true;

  // Get service worker status
  bool get isServiceWorkerSupported => _isServiceWorkerSupported;
  bool get isServiceWorkerRegistered => _isServiceWorkerRegistered;
  bool get isNotificationSupported => _isNotificationSupported;
  bool get isNotificationPermissionGranted => _isNotificationPermissionGranted;

  // Utility functions
  String _getVapidPublicKey() {
    // Replace with your actual VAPID public key
    return 'BNxBDfhBjM-HEHLLwuAzZBLrOiGXYjEAGlpR_9o-XLKzIvmRgqUHjLrFGwL-8OYlHqPZKLjF4hG8LvqvV8';
  }

  Uint8Array _urlBase64ToUint8Array(String base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    final base64 = (base64String + padding).replaceAll('-', '+').replaceAll('_', '/');
    final rawData = html.window.atob(base64);
    final outputArray = Uint8Array(rawData.length);
    
    for (int i = 0; i < rawData.length; ++i) {
      outputArray[i] = rawData.codeUnitAt(i);
    }
    
    return outputArray;
  }
}

// Extension for convenience
extension PWAExtension on PWAService {
  // Schedule notification
  Future<void> scheduleNotification({
    required String title,
    required String body,
    required Duration delay,
    String? icon,
    String? url,
  }) async {
    Future.delayed(delay, () {
      showNotification(
        title: title,
        body: body,
        icon: icon,
        url: url,
      );
    });
  }

  // Batch notifications
  Future<void> showBatchNotifications(List<Map<String, dynamic>> notifications) async {
    for (final notification in notifications) {
      await showNotification(
        title: notification['title'] ?? 'Mewayz',
        body: notification['body'] ?? '',
        icon: notification['icon'],
        url: notification['url'],
        data: notification['data'],
      );
      
      // Small delay between notifications
      await Future.delayed(Duration(milliseconds: 500));
    }
  }
}