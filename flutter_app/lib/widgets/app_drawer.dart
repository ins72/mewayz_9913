import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:go_router/go_router.dart';
import '../providers/auth_provider.dart';
import '../providers/workspace_provider.dart';
import '../providers/theme_provider.dart';
import '../config/theme.dart';

class AppDrawer extends StatelessWidget {
  const AppDrawer({super.key});

  @override
  Widget build(BuildContext context) {
    return Drawer(
      backgroundColor: AppColors.surface,
      child: SafeArea(
        child: Column(
          children: [
            // Header
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(20),
              decoration: const BoxDecoration(
                color: AppColors.background,
                border: Border(
                  bottom: BorderSide(color: AppColors.secondaryBorder),
                ),
              ),
              child: Consumer2<AuthProvider, WorkspaceProvider>(
                builder: (context, authProvider, workspaceProvider, _) {
                  return Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        children: [
                          CircleAvatar(
                            radius: 24,
                            backgroundColor: AppColors.primary,
                            child: Text(
                              authProvider.user?.name.isNotEmpty == true
                                  ? authProvider.user!.name[0].toUpperCase()
                                  : 'U',
                              style: const TextStyle(
                                color: AppColors.primaryText,
                                fontWeight: FontWeight.bold,
                                fontSize: 18,
                              ),
                            ),
                          ),
                          const SizedBox(width: 12),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  authProvider.user?.name ?? 'User',
                                  style: const TextStyle(
                                    color: AppColors.textPrimary,
                                    fontWeight: FontWeight.bold,
                                    fontSize: 16,
                                  ),
                                ),
                                Text(
                                  workspaceProvider.currentWorkspace?.name ?? '',
                                  style: const TextStyle(
                                    color: AppColors.textSecondary,
                                    fontSize: 12,
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ],
                  );
                },
              ),
            ),
            
            // Menu Items
            Expanded(
              child: ListView(
                padding: const EdgeInsets.symmetric(vertical: 8),
                children: [
                  _buildDrawerItem(
                    icon: Icons.dashboard,
                    title: 'Dashboard',
                    onTap: () => context.go('/dashboard'),
                  ),
                  _buildDrawerItem(
                    icon: Icons.share,
                    title: 'Social Media',
                    onTap: () => context.go('/social-media'),
                  ),
                  _buildDrawerItem(
                    icon: Icons.link,
                    title: 'Link in Bio',
                    onTap: () => context.go('/link-in-bio'),
                  ),
                  _buildDrawerItem(
                    icon: Icons.people,
                    title: 'CRM & Leads',
                    onTap: () => context.go('/crm'),
                  ),
                  _buildDrawerItem(
                    icon: Icons.email,
                    title: 'Email Marketing',
                    onTap: () => context.go('/email-marketing'),
                  ),
                  _buildDrawerItem(
                    icon: Icons.shopping_bag,
                    title: 'E-commerce',
                    onTap: () => context.go('/ecommerce'),
                  ),
                  _buildDrawerItem(
                    icon: Icons.school,
                    title: 'Courses',
                    onTap: () => context.go('/courses'),
                  ),
                  _buildDrawerItem(
                    icon: Icons.analytics,
                    title: 'Analytics',
                    onTap: () => context.go('/analytics'),
                  ),
                  const Divider(color: AppColors.secondaryBorder),
                  _buildDrawerItem(
                    icon: Icons.swap_horiz,
                    title: 'Switch Workspace',
                    onTap: () => context.go('/workspace-selector'),
                  ),
                  _buildDrawerItem(
                    icon: Icons.settings,
                    title: 'Settings',
                    onTap: () {
                      // TODO: Implement settings
                    },
                  ),
                  
                  // Theme Toggle
                  Consumer<ThemeProvider>(
                    builder: (context, themeProvider, _) {
                      return _buildDrawerItem(
                        icon: themeProvider.isDarkMode ? Icons.light_mode : Icons.dark_mode,
                        title: themeProvider.isDarkMode ? 'Light Mode' : 'Dark Mode',
                        onTap: themeProvider.toggleTheme,
                      );
                    },
                  ),
                ],
              ),
            ),
            
            // Logout
            Container(
              padding: const EdgeInsets.all(16),
              decoration: const BoxDecoration(
                border: Border(
                  top: BorderSide(color: AppColors.secondaryBorder),
                ),
              ),
              child: Consumer<AuthProvider>(
                builder: (context, authProvider, _) {
                  return _buildDrawerItem(
                    icon: Icons.logout,
                    title: 'Logout',
                    onTap: () async {
                      await authProvider.logout();
                      if (context.mounted) {
                        context.go('/login');
                      }
                    },
                  );
                },
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildDrawerItem({
    required IconData icon,
    required String title,
    required VoidCallback onTap,
  }) {
    return ListTile(
      leading: Icon(icon, color: AppColors.textSecondary),
      title: Text(
        title,
        style: const TextStyle(
          color: AppColors.textPrimary,
          fontSize: 16,
        ),
      ),
      onTap: onTap,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(8),
      ),
      contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
    );
  }
}