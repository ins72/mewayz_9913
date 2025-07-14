import 'package:flutter/material.dart';
import '../../config/colors.dart';
import '../../utils/responsive_layout.dart';
import '../../widgets/layout/main_layout.dart';
import '../../widgets/cards/social_account_card.dart';
import '../../widgets/cards/social_post_card.dart';
import '../../widgets/forms/create_post_form.dart';

class EnhancedSocialMediaScreen extends StatefulWidget {
  const EnhancedSocialMediaScreen({super.key});

  @override
  State<EnhancedSocialMediaScreen> createState() => _EnhancedSocialMediaScreenState();
}

class _EnhancedSocialMediaScreenState extends State<EnhancedSocialMediaScreen>
    with SingleTickerProviderStateMixin {
  late TabController _tabController;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 3, vsync: this);
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return MainLayout(
      currentRoute: '/social-media',
      title: 'Social Media Management',
      actions: [
        IconButton(
          icon: const Icon(Icons.add, color: AppColors.textPrimary),
          onPressed: () => _showCreatePostDialog(context),
        ),
        IconButton(
          icon: const Icon(Icons.settings, color: AppColors.textPrimary),
          onPressed: () {
            // TODO: Show settings
          },
        ),
      ],
      child: Column(
        children: [
          _buildTabBar(),
          Expanded(
            child: TabBarView(
              controller: _tabController,
              children: [
                _buildOverviewTab(),
                _buildPostsTab(),
                _buildAccountsTab(),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildTabBar() {
    return Container(
      color: AppColors.surface,
      child: TabBar(
        controller: _tabController,
        labelColor: AppColors.primary,
        unselectedLabelColor: AppColors.textSecondary,
        indicatorColor: AppColors.primary,
        tabs: const [
          Tab(text: 'Overview'),
          Tab(text: 'Posts'),
          Tab(text: 'Accounts'),
        ],
      ),
    );
  }

  Widget _buildOverviewTab() {
    return ResponsiveLayout(
      mobile: _buildMobileOverview(),
      tablet: _buildTabletOverview(),
      desktop: _buildDesktopOverview(),
    );
  }

  Widget _buildMobileOverview() {
    return SingleChildScrollView(
      padding: EdgeInsets.all(ResponsiveHelper.getSidePadding(context)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          _buildMetricsSection(),
          const SizedBox(height: 24),
          _buildRecentPostsSection(),
          const SizedBox(height: 24),
          _buildScheduledPostsSection(),
        ],
      ),
    );
  }

  Widget _buildTabletOverview() {
    return SingleChildScrollView(
      padding: EdgeInsets.all(ResponsiveHelper.getSidePadding(context)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          _buildMetricsSection(),
          const SizedBox(height: 24),
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Expanded(child: _buildRecentPostsSection()),
              const SizedBox(width: 24),
              Expanded(child: _buildScheduledPostsSection()),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildDesktopOverview() {
    return SingleChildScrollView(
      padding: EdgeInsets.all(ResponsiveHelper.getSidePadding(context)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          _buildMetricsSection(),
          const SizedBox(height: 32),
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Expanded(
                flex: 2,
                child: _buildRecentPostsSection(),
              ),
              const SizedBox(width: 32),
              Expanded(
                flex: 1,
                child: _buildScheduledPostsSection(),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildMetricsSection() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text(
          'Social Media Metrics',
          style: TextStyle(
            fontSize: 20,
            fontWeight: FontWeight.bold,
            color: AppColors.textPrimary,
          ),
        ),
        const SizedBox(height: 16),
        GridView.builder(
          shrinkWrap: true,
          physics: const NeverScrollableScrollPhysics(),
          gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
            crossAxisCount: ResponsiveHelper.isDesktop(context) ? 4 : 2,
            childAspectRatio: 1.5,
            crossAxisSpacing: 16,
            mainAxisSpacing: 16,
          ),
          itemCount: 4,
          itemBuilder: (context, index) {
            final metrics = [
              _MetricData('Total Posts', '124', '+8%', true, Icons.post_add, const Color(0xFF4ECDC4)),
              _MetricData('Engagement', '2.4K', '+12%', true, Icons.favorite, const Color(0xFF45B7D1)),
              _MetricData('Reach', '15.2K', '+18%', true, Icons.visibility, const Color(0xFF26DE81)),
              _MetricData('Followers', '3.8K', '+5%', true, Icons.people, const Color(0xFFF9CA24)),
            ];
            final metric = metrics[index];
            return _buildMetricCard(metric);
          },
        ),
      ],
    );
  }

  Widget _buildMetricCard(_MetricData metric) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: AppColors.border),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Icon(metric.icon, color: metric.color, size: 24),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                decoration: BoxDecoration(
                  color: metric.isPositive
                      ? const Color(0xFF26DE81).withOpacity(0.1)
                      : const Color(0xFFFF4757).withOpacity(0.1),
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Text(
                  metric.change,
                  style: TextStyle(
                    fontSize: 10,
                    fontWeight: FontWeight.w600,
                    color: metric.isPositive
                        ? const Color(0xFF26DE81)
                        : const Color(0xFFFF4757),
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 8),
          Text(
            metric.value,
            style: const TextStyle(
              fontSize: 20,
              fontWeight: FontWeight.bold,
              color: AppColors.textPrimary,
            ),
          ),
          const SizedBox(height: 4),
          Text(
            metric.title,
            style: const TextStyle(
              fontSize: 12,
              color: AppColors.textSecondary,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildRecentPostsSection() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text(
          'Recent Posts',
          style: TextStyle(
            fontSize: 18,
            fontWeight: FontWeight.bold,
            color: AppColors.textPrimary,
          ),
        ),
        const SizedBox(height: 16),
        ListView.builder(
          shrinkWrap: true,
          physics: const NeverScrollableScrollPhysics(),
          itemCount: 3,
          itemBuilder: (context, index) {
            return Padding(
              padding: const EdgeInsets.only(bottom: 16),
              child: SocialPostCard(
                platform: ['Instagram', 'Facebook', 'Twitter'][index],
                content: 'Sample post content ${index + 1}',
                imageUrl: null,
                likes: (index + 1) * 120,
                comments: (index + 1) * 15,
                shares: (index + 1) * 8,
                timestamp: DateTime.now().subtract(Duration(hours: index + 1)),
              ),
            );
          },
        ),
      ],
    );
  }

  Widget _buildScheduledPostsSection() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text(
          'Scheduled Posts',
          style: TextStyle(
            fontSize: 18,
            fontWeight: FontWeight.bold,
            color: AppColors.textPrimary,
          ),
        ),
        const SizedBox(height: 16),
        ListView.builder(
          shrinkWrap: true,
          physics: const NeverScrollableScrollPhysics(),
          itemCount: 2,
          itemBuilder: (context, index) {
            return Container(
              margin: const EdgeInsets.only(bottom: 12),
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: AppColors.surface,
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: AppColors.border),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Icon(
                        Icons.schedule,
                        color: AppColors.primary,
                        size: 16,
                      ),
                      const SizedBox(width: 8),
                      Text(
                        'Scheduled for ${DateTime.now().add(Duration(hours: index + 1)).toString().split('.')[0]}',
                        style: const TextStyle(
                          fontSize: 12,
                          color: AppColors.textSecondary,
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 8),
                  Text(
                    'Scheduled post content ${index + 1}',
                    style: const TextStyle(
                      fontSize: 14,
                      color: AppColors.textPrimary,
                    ),
                  ),
                ],
              ),
            );
          },
        ),
      ],
    );
  }

  Widget _buildPostsTab() {
    return SingleChildScrollView(
      padding: EdgeInsets.all(ResponsiveHelper.getSidePadding(context)),
      child: Column(
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text(
                'All Posts',
                style: TextStyle(
                  fontSize: 20,
                  fontWeight: FontWeight.bold,
                  color: AppColors.textPrimary,
                ),
              ),
              ElevatedButton.icon(
                onPressed: () => _showCreatePostDialog(context),
                icon: const Icon(Icons.add),
                label: const Text('Create Post'),
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppColors.primary,
                  foregroundColor: Colors.white,
                ),
              ),
            ],
          ),
          const SizedBox(height: 24),
          ListView.builder(
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            itemCount: 5,
            itemBuilder: (context, index) {
              return Padding(
                padding: const EdgeInsets.only(bottom: 16),
                child: SocialPostCard(
                  platform: ['Instagram', 'Facebook', 'Twitter', 'LinkedIn', 'TikTok'][index],
                  content: 'Sample post content ${index + 1}',
                  imageUrl: null,
                  likes: (index + 1) * 120,
                  comments: (index + 1) * 15,
                  shares: (index + 1) * 8,
                  timestamp: DateTime.now().subtract(Duration(hours: index + 1)),
                ),
              );
            },
          ),
        ],
      ),
    );
  }

  Widget _buildAccountsTab() {
    return SingleChildScrollView(
      padding: EdgeInsets.all(ResponsiveHelper.getSidePadding(context)),
      child: Column(
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text(
                'Connected Accounts',
                style: TextStyle(
                  fontSize: 20,
                  fontWeight: FontWeight.bold,
                  color: AppColors.textPrimary,
                ),
              ),
              ElevatedButton.icon(
                onPressed: () => _showConnectAccountDialog(context),
                icon: const Icon(Icons.add),
                label: const Text('Connect Account'),
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppColors.primary,
                  foregroundColor: Colors.white,
                ),
              ),
            ],
          ),
          const SizedBox(height: 24),
          GridView.builder(
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
              crossAxisCount: ResponsiveHelper.isDesktop(context) ? 3 : 2,
              childAspectRatio: 1.2,
              crossAxisSpacing: 16,
              mainAxisSpacing: 16,
            ),
            itemCount: 4,
            itemBuilder: (context, index) {
              final accounts = [
                _AccountData('Instagram', '@mewayz_official', true, Icons.camera_alt, const Color(0xFFE4405F)),
                _AccountData('Facebook', 'Mewayz Business', true, Icons.facebook, const Color(0xFF1877F2)),
                _AccountData('Twitter', '@mewayz', false, Icons.alternate_email, const Color(0xFF1DA1F2)),
                _AccountData('LinkedIn', 'Mewayz Company', true, Icons.business, const Color(0xFF0A66C2)),
              ];
              final account = accounts[index];
              return SocialAccountCard(
                platform: account.platform,
                username: account.username,
                isConnected: account.isConnected,
                icon: account.icon,
                color: account.color,
                onTap: () => _handleAccountTap(account),
              );
            },
          ),
        ],
      ),
    );
  }

  void _showCreatePostDialog(BuildContext context) {
    showDialog(
      context: context,
      builder: (context) => Dialog(
        backgroundColor: AppColors.surface,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(16),
        ),
        child: const CreatePostForm(),
      ),
    );
  }

  void _showConnectAccountDialog(BuildContext context) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        backgroundColor: AppColors.surface,
        title: const Text('Connect Account', style: TextStyle(color: AppColors.textPrimary)),
        content: const Text('Select a platform to connect:', style: TextStyle(color: AppColors.textSecondary)),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('Cancel', style: TextStyle(color: AppColors.textSecondary)),
          ),
          ElevatedButton(
            onPressed: () {
              Navigator.pop(context);
              // TODO: Implement account connection
            },
            style: ElevatedButton.styleFrom(
              backgroundColor: AppColors.primary,
              foregroundColor: Colors.white,
            ),
            child: const Text('Connect'),
          ),
        ],
      ),
    );
  }

  void _handleAccountTap(_AccountData account) {
    if (account.isConnected) {
      // TODO: Show account details
    } else {
      // TODO: Connect account
    }
  }
}

class _MetricData {
  final String title;
  final String value;
  final String change;
  final bool isPositive;
  final IconData icon;
  final Color color;

  _MetricData(this.title, this.value, this.change, this.isPositive, this.icon, this.color);
}

class _AccountData {
  final String platform;
  final String username;
  final bool isConnected;
  final IconData icon;
  final Color color;

  _AccountData(this.platform, this.username, this.isConnected, this.icon, this.color);
}