import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../config/colors.dart';
import '../../utils/responsive_layout.dart';

class SideNavigation extends StatelessWidget {
  final String currentRoute;

  const SideNavigation({
    super.key,
    required this.currentRoute,
  });

  @override
  Widget build(BuildContext context) {
    return ResponsiveLayout(
      mobile: _buildMobileNavigation(context),
      tablet: _buildTabletNavigation(context),
      desktop: _buildDesktopNavigation(context),
    );
  }

  Widget _buildMobileNavigation(BuildContext context) {
    return Container(
      height: 60,
      color: AppColors.surface,
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceEvenly,
        children: _getNavigationItems(context, true),
      ),
    );
  }

  Widget _buildTabletNavigation(BuildContext context) {
    return Container(
      width: 80,
      color: AppColors.surface,
      child: Column(
        children: _getNavigationItems(context, false),
      ),
    );
  }

  Widget _buildDesktopNavigation(BuildContext context) {
    return Container(
      width: 280,
      color: AppColors.surface,
      child: Column(
        children: [
          // Logo Section
          Container(
            padding: const EdgeInsets.all(24),
            child: Row(
              children: [
                Container(
                  width: 40,
                  height: 40,
                  decoration: BoxDecoration(
                    color: AppColors.primary,
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: const Center(
                    child: Text(
                      'M',
                      style: TextStyle(
                        color: Colors.white,
                        fontSize: 20,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ),
                ),
                const SizedBox(width: 12),
                const Text(
                  'Mewayz',
                  style: TextStyle(
                    color: AppColors.textPrimary,
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ],
            ),
          ),
          const Divider(color: AppColors.border),
          // Navigation Items
          Expanded(
            child: ListView(
              padding: const EdgeInsets.all(16),
              children: _getDesktopNavigationItems(context),
            ),
          ),
        ],
      ),
    );
  }

  List<Widget> _getNavigationItems(BuildContext context, bool isMobile) {
    final items = [
      _NavItem(
        icon: Icons.dashboard,
        label: 'Dashboard',
        route: '/dashboard',
        isActive: currentRoute == '/dashboard',
      ),
      _NavItem(
        icon: Icons.link,
        label: 'Bio Sites',
        route: '/bio-sites',
        isActive: currentRoute == '/bio-sites',
      ),
      _NavItem(
        icon: Icons.share,
        label: 'Social',
        route: '/social-media',
        isActive: currentRoute == '/social-media',
      ),
      _NavItem(
        icon: Icons.analytics,
        label: 'Analytics',
        route: '/analytics',
        isActive: currentRoute == '/analytics',
      ),
      _NavItem(
        icon: Icons.settings,
        label: 'Settings',
        route: '/settings',
        isActive: currentRoute == '/settings',
      ),
    ];

    return items.map((item) {
      return isMobile
          ? _buildMobileNavItem(context, item)
          : _buildTabletNavItem(context, item);
    }).toList();
  }

  List<Widget> _getDesktopNavigationItems(BuildContext context) {
    final items = [
      _NavItem(
        icon: Icons.dashboard,
        label: 'Dashboard',
        route: '/dashboard',
        isActive: currentRoute == '/dashboard',
      ),
      _NavItem(
        icon: Icons.link,
        label: 'Bio Sites',
        route: '/bio-sites',
        isActive: currentRoute == '/bio-sites',
      ),
      _NavItem(
        icon: Icons.share,
        label: 'Social Media',
        route: '/social-media',
        isActive: currentRoute == '/social-media',
      ),
      _NavItem(
        icon: Icons.people,
        label: 'CRM',
        route: '/crm',
        isActive: currentRoute == '/crm',
      ),
      _NavItem(
        icon: Icons.email,
        label: 'Email Marketing',
        route: '/email-marketing',
        isActive: currentRoute == '/email-marketing',
      ),
      _NavItem(
        icon: Icons.store,
        label: 'E-commerce',
        route: '/ecommerce',
        isActive: currentRoute == '/ecommerce',
      ),
      _NavItem(
        icon: Icons.school,
        label: 'Courses',
        route: '/courses',
        isActive: currentRoute == '/courses',
      ),
      _NavItem(
        icon: Icons.analytics,
        label: 'Analytics',
        route: '/analytics',
        isActive: currentRoute == '/analytics',
      ),
    ];

    return items.map((item) => _buildDesktopNavItem(context, item)).toList();
  }

  Widget _buildMobileNavItem(BuildContext context, _NavItem item) {
    return GestureDetector(
      onTap: () => context.go(item.route),
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 8),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(
              item.icon,
              color: item.isActive ? AppColors.primary : AppColors.textSecondary,
              size: 24,
            ),
            const SizedBox(height: 4),
            Text(
              item.label,
              style: TextStyle(
                color: item.isActive ? AppColors.primary : AppColors.textSecondary,
                fontSize: 10,
                fontWeight: item.isActive ? FontWeight.w600 : FontWeight.normal,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildTabletNavItem(BuildContext context, _NavItem item) {
    return GestureDetector(
      onTap: () => context.go(item.route),
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 16),
        child: Icon(
          item.icon,
          color: item.isActive ? AppColors.primary : AppColors.textSecondary,
          size: 28,
        ),
      ),
    );
  }

  Widget _buildDesktopNavItem(BuildContext context, _NavItem item) {
    return Container(
      margin: const EdgeInsets.only(bottom: 4),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          borderRadius: BorderRadius.circular(8),
          onTap: () => context.go(item.route),
          child: Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
            decoration: BoxDecoration(
              color: item.isActive ? AppColors.primary.withOpacity(0.1) : null,
              borderRadius: BorderRadius.circular(8),
              border: item.isActive
                  ? Border.all(color: AppColors.primary.withOpacity(0.2))
                  : null,
            ),
            child: Row(
              children: [
                Icon(
                  item.icon,
                  color: item.isActive ? AppColors.primary : AppColors.textSecondary,
                  size: 20,
                ),
                const SizedBox(width: 12),
                Text(
                  item.label,
                  style: TextStyle(
                    color: item.isActive ? AppColors.primary : AppColors.textSecondary,
                    fontSize: 14,
                    fontWeight: item.isActive ? FontWeight.w600 : FontWeight.normal,
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

class _NavItem {
  final IconData icon;
  final String label;
  final String route;
  final bool isActive;

  _NavItem({
    required this.icon,
    required this.label,
    required this.route,
    required this.isActive,
  });
}