import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/pwa_provider.dart';

class PWANotificationWidget extends StatefulWidget {
  const PWANotificationWidget({super.key});

  @override
  State<PWANotificationWidget> createState() => _PWANotificationWidgetState();
}

class _PWANotificationWidgetState extends State<PWANotificationWidget>
    with SingleTickerProviderStateMixin {
  late AnimationController _animationController;
  late Animation<double> _fadeAnimation;
  late Animation<Offset> _slideAnimation;
  
  bool _isVisible = false;
  String? _currentNotificationType;

  @override
  void initState() {
    super.initState();
    _animationController = AnimationController(
      duration: const Duration(milliseconds: 300),
      vsync: this,
    );
    
    _fadeAnimation = Tween<double>(
      begin: 0.0,
      end: 1.0,
    ).animate(CurvedAnimation(
      parent: _animationController,
      curve: Curves.easeInOut,
    ));
    
    _slideAnimation = Tween<Offset>(
      begin: const Offset(0, -1),
      end: const Offset(0, 0),
    ).animate(CurvedAnimation(
      parent: _animationController,
      curve: Curves.easeInOut,
    ));
  }

  @override
  void dispose() {
    _animationController.dispose();
    super.dispose();
  }

  void _showNotification(String type) {
    if (_currentNotificationType == type && _isVisible) return;
    
    setState(() {
      _currentNotificationType = type;
      _isVisible = true;
    });
    
    _animationController.forward();
    
    // Auto-hide after 10 seconds for some notifications
    if (type == 'offline' || type == 'online') {
      Future.delayed(const Duration(seconds: 10), () {
        _hideNotification();
      });
    }
  }

  void _hideNotification() {
    _animationController.reverse().then((_) {
      if (mounted) {
        setState(() {
          _isVisible = false;
          _currentNotificationType = null;
        });
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return Consumer<PWAProvider>(
      builder: (context, pwaProvider, child) {
        // Show install prompt
        if (pwaProvider.installPromptState == InstallPromptState.available &&
            _currentNotificationType != 'install') {
          WidgetsBinding.instance.addPostFrameCallback((_) {
            _showNotification('install');
          });
        }

        // Show update available notification
        if (pwaProvider.updateAvailable && _currentNotificationType != 'update') {
          WidgetsBinding.instance.addPostFrameCallback((_) {
            _showNotification('update');
          });
        }

        // Show offline/online notifications
        if (!pwaProvider.isOnline && _currentNotificationType != 'offline') {
          WidgetsBinding.instance.addPostFrameCallback((_) {
            _showNotification('offline');
          });
        } else if (pwaProvider.isOnline && _currentNotificationType == 'offline') {
          WidgetsBinding.instance.addPostFrameCallback((_) {
            _showNotification('online');
          });
        }

        if (!_isVisible) return const SizedBox.shrink();

        return Positioned(
          top: 0,
          left: 0,
          right: 0,
          child: AnimatedBuilder(
            animation: _animationController,
            builder: (context, child) {
              return SlideTransition(
                position: _slideAnimation,
                child: FadeTransition(
                  opacity: _fadeAnimation,
                  child: _buildNotificationContent(context, pwaProvider),
                ),
              );
            },
          ),
        );
      },
    );
  }

  Widget _buildNotificationContent(BuildContext context, PWAProvider pwaProvider) {
    final theme = Theme.of(context);
    
    switch (_currentNotificationType) {
      case 'install':
        return _buildInstallNotification(context, pwaProvider);
      case 'update':
        return _buildUpdateNotification(context, pwaProvider);
      case 'offline':
        return _buildOfflineNotification(context);
      case 'online':
        return _buildOnlineNotification(context);
      default:
        return const SizedBox.shrink();
    }
  }

  Widget _buildInstallNotification(BuildContext context, PWAProvider pwaProvider) {
    return Container(
      margin: const EdgeInsets.all(16),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: const Color(0xFF191919),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: const Color(0xFF333333)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.3),
            blurRadius: 8,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Row(
        children: [
          Container(
            width: 48,
            height: 48,
            decoration: BoxDecoration(
              color: const Color(0xFF101010),
              borderRadius: BorderRadius.circular(8),
            ),
            child: const Icon(
              Icons.install_mobile,
              color: Color(0xFFFFFFFF),
              size: 24,
            ),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Install Mewayz',
                  style: TextStyle(
                    color: Color(0xFFFFFFFF),
                    fontSize: 16,
                    fontWeight: FontWeight.w600,
                  ),
                ),
                const SizedBox(height: 4),
                const Text(
                  'Install the app for a better experience and offline access.',
                  style: TextStyle(
                    color: Color(0xFF7B7B7B),
                    fontSize: 14,
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(width: 16),
          Row(
            children: [
              TextButton(
                onPressed: _hideNotification,
                child: const Text(
                  'Later',
                  style: TextStyle(
                    color: Color(0xFF7B7B7B),
                    fontSize: 14,
                  ),
                ),
              ),
              const SizedBox(width: 8),
              ElevatedButton(
                onPressed: () async {
                  await pwaProvider.installApp();
                  _hideNotification();
                },
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFFFFFFFF),
                  foregroundColor: const Color(0xFF101010),
                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(8),
                  ),
                ),
                child: const Text(
                  'Install',
                  style: TextStyle(
                    fontSize: 14,
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildUpdateNotification(BuildContext context, PWAProvider pwaProvider) {
    return Container(
      margin: const EdgeInsets.all(16),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: const Color(0xFF191919),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: const Color(0xFF333333)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.3),
            blurRadius: 8,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Row(
        children: [
          Container(
            width: 48,
            height: 48,
            decoration: BoxDecoration(
              color: const Color(0xFF3b82f6),
              borderRadius: BorderRadius.circular(8),
            ),
            child: const Icon(
              Icons.system_update,
              color: Colors.white,
              size: 24,
            ),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Update Available',
                  style: TextStyle(
                    color: Color(0xFFFFFFFF),
                    fontSize: 16,
                    fontWeight: FontWeight.w600,
                  ),
                ),
                const SizedBox(height: 4),
                const Text(
                  'A new version of Mewayz is available with improvements and bug fixes.',
                  style: TextStyle(
                    color: Color(0xFF7B7B7B),
                    fontSize: 14,
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(width: 16),
          Row(
            children: [
              TextButton(
                onPressed: _hideNotification,
                child: const Text(
                  'Later',
                  style: TextStyle(
                    color: Color(0xFF7B7B7B),
                    fontSize: 14,
                  ),
                ),
              ),
              const SizedBox(width: 8),
              ElevatedButton(
                onPressed: () {
                  pwaProvider.skipWaitingForUpdate();
                  _hideNotification();
                },
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFF3b82f6),
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(8),
                  ),
                ),
                child: const Text(
                  'Update',
                  style: TextStyle(
                    fontSize: 14,
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildOfflineNotification(BuildContext context) {
    return Container(
      margin: const EdgeInsets.all(16),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: const Color(0xFF191919),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: const Color(0xFFf59e0b)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.3),
            blurRadius: 8,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Row(
        children: [
          Container(
            width: 48,
            height: 48,
            decoration: BoxDecoration(
              color: const Color(0xFFf59e0b),
              borderRadius: BorderRadius.circular(8),
            ),
            child: const Icon(
              Icons.cloud_off,
              color: Colors.white,
              size: 24,
            ),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'You\'re Offline',
                  style: TextStyle(
                    color: Color(0xFFFFFFFF),
                    fontSize: 16,
                    fontWeight: FontWeight.w600,
                  ),
                ),
                const SizedBox(height: 4),
                const Text(
                  'Some features may be limited. Your data will sync when you\'re back online.',
                  style: TextStyle(
                    color: Color(0xFF7B7B7B),
                    fontSize: 14,
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(width: 16),
          IconButton(
            onPressed: _hideNotification,
            icon: const Icon(
              Icons.close,
              color: Color(0xFF7B7B7B),
              size: 20,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildOnlineNotification(BuildContext context) {
    return Container(
      margin: const EdgeInsets.all(16),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: const Color(0xFF191919),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: const Color(0xFF22c55e)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.3),
            blurRadius: 8,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Row(
        children: [
          Container(
            width: 48,
            height: 48,
            decoration: BoxDecoration(
              color: const Color(0xFF22c55e),
              borderRadius: BorderRadius.circular(8),
            ),
            child: const Icon(
              Icons.cloud_done,
              color: Colors.white,
              size: 24,
            ),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Back Online',
                  style: TextStyle(
                    color: Color(0xFFFFFFFF),
                    fontSize: 16,
                    fontWeight: FontWeight.w600,
                  ),
                ),
                const SizedBox(height: 4),
                const Text(
                  'Your connection has been restored. Syncing your data...',
                  style: TextStyle(
                    color: Color(0xFF7B7B7B),
                    fontSize: 14,
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(width: 16),
          IconButton(
            onPressed: _hideNotification,
            icon: const Icon(
              Icons.close,
              color: Color(0xFF7B7B7B),
              size: 20,
            ),
          ),
        ],
      ),
    );
  }
}

// PWA status indicator widget
class PWAStatusIndicator extends StatelessWidget {
  const PWAStatusIndicator({super.key});

  @override
  Widget build(BuildContext context) {
    return Consumer<PWAProvider>(
      builder: (context, pwaProvider, child) {
        if (!pwaProvider.isInitialized) {
          return const SizedBox.shrink();
        }

        return Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            // Online/Offline indicator
            Container(
              width: 8,
              height: 8,
              decoration: BoxDecoration(
                color: pwaProvider.isOnline 
                    ? const Color(0xFF22c55e)
                    : const Color(0xFFf59e0b),
                shape: BoxShape.circle,
              ),
            ),
            const SizedBox(width: 8),
            Text(
              pwaProvider.isOnline ? 'Online' : 'Offline',
              style: TextStyle(
                color: pwaProvider.isOnline 
                    ? const Color(0xFF22c55e)
                    : const Color(0xFFf59e0b),
                fontSize: 12,
                fontWeight: FontWeight.w500,
              ),
            ),
            
            // Notification indicator
            if (pwaProvider.unreadNotifications > 0) ...[
              const SizedBox(width: 12),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                decoration: BoxDecoration(
                  color: const Color(0xFFef4444),
                  borderRadius: BorderRadius.circular(10),
                ),
                child: Text(
                  pwaProvider.unreadNotifications.toString(),
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 10,
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ),
            ],
          ],
        );
      },
    );
  }
}