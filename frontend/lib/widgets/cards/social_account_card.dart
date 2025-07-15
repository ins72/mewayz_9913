import 'package:flutter/material.dart';
import '../../config/colors.dart';

class SocialAccountCard extends StatelessWidget {
  final String platform;
  final String username;
  final bool isConnected;
  final IconData icon;
  final Color color;
  final VoidCallback? onTap;

  const SocialAccountCard({
    super.key,
    required this.platform,
    required this.username,
    required this.isConnected,
    required this.icon,
    required this.color,
    this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: AppColors.surface,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(
            color: isConnected ? color.withOpacity(0.3) : AppColors.border,
          ),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Container(
                  width: 40,
                  height: 40,
                  decoration: BoxDecoration(
                    color: color.withOpacity(0.1),
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Icon(
                    icon,
                    color: color,
                    size: 24,
                  ),
                ),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                  decoration: BoxDecoration(
                    color: isConnected
                        ? const Color(0xFF26DE81).withOpacity(0.1)
                        : const Color(0xFFFF4757).withOpacity(0.1),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Text(
                    isConnected ? 'Connected' : 'Disconnected',
                    style: TextStyle(
                      fontSize: 10,
                      fontWeight: FontWeight.w600,
                      color: isConnected
                          ? const Color(0xFF26DE81)
                          : const Color(0xFFFF4757),
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 16),
            Text(
              platform,
              style: const TextStyle(
                fontSize: 16,
                fontWeight: FontWeight.bold,
                color: AppColors.textPrimary,
              ),
            ),
            const SizedBox(height: 4),
            Text(
              username,
              style: const TextStyle(
                fontSize: 12,
                color: AppColors.textSecondary,
              ),
            ),
          ],
        ),
      ),
    );
  }
}