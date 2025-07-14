import 'package:flutter/foundation.dart';
import '../services/pwa_service.dart';
import '../services/notification_service.dart';
import '../services/offline_storage_service.dart';

enum InstallPromptState {
  hidden,
  available,
  installed,
}

class PWAProvider extends ChangeNotifier {
  final PWAService _pwaService = PWAService();
  final NotificationService _notificationService = NotificationService();
  final OfflineStorageService _storageService = OfflineStorageService();

  bool _isInitialized = false;
  bool _isOnline = true;
  InstallPromptState _installPromptState = InstallPromptState.hidden;
  int _unreadNotifications = 0;
  bool _updateAvailable = false;

  // Getters
  bool get isInitialized => _isInitialized;
  bool get isOnline => _isOnline;
  bool get isServiceWorkerSupported => _pwaService.isServiceWorkerSupported;
  bool get isServiceWorkerRegistered => _pwaService.isServiceWorkerRegistered;
  bool get isNotificationSupported => _pwaService.isNotificationSupported;
  bool get isNotificationPermissionGranted => _pwaService.isNotificationPermissionGranted;
  bool get isAppInstalled => _pwaService.isAppInstalled;
  InstallPromptState get installPromptState => _installPromptState;
  int get unreadNotifications => _unreadNotifications;
  bool get updateAvailable => _updateAvailable;

  // Services
  PWAService get pwaService => _pwaService;
  NotificationService get notificationService => _notificationService;
  OfflineStorageService get storageService => _storageService;

  // Initialize PWA features
  Future<void> initialize() async {
    if (!kIsWeb) {
      _isInitialized = true;
      notifyListeners();
      return;
    }

    try {
      // Initialize services
      await _storageService.initialize();
      await _pwaService.initialize();
      await _notificationService.initialize();

      // Setup listeners
      _setupNotificationListener();
      _setupConnectivityListener();
      _setupInstallPromptListener();

      _isInitialized = true;
      notifyListeners();

      print('PWA Provider initialized successfully');
    } catch (e) {
      print('Error initializing PWA Provider: $e');
      _isInitialized = true; // Mark as initialized even on error
      notifyListeners();
    }
  }

  // Setup notification listener
  void _setupNotificationListener() {
    _notificationService.notificationStream.listen((notification) {
      _unreadNotifications = _notificationService.unreadCount;
      notifyListeners();
    });
  }

  // Setup connectivity listener
  void _setupConnectivityListener() {
    // Listen for online/offline events
    if (kIsWeb) {
      _isOnline = _pwaService.isOnline;
      
      // Set up periodic connectivity check
      Stream.periodic(Duration(seconds: 10)).listen((_) {
        final currentOnlineState = _pwaService.isOnline;
        if (currentOnlineState != _isOnline) {
          _isOnline = currentOnlineState;
          notifyListeners();
          
          if (_isOnline) {
            _handleOnlineEvent();
          } else {
            _handleOfflineEvent();
          }
        }
      });
    }
  }

  // Setup install prompt listener
  void _setupInstallPromptListener() {
    if (kIsWeb) {
      // Check if app is already installed
      if (_pwaService.isAppInstalled) {
        _installPromptState = InstallPromptState.installed;
      } else {
        _installPromptState = InstallPromptState.available;
      }
      notifyListeners();
    }
  }

  // Handle online event
  void _handleOnlineEvent() {
    _notificationService.showSuccess(
      title: 'Back Online',
      message: 'Your connection has been restored. Syncing data...',
      showPushNotification: false,
    );
    
    // Sync pending offline actions
    _syncOfflineData();
  }

  // Handle offline event
  void _handleOfflineEvent() {
    _notificationService.showWarning(
      title: 'You\'re Offline',
      message: 'Some features may be limited. Your data will sync when you\'re back online.',
      showPushNotification: false,
    );
  }

  // Sync offline data
  Future<void> _syncOfflineData() async {
    try {
      // Sync pending actions
      final pendingActions = await _storageService.getPendingActions();
      
      for (final action in pendingActions) {
        // Process each pending action
        await _processPendingAction(action);
      }

      if (pendingActions.isNotEmpty) {
        await _notificationService.showSuccess(
          title: 'Data Synced',
          message: 'Successfully synced ${pendingActions.length} offline actions.',
          showPushNotification: false,
        );
      }
    } catch (e) {
      print('Error syncing offline data: $e');
      await _notificationService.showError(
        title: 'Sync Failed',
        message: 'Failed to sync some offline data. Will retry automatically.',
        showPushNotification: false,
      );
    }
  }

  // Process pending action
  Future<void> _processPendingAction(Map<String, dynamic> action) async {
    try {
      // Mark as completed
      await _storageService.markActionCompleted(action['id']);
      print('Processed pending action: ${action['action_type']}');
    } catch (e) {
      await _storageService.markActionFailed(action['id'], e.toString());
      print('Failed to process pending action: ${action['action_type']} - $e');
    }
  }

  // Install app to home screen
  Future<void> installApp() async {
    if (!kIsWeb) return;

    try {
      _pwaService.installApp();
      _installPromptState = InstallPromptState.installed;
      notifyListeners();
      
      await _notificationService.showSuccess(
        title: 'App Installed',
        message: 'Mewayz has been installed to your home screen!',
        showPushNotification: false,
      );
    } catch (e) {
      print('Error installing app: $e');
      await _notificationService.showError(
        title: 'Installation Failed',
        message: 'Failed to install app. Please try again.',
        showPushNotification: false,
      );
    }
  }

