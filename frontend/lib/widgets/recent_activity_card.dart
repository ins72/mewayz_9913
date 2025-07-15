import 'package:flutter/material.dart';
import '../config/colors.dart';

class RecentActivityCard extends StatelessWidget {
  const RecentActivityCard({super.key});

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: AppColors.secondaryBorder),
      ),
      child: Column(
        children: [
          _buildActivityItem(
            icon: Icons.link,
            iconColor: const Color(0xFF6C5CE7),
            title: 'New bio site created',
            subtitle: 'Personal Portfolio',
            time: '2 minutes ago',
          ),
          _buildDivider(),
          _buildActivityItem(
            icon: Icons.share,
            iconColor: const Color(0xFF4ECDC4),
            title: 'Post scheduled',
            subtitle: 'Instagram - Product Launch',
            time: '15 minutes ago',
          ),
          _buildDivider(),
          _buildActivityItem(
            icon: Icons.mouse,
            iconColor: const Color(0xFF45B7D1),
            title: 'Link clicked',
            subtitle: 'Portfolio → GitHub',
            time: '1 hour ago',
          ),
          _buildDivider(),
          _buildActivityItem(
            icon: Icons.visibility,
            iconColor: const Color(0xFF26DE81),
            title: 'Bio site viewed',
            subtitle: '12 new views today',
            time: '2 hours ago',
          ),
        ],
      ),
    );
  }

  Widget _buildActivityItem({
    required IconData icon,
    required Color iconColor,
    required String title,
    required String subtitle,
    required String time,
  }) {
    return Padding(
      padding: const EdgeInsets.all(16),
      child: Row(
        children: [
          // Icon
          Container(
            width: 40,
            height: 40,
            decoration: BoxDecoration(
              color: iconColor.withOpacity(0.1),
              borderRadius: BorderRadius.circular(8),
            ),
            child: Icon(
              icon,
              color: iconColor,
              size: 20,
            ),
          ),
          
          const SizedBox(width: 12),
          
          // Content
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  title,
                  style: TextStyle(
                    fontSize: 14,
                    fontWeight: FontWeight.w500,
                    color: AppColors.textPrimary,
                  ),
                ),
                const SizedBox(height: 2),
                Text(
                  subtitle,
                  style: TextStyle(
                    fontSize: 12,
                    color: AppColors.textSecondary,
                  ),
                ),
              ],
            ),
          ),
          
          // Time
          Text(
            time,
            style: TextStyle(
              fontSize: 11,
              color: AppColors.textSecondary,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildDivider() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: Divider(
        height: 1,
        color: AppColors.secondaryBorder,
      ),
    );
  }
}