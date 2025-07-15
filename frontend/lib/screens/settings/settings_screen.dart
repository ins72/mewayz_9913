import 'package:flutter/material.dart';
import '../../config/colors.dart';
import '../../utils/responsive_layout.dart';
import '../../widgets/layout/main_layout.dart';

class SettingsScreen extends StatelessWidget {
  const SettingsScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return MainLayout(
      currentRoute: '/settings',
      title: 'Settings',
      child: ResponsiveLayout(
        mobile: _buildMobileLayout(context),
        tablet: _buildTabletLayout(context),
        desktop: _buildDesktopLayout(context),
      ),
    );
  }

  Widget _buildMobileLayout(BuildContext context) {
    return SingleChildScrollView(
      padding: EdgeInsets.all(ResponsiveHelper.getSidePadding(context)),
      child: Column(
        children: [
          _buildAccountSection(context),
          const SizedBox(height: 24),
          _buildPreferencesSection(context),
          const SizedBox(height: 24),
          _buildNotificationsSection(context),
          const SizedBox(height: 24),
          _buildSecuritySection(context),
        ],
      ),
    );
  }

  Widget _buildTabletLayout(BuildContext context) {
    return SingleChildScrollView(
      padding: EdgeInsets.all(ResponsiveHelper.getSidePadding(context)),
      child: Column(
        children: [
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Expanded(
                child: Column(
                  children: [
                    _buildAccountSection(context),
                    const SizedBox(height: 24),
                    _buildPreferencesSection(context),
                  ],
                ),
              ),
              const SizedBox(width: 24),
              Expanded(
                child: Column(
                  children: [
                    _buildNotificationsSection(context),
                    const SizedBox(height: 24),
                    _buildSecuritySection(context),
                  ],
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildDesktopLayout(BuildContext context) {
    return _buildTabletLayout(context);
  }

  Widget _buildAccountSection(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.border),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Account Settings',
            style: TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.bold,
              color: AppColors.textPrimary,
            ),
          ),
          const SizedBox(height: 16),
          ListTile(
            leading: const Icon(Icons.person, color: AppColors.textSecondary),
            title: const Text('Profile', style: TextStyle(color: AppColors.textPrimary)),
            subtitle: const Text('Update your profile information', style: TextStyle(color: AppColors.textSecondary)),
            trailing: const Icon(Icons.arrow_forward_ios, color: AppColors.textSecondary, size: 16),
            onTap: () {
              // TODO: Navigate to profile
            },
          ),
          const Divider(color: AppColors.border),
          ListTile(
            leading: const Icon(Icons.business, color: AppColors.textSecondary),
            title: const Text('Workspace', style: TextStyle(color: AppColors.textPrimary)),
            subtitle: const Text('Manage workspace settings', style: TextStyle(color: AppColors.textSecondary)),
            trailing: const Icon(Icons.arrow_forward_ios, color: AppColors.textSecondary, size: 16),
            onTap: () {
              // TODO: Navigate to workspace settings
            },
          ),
          const Divider(color: AppColors.border),
          ListTile(
            leading: const Icon(Icons.credit_card, color: AppColors.textSecondary),
            title: const Text('Billing', style: TextStyle(color: AppColors.textPrimary)),
            subtitle: const Text('View billing and subscription', style: TextStyle(color: AppColors.textSecondary)),
            trailing: const Icon(Icons.arrow_forward_ios, color: AppColors.textSecondary, size: 16),
            onTap: () {
              // TODO: Navigate to billing
            },
          ),
        ],
      ),
    );
  }

  Widget _buildPreferencesSection(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.border),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Preferences',
            style: TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.bold,
              color: AppColors.textPrimary,
            ),
          ),
          const SizedBox(height: 16),
          SwitchListTile(
            secondary: const Icon(Icons.dark_mode, color: AppColors.textSecondary),
            title: const Text('Dark Mode', style: TextStyle(color: AppColors.textPrimary)),
            subtitle: const Text('Use dark theme', style: TextStyle(color: AppColors.textSecondary)),
            value: true,
            onChanged: (value) {
              // TODO: Toggle dark mode
            },
          ),
          const Divider(color: AppColors.border),
          SwitchListTile(
            secondary: const Icon(Icons.language, color: AppColors.textSecondary),
            title: const Text('Auto-detect Language', style: TextStyle(color: AppColors.textPrimary)),
            subtitle: const Text('Automatically detect language', style: TextStyle(color: AppColors.textSecondary)),
            value: false,
            onChanged: (value) {
              // TODO: Toggle auto-detect language
            },
          ),
          const Divider(color: AppColors.border),
          ListTile(
            leading: const Icon(Icons.schedule, color: AppColors.textSecondary),
            title: const Text('Timezone', style: TextStyle(color: AppColors.textPrimary)),
            subtitle: const Text('UTC-8 (Pacific Time)', style: TextStyle(color: AppColors.textSecondary)),
            trailing: const Icon(Icons.arrow_forward_ios, color: AppColors.textSecondary, size: 16),
            onTap: () {
              // TODO: Change timezone
            },
          ),
        ],
      ),
    );
  }

  Widget _buildNotificationsSection(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.border),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Notifications',
            style: TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.bold,
              color: AppColors.textPrimary,
            ),
          ),
          const SizedBox(height: 16),
          SwitchListTile(
            secondary: const Icon(Icons.notifications, color: AppColors.textSecondary),
            title: const Text('Push Notifications', style: TextStyle(color: AppColors.textPrimary)),
            subtitle: const Text('Receive push notifications', style: TextStyle(color: AppColors.textSecondary)),
            value: true,
            onChanged: (value) {
              // TODO: Toggle push notifications
            },
          ),
          const Divider(color: AppColors.border),
          SwitchListTile(
            secondary: const Icon(Icons.email, color: AppColors.textSecondary),
            title: const Text('Email Notifications', style: TextStyle(color: AppColors.textPrimary)),
            subtitle: const Text('Receive email notifications', style: TextStyle(color: AppColors.textSecondary)),
            value: true,
            onChanged: (value) {
              // TODO: Toggle email notifications
            },
          ),
          const Divider(color: AppColors.border),
          SwitchListTile(
            secondary: const Icon(Icons.schedule, color: AppColors.textSecondary),
            title: const Text('Scheduled Reports', style: TextStyle(color: AppColors.textPrimary)),
            subtitle: const Text('Weekly analytics reports', style: TextStyle(color: AppColors.textSecondary)),
            value: false,
            onChanged: (value) {
              // TODO: Toggle scheduled reports
            },
          ),
        ],
      ),
    );
  }

  Widget _buildSecuritySection(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.border),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Security',
            style: TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.bold,
              color: AppColors.textPrimary,
            ),
          ),
          const SizedBox(height: 16),
          ListTile(
            leading: const Icon(Icons.lock, color: AppColors.textSecondary),
            title: const Text('Change Password', style: TextStyle(color: AppColors.textPrimary)),
            subtitle: const Text('Update your password', style: TextStyle(color: AppColors.textSecondary)),
            trailing: const Icon(Icons.arrow_forward_ios, color: AppColors.textSecondary, size: 16),
            onTap: () {
              // TODO: Change password
            },
          ),
          const Divider(color: AppColors.border),
          ListTile(
            leading: const Icon(Icons.security, color: AppColors.textSecondary),
            title: const Text('Two-Factor Authentication', style: TextStyle(color: AppColors.textPrimary)),
            subtitle: const Text('Enable 2FA for extra security', style: TextStyle(color: AppColors.textSecondary)),
            trailing: const Icon(Icons.arrow_forward_ios, color: AppColors.textSecondary, size: 16),
            onTap: () {
              // TODO: Setup 2FA
            },
          ),
          const Divider(color: AppColors.border),
          ListTile(
            leading: const Icon(Icons.devices, color: AppColors.textSecondary),
            title: const Text('Active Sessions', style: TextStyle(color: AppColors.textPrimary)),
            subtitle: const Text('Manage logged in devices', style: TextStyle(color: AppColors.textSecondary)),
            trailing: const Icon(Icons.arrow_forward_ios, color: AppColors.textSecondary, size: 16),
            onTap: () {
              // TODO: View active sessions
            },
          ),
        ],
      ),
    );
  }
}