import 'dart:async';
import 'dart:html' as html;
import 'package:flutter/foundation.dart';
import 'pwa_service.dart';

enum NotificationType {
  info,
  success,
  warning,
  error,
  social,
  analytics,
  workspace,
  crm,
  marketing,
}

class NotificationService {
  static final NotificationService _instance = NotificationService._internal();
  factory NotificationService() => _instance;
  NotificationService._internal();

  final PWAService _pwaService = PWAService();
  final StreamController<AppNotification> _notificationController = StreamController<AppNotification>.broadcast();
  final List<AppNotification> _notifications = [];
  Timer? _cleanupTimer;

  // Initialize notification service
  Future<void> initialize() async {
    if (!kIsWeb) return;

    try {
      // Start cleanup timer
      _cleanupTimer = Timer.periodic(Duration(minutes: 5), (_) => _cleanupOldNotifications());
      
      // Setup PWA service
      await _pwaService.initialize();
      
      print('Notification Service initialized');
    } catch (e) {
      print('Error initializing Notification Service: $e');
    }
  }

  // Get notification stream
  Stream<AppNotification> get notificationStream => _notificationController.stream;

  // Get all notifications
  List<AppNotification> get notifications => List.unmodifiable(_notifications);

  // Get unread notifications count
  int get unreadCount => _notifications.where((n) => !n.isRead).length;

  // Show notification
  Future<void> showNotification({
    required String title,
    required String message,
    NotificationType type = NotificationType.info,
    String? actionUrl,
    Map<String, dynamic>? data,
    Duration? duration,
    bool persistent = false,
    bool showPushNotification = true,
  }) async {
    final notification = AppNotification(
      id: DateTime.now().millisecondsSinceEpoch.toString(),
      title: title,
      message: message,
      type: type,
      actionUrl: actionUrl,
      data: data,
      timestamp: DateTime.now(),
      persistent: persistent,
    );

    // Add to local notifications
    _notifications.insert(0, notification);
    _notificationController.add(notification);

    // Show push notification if enabled
    if (showPushNotification && _pwaService.isNotificationPermissionGranted) {
      await _pwaService.showNotification(
        title: title,
        body: message,
        icon: _getIconForType(type),
        url: actionUrl,
        data: data,
      );
    }

    // Auto-remove non-persistent notifications
    if (!persistent && duration != null) {
      Timer(duration, () => removeNotification(notification.id));
    }
  }

  // Show success notification
  Future<void> showSuccess({
    required String title,
    required String message,
    String? actionUrl,
    bool showPushNotification = true,
  }) async {
    await showNotification(
      title: title,
      message: message,
      type: NotificationType.success,
      actionUrl: actionUrl,
      showPushNotification: showPushNotification,
      duration: Duration(seconds: 5),
    );
  }

  // Show error notification
  Future<void> showError({
    required String title,
    required String message,
    String? actionUrl,
    bool persistent = true,
    bool showPushNotification = true,
  }) async {
    await showNotification(
      title: title,
      message: message,
      type: NotificationType.error,
      actionUrl: actionUrl,
      persistent: persistent,
      showPushNotification: showPushNotification,
    );
  }

  // Show warning notification
  Future<void> showWarning({
    required String title,
    required String message,
    String? actionUrl,
    bool showPushNotification = true,
  }) async {
    await showNotification(
      title: title,
      message: message,
      type: NotificationType.warning,
      actionUrl: actionUrl,
      showPushNotification: showPushNotification,
      duration: Duration(seconds: 8),
    );
  }

  // Show info notification
  Future<void> showInfo({
    required String title,
    required String message,
    String? actionUrl,
    bool showPushNotification = false,
  }) async {
    await showNotification(
      title: title,
      message: message,
      type: NotificationType.info,
      actionUrl: actionUrl,
      showPushNotification: showPushNotification,
      duration: Duration(seconds: 4),
    );
  }

  // Show social media notification
  Future<void> showSocialNotification({
    required String title,
    required String message,
    String? actionUrl,
    Map<String, dynamic>? data,
  }) async {
    await showNotification(
      title: title,
      message: message,
      type: NotificationType.social,
      actionUrl: actionUrl,
      data: data,
      persistent: true,
    );
  }

  // Show analytics notification
  Future<void> showAnalyticsNotification({
    required String title,
    required String message,
    String? actionUrl,
    Map<String, dynamic>? data,
  }) async {
    await showNotification(
      title: title,
      message: message,
      type: NotificationType.analytics,
      actionUrl: actionUrl,
      data: data,
      persistent: true,
    );
  }

  // Show workspace notification
  Future<void> showWorkspaceNotification({
    required String title,
    required String message,
    String? actionUrl,
    Map<String, dynamic>? data,
  }) async {
    await showNotification(
      title: title,
      message: message,
      type: NotificationType.workspace,
      actionUrl: actionUrl,
      data: data,
      persistent: true,
    );
  }

  // Show CRM notification
  Future<void> showCrmNotification({
    required String title,
    required String message,
    String? actionUrl,
    Map<String, dynamic>? data,
  }) async {
    await showNotification(
      title: title,
      message: message,
      type: NotificationType.crm,
      actionUrl: actionUrl,
      data: data,
      persistent: true,
    );
  }

  // Show marketing notification
  Future<void> showMarketingNotification({
    required String title,
    required String message,
    String? actionUrl,
    Map<String, dynamic>? data,
  }) async {
    await showNotification(
      title: title,
      message: message,
      type: NotificationType.marketing,
      actionUrl: actionUrl,
      data: data,
      persistent: true,
    );
  }

