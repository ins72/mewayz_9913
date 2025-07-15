import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/auth_provider.dart';
import '../../config/colors.dart';
import '../../utils/responsive_layout.dart';
import '../../widgets/layout/main_layout.dart';
import '../../widgets/charts/analytics_chart.dart';
import '../../widgets/cards/metric_card.dart';
import '../../widgets/cards/quick_action_card.dart';

class EnhancedDashboardScreen extends StatefulWidget {
  const EnhancedDashboardScreen({super.key});

  @override
  State<EnhancedDashboardScreen> createState() => _EnhancedDashboardScreenState();
}

class _EnhancedDashboardScreenState extends State<EnhancedDashboardScreen> {
  @override
  Widget build(BuildContext context) {
    return MainLayout(
      currentRoute: '/dashboard',
      title: 'Dashboard',
      actions: [
        IconButton(
          icon: const Icon(Icons.refresh, color: AppColors.textPrimary),
          onPressed: () {
            // TODO: Refresh dashboard data
          },
        ),
        IconButton(
          icon: const Icon(Icons.notifications, color: AppColors.textPrimary),
          onPressed: () {
            // TODO: Show notifications
          },
        ),
      ],
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
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          _buildWelcomeSection(context),
          const SizedBox(height: 24),
          _buildMetricsGrid(context),
          const SizedBox(height: 24),
          _buildQuickActionsGrid(context),
          const SizedBox(height: 24),
          _buildAnalyticsChart(context),
          const SizedBox(height: 24),
          _buildRecentActivity(context),
        ],
      ),
    );
  }

  Widget _buildTabletLayout(BuildContext context) {
    return SingleChildScrollView(
      padding: EdgeInsets.all(ResponsiveHelper.getSidePadding(context)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          _buildWelcomeSection(context),
          const SizedBox(height: 24),
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Expanded(
                flex: 2,
                child: Column(
                  children: [
                    _buildMetricsGrid(context),
                    const SizedBox(height: 24),
                    _buildAnalyticsChart(context),
                  ],
                ),
              ),
              const SizedBox(width: 24),
              Expanded(
                flex: 1,
                child: Column(
                  children: [
                    _buildQuickActionsGrid(context),
                    const SizedBox(height: 24),
                    _buildRecentActivity(context),
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
    return SingleChildScrollView(
      padding: EdgeInsets.all(ResponsiveHelper.getSidePadding(context)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          _buildWelcomeSection(context),
          const SizedBox(height: 32),
          _buildMetricsGrid(context),
          const SizedBox(height: 32),
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Expanded(
                flex: 2,
                child: _buildAnalyticsChart(context),
              ),
              const SizedBox(width: 32),
              Expanded(
                flex: 1,
                child: Column(
                  children: [
                    _buildQuickActionsGrid(context),
                    const SizedBox(height: 24),
                    _buildRecentActivity(context),
                  ],
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildWelcomeSection(BuildContext context) {
    return Consumer<AuthProvider>(
      builder: (context, authProvider, _) {
        return Container(
          padding: EdgeInsets.all(ResponsiveHelper.getCardPadding(context)),
          decoration: BoxDecoration(
            gradient: LinearGradient(
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
              colors: [
                AppColors.primary.withOpacity(0.1),
                AppColors.primary.withOpacity(0.05),
              ],
            ),
            borderRadius: BorderRadius.circular(16),
            border: Border.all(color: AppColors.primary.withOpacity(0.2)),
          ),
          child: ResponsiveLayout(
            mobile: _buildWelcomeMobile(authProvider),
            tablet: _buildWelcomeTablet(authProvider),
            desktop: _buildWelcomeDesktop(authProvider),
          ),
        );
      },
    );
  }

  Widget _buildWelcomeMobile(AuthProvider authProvider) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text(
                    'Welcome back,',
                    style: TextStyle(
                      fontSize: 14,
                      color: AppColors.textSecondary,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    authProvider.user?.name ?? 'User',
                    style: const TextStyle(
                      fontSize: 24,
                      fontWeight: FontWeight.bold,
                      color: AppColors.textPrimary,
                    ),
                  ),
                ],
              ),
            ),
            Container(
              width: 50,
              height: 50,
              decoration: BoxDecoration(
                color: AppColors.primary.withOpacity(0.1),
                borderRadius: BorderRadius.circular(12),
              ),
              child: Icon(
                Icons.rocket_launch,
                color: AppColors.primary,
                size: 24,
              ),
            ),
          ],
        ),
        const SizedBox(height: 12),
        const Text(
          'Ready to grow your business today?',
          style: TextStyle(
            fontSize: 14,
            color: AppColors.textSecondary,
          ),
        ),
      ],
    );
  }

  Widget _buildWelcomeTablet(AuthProvider authProvider) {
    return Row(
      children: [
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text(
                'Welcome back,',
                style: TextStyle(
                  fontSize: 16,
                  color: AppColors.textSecondary,
                ),
              ),
              const SizedBox(height: 4),
              Text(
                authProvider.user?.name ?? 'User',
                style: const TextStyle(
                  fontSize: 28,
                  fontWeight: FontWeight.bold,
                  color: AppColors.textPrimary,
                ),
              ),
              const SizedBox(height: 8),
              const Text(
                'Ready to grow your business today?',
                style: TextStyle(
                  fontSize: 16,
                  color: AppColors.textSecondary,
                ),
              ),
            ],
          ),
        ),
        Container(
          width: 60,
          height: 60,
          decoration: BoxDecoration(
            color: AppColors.primary.withOpacity(0.1),
            borderRadius: BorderRadius.circular(12),
          ),
          child: Icon(
            Icons.rocket_launch,
            color: AppColors.primary,
            size: 30,
          ),
        ),
      ],
    );
  }

  Widget _buildWelcomeDesktop(AuthProvider authProvider) {
    return Row(
      children: [
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text(
                'Welcome back,',
                style: TextStyle(
                  fontSize: 18,
                  color: AppColors.textSecondary,
                ),
              ),
              const SizedBox(height: 8),
              Text(
                authProvider.user?.name ?? 'User',
                style: const TextStyle(
                  fontSize: 32,
                  fontWeight: FontWeight.bold,
                  color: AppColors.textPrimary,
                ),
              ),
              const SizedBox(height: 12),
              const Text(
                'Ready to grow your business today? Let\'s check your performance.',
                style: TextStyle(
                  fontSize: 16,
                  color: AppColors.textSecondary,
                ),
              ),
            ],
          ),
        ),
        Container(
          width: 80,
          height: 80,
          decoration: BoxDecoration(
            color: AppColors.primary.withOpacity(0.1),
            borderRadius: BorderRadius.circular(16),
          ),
          child: Icon(
            Icons.rocket_launch,
            color: AppColors.primary,
            size: 40,
          ),
        ),
      ],
    );
  }

  Widget _buildMetricsGrid(BuildContext context) {
    final metrics = [
      _MetricData(
        title: 'Total Views',
        value: '12.4K',
        change: '+12%',
        isPositive: true,
        icon: Icons.visibility,
        color: const Color(0xFF4ECDC4),
      ),
      _MetricData(
        title: 'Link Clicks',
        value: '3.2K',
        change: '+8%',
        isPositive: true,
        icon: Icons.mouse,
        color: const Color(0xFF45B7D1),
      ),
      _MetricData(
        title: 'Revenue',
        value: '\$2.1K',
        change: '+15%',
        isPositive: true,
        icon: Icons.trending_up,
        color: const Color(0xFF26DE81),
      ),
      _MetricData(
        title: 'Conversion',
        value: '3.8%',
        change: '+2%',
        isPositive: true,
        icon: Icons.conversion_path,
        color: const Color(0xFFF9CA24),
      ),
    ];

    return GridView.builder(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
        crossAxisCount: ResponsiveHelper.isDesktop(context) ? 4 : 2,
        childAspectRatio: ResponsiveHelper.isDesktop(context) ? 1.2 : 1.0,
        crossAxisSpacing: 16,
        mainAxisSpacing: 16,
      ),
      itemCount: metrics.length,
      itemBuilder: (context, index) {
        final metric = metrics[index];
        return MetricCard(
          title: metric.title,
          value: metric.value,
          change: metric.change,
          isPositive: metric.isPositive,
          icon: metric.icon,
          color: metric.color,
        );
      },
    );
  }

  Widget _buildQuickActionsGrid(BuildContext context) {
    final actions = [
      _ActionData(
        title: 'Bio Sites',
        subtitle: 'Manage links',
        icon: Icons.link,
        color: const Color(0xFF6C5CE7),
        route: '/bio-sites',
      ),
      _ActionData(
        title: 'Social Media',
        subtitle: 'Schedule posts',
        icon: Icons.share,
        color: const Color(0xFF4ECDC4),
        route: '/social-media',
      ),
      _ActionData(
        title: 'CRM',
        subtitle: 'Manage leads',
        icon: Icons.people,
        color: const Color(0xFF45B7D1),
        route: '/crm',
      ),
      _ActionData(
        title: 'Analytics',
        subtitle: 'View insights',
        icon: Icons.analytics,
        color: const Color(0xFF26DE81),
        route: '/analytics',
      ),
    ];

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text(
          'Quick Actions',
          style: TextStyle(
            fontSize: 18,
            fontWeight: FontWeight.bold,
            color: AppColors.textPrimary,
          ),
        ),
        const SizedBox(height: 16),
        GridView.builder(
          shrinkWrap: true,
          physics: const NeverScrollableScrollPhysics(),
          gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
            crossAxisCount: ResponsiveHelper.isDesktop(context) ? 1 : 2,
            childAspectRatio: ResponsiveHelper.isDesktop(context) ? 3.0 : 1.5,
            crossAxisSpacing: 12,
            mainAxisSpacing: 12,
          ),
          itemCount: actions.length,
          itemBuilder: (context, index) {
            final action = actions[index];
            return QuickActionCard(
              title: action.title,
              subtitle: action.subtitle,
              icon: action.icon,
              color: action.color,
              route: action.route,
            );
          },
        ),
      ],
    );
  }

  Widget _buildAnalyticsChart(BuildContext context) {
    return Container(
      height: 300,
      padding: EdgeInsets.all(ResponsiveHelper.getCardPadding(context)),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.border),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Analytics Overview',
            style: TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.bold,
              color: AppColors.textPrimary,
            ),
          ),
          const SizedBox(height: 16),
          const Expanded(
            child: AnalyticsChart(),
          ),
        ],
      ),
    );
  }

  Widget _buildRecentActivity(BuildContext context) {
    return Container(
      padding: EdgeInsets.all(ResponsiveHelper.getCardPadding(context)),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.border),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text(
                'Recent Activity',
                style: TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                  color: AppColors.textPrimary,
                ),
              ),
              TextButton(
                onPressed: () {
                  // TODO: Show all activity
                },
                child: const Text(
                  'View All',
                  style: TextStyle(color: AppColors.primary),
                ),
              ),
            ],
          ),
          const SizedBox(height: 16),
          ...List.generate(
            3,
            (index) => _buildActivityItem(
              'User activity ${index + 1}',
              'Description of activity ${index + 1}',
              '${index + 1}h ago',
              Icons.activity_zone,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildActivityItem(String title, String description, String time, IconData icon) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: Row(
        children: [
          Container(
            width: 40,
            height: 40,
            decoration: BoxDecoration(
              color: AppColors.primary.withOpacity(0.1),
              borderRadius: BorderRadius.circular(8),
            ),
            child: Icon(
              icon,
              color: AppColors.primary,
              size: 20,
            ),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  title,
                  style: const TextStyle(
                    fontSize: 14,
                    fontWeight: FontWeight.w600,
                    color: AppColors.textPrimary,
                  ),
                ),
                const SizedBox(height: 2),
                Text(
                  description,
                  style: const TextStyle(
                    fontSize: 12,
                    color: AppColors.textSecondary,
                  ),
                ),
              ],
            ),
          ),
          Text(
            time,
            style: const TextStyle(
              fontSize: 12,
              color: AppColors.textSecondary,
            ),
          ),
        ],
      ),
    );
  }
}

class _MetricData {
  final String title;
  final String value;
  final String change;
  final bool isPositive;
  final IconData icon;
  final Color color;

  _MetricData({
    required this.title,
    required this.value,
    required this.change,
    required this.isPositive,
    required this.icon,
    required this.color,
  });
}

class _ActionData {
  final String title;
  final String subtitle;
  final IconData icon;
  final Color color;
  final String route;

  _ActionData({
    required this.title,
    required this.subtitle,
    required this.icon,
    required this.color,
    required this.route,
  });
}