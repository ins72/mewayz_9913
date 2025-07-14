import 'package:flutter/material.dart';
import '../../config/colors.dart';
import '../../utils/responsive_layout.dart';
import '../navigation/side_navigation.dart';

class MainLayout extends StatelessWidget {
  final Widget child;
  final String currentRoute;
  final String title;
  final List<Widget>? actions;

  const MainLayout({
    super.key,
    required this.child,
    required this.currentRoute,
    required this.title,
    this.actions,
  });

  @override
  Widget build(BuildContext context) {
    return ResponsiveLayout(
      mobile: _buildMobileLayout(context),
      tablet: _buildTabletLayout(context),
      desktop: _buildDesktopLayout(context),
    );
  }

  Widget _buildMobileLayout(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        backgroundColor: AppColors.surface,
        elevation: 0,
        title: Text(
          title,
          style: const TextStyle(
            color: AppColors.textPrimary,
            fontWeight: FontWeight.bold,
          ),
        ),
        actions: actions,
      ),
      body: child,
      bottomNavigationBar: SideNavigation(currentRoute: currentRoute),
    );
  }

  Widget _buildTabletLayout(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      body: Row(
        children: [
          SideNavigation(currentRoute: currentRoute),
          Expanded(
            child: Column(
              children: [
                _buildHeader(context),
                Expanded(child: child),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildDesktopLayout(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      body: Row(
        children: [
          SideNavigation(currentRoute: currentRoute),
          Expanded(
            child: Column(
              children: [
                _buildHeader(context),
                Expanded(child: child),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildHeader(BuildContext context) {
    return Container(
      height: 70,
      padding: const EdgeInsets.symmetric(horizontal: 24),
      decoration: const BoxDecoration(
        color: AppColors.surface,
        border: Border(
          bottom: BorderSide(
            color: AppColors.border,
            width: 1,
          ),
        ),
      ),
      child: Row(
        children: [
          Text(
            title,
            style: const TextStyle(
              color: AppColors.textPrimary,
              fontSize: 20,
              fontWeight: FontWeight.bold,
            ),
          ),
          const Spacer(),
          if (actions != null) ...actions!,
        ],
      ),
    );
  }
}