  // Schedule notification
  Future<void> scheduleNotification({
    required String title,
    required String message,
    required Duration delay,
    NotificationType type = NotificationType.info,
    String? actionUrl,
    Map<String, dynamic>? data,
  }) async {
    Timer(delay, () {
      showNotification(
        title: title,
        message: message,
        type: type,
        actionUrl: actionUrl,
        data: data,
      );
    });
  }

  // Mark notification as read
  void markAsRead(String notificationId) {
    final index = _notifications.indexWhere((n) => n.id == notificationId);
    if (index != -1) {
      _notifications[index] = _notifications[index].copyWith(isRead: true);
      _notificationController.add(_notifications[index]);
    }
  }

  // Mark all notifications as read
  void markAllAsRead() {
    for (int i = 0; i < _notifications.length; i++) {
      if (!_notifications[i].isRead) {
        _notifications[i] = _notifications[i].copyWith(isRead: true);
      }
    }
    _notificationController.add(_notifications.isNotEmpty ? _notifications.first : AppNotification.empty());
  }

  // Remove notification
  void removeNotification(String notificationId) {
    _notifications.removeWhere((n) => n.id == notificationId);
    _notificationController.add(_notifications.isNotEmpty ? _notifications.first : AppNotification.empty());
  }

  // Clear all notifications
  void clearAllNotifications() {
    _notifications.clear();
    _notificationController.add(AppNotification.empty());
  }

  // Clear notifications by type
  void clearNotificationsByType(NotificationType type) {
    _notifications.removeWhere((n) => n.type == type);
    _notificationController.add(_notifications.isNotEmpty ? _notifications.first : AppNotification.empty());
  }

  // Get notifications by type
  List<AppNotification> getNotificationsByType(NotificationType type) {
    return _notifications.where((n) => n.type == type).toList();
  }

  // Cleanup old notifications
  void _cleanupOldNotifications() {
    final cutoff = DateTime.now().subtract(Duration(days: 7));
    _notifications.removeWhere((n) => !n.persistent && n.timestamp.isBefore(cutoff));
  }

  // Get icon for notification type
  String _getIconForType(NotificationType type) {
    switch (type) {
      case NotificationType.success:
        return '/icons/success.png';
      case NotificationType.error:
        return '/icons/error.png';
      case NotificationType.warning:
        return '/icons/warning.png';
      case NotificationType.social:
        return '/icons/social.png';
      case NotificationType.analytics:
        return '/icons/analytics.png';
      case NotificationType.workspace:
        return '/icons/workspace.png';
      case NotificationType.crm:
        return '/icons/crm.png';
      case NotificationType.marketing:
        return '/icons/marketing.png';
      default:
        return '/icons/Icon-192.png';
    }
  }

  // Dispose
  void dispose() {
    _cleanupTimer?.cancel();
    _notificationController.close();
  }
}

// Notification model
class AppNotification {
  final String id;
  final String title;
  final String message;
  final NotificationType type;
  final String? actionUrl;
  final Map<String, dynamic>? data;
  final DateTime timestamp;
  final bool isRead;
  final bool persistent;

  const AppNotification({
    required this.id,
    required this.title,
    required this.message,
    required this.type,
    this.actionUrl,
    this.data,
    required this.timestamp,
    this.isRead = false,
    this.persistent = false,
  });

  AppNotification copyWith({
    String? id,
    String? title,
    String? message,
    NotificationType? type,
    String? actionUrl,
    Map<String, dynamic>? data,
    DateTime? timestamp,
    bool? isRead,
    bool? persistent,
  }) {
    return AppNotification(
      id: id ?? this.id,
      title: title ?? this.title,
      message: message ?? this.message,
      type: type ?? this.type,
      actionUrl: actionUrl ?? this.actionUrl,
      data: data ?? this.data,
      timestamp: timestamp ?? this.timestamp,
      isRead: isRead ?? this.isRead,
      persistent: persistent ?? this.persistent,
    );
  }

  factory AppNotification.empty() {
    return AppNotification(
      id: '',
      title: '',
      message: '',
      type: NotificationType.info,
      timestamp: DateTime.now(),
    );
  }

  @override
  String toString() {
    return 'AppNotification(id: $id, title: $title, message: $message, type: $type, isRead: $isRead)';
  }
}

// Notification extension methods
extension NotificationExtensions on AppNotification {
  // Get relative time
  String get relativeTime {
    final now = DateTime.now();
    final difference = now.difference(timestamp);

    if (difference.inMinutes < 1) {
      return 'Just now';
    } else if (difference.inMinutes < 60) {
      return '${difference.inMinutes}m ago';
    } else if (difference.inHours < 24) {
      return '${difference.inHours}h ago';
    } else if (difference.inDays < 7) {
      return '${difference.inDays}d ago';
    } else {
      return '${timestamp.day}/${timestamp.month}/${timestamp.year}';
    }
  }

  // Get type color
  String get typeColor {
    switch (type) {
      case NotificationType.success:
        return '#22c55e';
      case NotificationType.error:
        return '#ef4444';
      case NotificationType.warning:
        return '#f59e0b';
      case NotificationType.social:
        return '#3b82f6';
      case NotificationType.analytics:
        return '#8b5cf6';
      case NotificationType.workspace:
        return '#06b6d4';
      case NotificationType.crm:
        return '#f97316';
      case NotificationType.marketing:
        return '#ec4899';
      default:
        return '#6b7280';
    }
  }

  // Get type icon
  String get typeIcon {
    switch (type) {
      case NotificationType.success:
        return '✅';
      case NotificationType.error:
        return '❌';
      case NotificationType.warning:
        return '⚠️';
      case NotificationType.social:
        return '📱';
      case NotificationType.analytics:
        return '📊';
      case NotificationType.workspace:
        return '🏢';
      case NotificationType.crm:
        return '👥';
      case NotificationType.marketing:
        return '📧';
      default:
        return 'ℹ️';
    }
  }
}