  // Request notification permission
  Future<void> requestNotificationPermission() async {
    if (!kIsWeb) return;

    try {
      await _pwaService.initialize();
      notifyListeners();
      
      if (_pwaService.isNotificationPermissionGranted) {
        await _notificationService.showSuccess(
          title: 'Notifications Enabled',
          message: 'You\'ll now receive important updates and alerts.',
          showPushNotification: false,
        );
      }
    } catch (e) {
      print('Error requesting notification permission: $e');
    }
  }

  // Subscribe to push notifications
  Future<void> subscribeToPushNotifications() async {
    if (!kIsWeb) return;

    try {
      final subscription = await _pwaService.subscribeToPushNotifications();
      
      if (subscription != null) {
        // Send subscription to your backend
        await _sendSubscriptionToBackend(subscription);
        
        await _notificationService.showSuccess(
          title: 'Push Notifications Enabled',
          message: 'You\'ll receive notifications even when the app is closed.',
          showPushNotification: false,
        );
      }
    } catch (e) {
      print('Error subscribing to push notifications: $e');
      await _notificationService.showError(
        title: 'Push Notifications Failed',
        message: 'Failed to enable push notifications. Please try again.',
        showPushNotification: false,
      );
    }
  }

  // Send subscription to backend
  Future<void> _sendSubscriptionToBackend(String subscription) async {
    // Implement API call to send subscription to backend
    print('Sending push subscription to backend: $subscription');
  }

  // Clear all caches
  Future<void> clearAllCaches() async {
    if (!kIsWeb) return;

    try {
      _pwaService.clearAllCaches();
      await _storageService.clearAllData();
      
      await _notificationService.showSuccess(
        title: 'Cache Cleared',
        message: 'All cached data has been cleared.',
        showPushNotification: false,
      );
    } catch (e) {
      print('Error clearing caches: $e');
      await _notificationService.showError(
        title: 'Clear Cache Failed',
        message: 'Failed to clear some cached data.',
        showPushNotification: false,
      );
    }
  }

  // Get storage usage
  Future<String> getStorageUsage() async {
    if (!kIsWeb) return '0 KB';

    try {
      final sizeBytes = await _storageService.getStorageSize();
      return _formatBytes(sizeBytes);
    } catch (e) {
      print('Error getting storage usage: $e');
      return 'Unknown';
    }
  }

  // Format bytes to human readable
  String _formatBytes(int bytes) {
    if (bytes < 1024) return '$bytes B';
    if (bytes < 1024 * 1024) return '${(bytes / 1024).toStringAsFixed(1)} KB';
    if (bytes < 1024 * 1024 * 1024) return '${(bytes / (1024 * 1024)).toStringAsFixed(1)} MB';
    return '${(bytes / (1024 * 1024 * 1024)).toStringAsFixed(1)} GB';
  }

  // Save draft
  Future<void> saveDraft({
    required String type,
    required Map<String, dynamic> data,
    String? workspaceId,
  }) async {
    try {
      final draft = {
        'type': type,
        'data': data,
        'workspace_id': workspaceId,
      };
      
      await _storageService.saveDraft(draft);
      
      await _notificationService.showInfo(
        title: 'Draft Saved',
        message: 'Your work has been saved as a draft.',
        showPushNotification: false,
      );
    } catch (e) {
      print('Error saving draft: $e');
    }
  }

  // Add offline action
  Future<void> addOfflineAction({
    required String actionType,
    required Map<String, dynamic> data,
  }) async {
    try {
      final action = {
        'action_type': actionType,
        'data': data,
      };
      
      await _storageService.savePendingAction(action);
      
      if (!_isOnline) {
        await _notificationService.showInfo(
          title: 'Action Queued',
          message: 'Your action will be processed when you\'re back online.',
          showPushNotification: false,
        );
      }
    } catch (e) {
      print('Error adding offline action: $e');
    }
  }

  // Mark notification as read
  void markNotificationAsRead(String notificationId) {
    _notificationService.markAsRead(notificationId);
    _unreadNotifications = _notificationService.unreadCount;
    notifyListeners();
  }

  // Mark all notifications as read
  void markAllNotificationsAsRead() {
    _notificationService.markAllAsRead();
    _unreadNotifications = 0;
    notifyListeners();
  }

  // Clear notifications
  void clearNotifications() {
    _notificationService.clearAllNotifications();
    _unreadNotifications = 0;
    notifyListeners();
  }

  // Update connectivity status manually
  void updateConnectivityStatus(bool isOnline) {
    if (_isOnline != isOnline) {
      _isOnline = isOnline;
      notifyListeners();
      
      if (isOnline) {
        _handleOnlineEvent();
      } else {
        _handleOfflineEvent();
      }
    }
  }

  // Skip waiting for service worker update
  void skipWaitingForUpdate() {
    _pwaService.skipWaiting();
    _updateAvailable = false;
    notifyListeners();
  }

  // Show update available notification
  void showUpdateAvailable() {
    _updateAvailable = true;
    notifyListeners();
    
    _notificationService.showInfo(
      title: 'Update Available',
      message: 'A new version of Mewayz is available. Refresh to update.',
      showPushNotification: false,
    );
  }

  @override
  void dispose() {
    _notificationService.dispose();
    _storageService.dispose();
    super.dispose();
  }
}