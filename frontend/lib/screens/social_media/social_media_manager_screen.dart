import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../config/theme.dart';
import '../../widgets/app_drawer.dart';
import '../../widgets/custom_button.dart';

class SocialMediaManagerScreen extends StatefulWidget {
  const SocialMediaManagerScreen({super.key});

  @override
  State<SocialMediaManagerScreen> createState() => _SocialMediaManagerScreenState();
}

class _SocialMediaManagerScreenState extends State<SocialMediaManagerScreen>
    with SingleTickerProviderStateMixin {
  late TabController _tabController;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 4, vsync: this);
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: const Text('Social Media Manager'),
        backgroundColor: AppColors.background,
        foregroundColor: AppColors.textPrimary,
        elevation: 0,
        bottom: TabBar(
          controller: _tabController,
          labelColor: AppColors.primary,
          unselectedLabelColor: AppColors.textSecondary,
          indicatorColor: AppColors.primary,
          tabs: const [
            Tab(text: 'Accounts'),
            Tab(text: 'Schedule'),
            Tab(text: 'Analytics'),
            Tab(text: 'Database'),
          ],
        ),
      ),
      drawer: const AppDrawer(),
      body: TabBarView(
        controller: _tabController,
        children: const [
          SocialAccountsTab(),
          SocialScheduleTab(),
          SocialAnalyticsTab(),
          InstagramDatabaseTab(),
        ],
      ),
    );
  }
}

class SocialAccountsTab extends StatelessWidget {
  const SocialAccountsTab({super.key});

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Connected Accounts',
            style: TextStyle(
              fontSize: 20,
              fontWeight: FontWeight.bold,
              color: AppColors.textPrimary,
            ),
          ),
          const SizedBox(height: 16),
          
          // Social Media Platform Cards
          Expanded(
            child: ListView(
              children: [
                _buildSocialPlatformCard(
                  'Instagram',
                  'Connect your Instagram account',
                  Icons.camera_alt,
                  AppColors.error,
                  false,
                ),
                const SizedBox(height: 12),
                _buildSocialPlatformCard(
                  'Facebook',
                  'Connect your Facebook page',
                  Icons.facebook,
                  AppColors.info,
                  false,
                ),
                const SizedBox(height: 12),
                _buildSocialPlatformCard(
                  'Twitter/X',
                  'Connect your Twitter account',
                  Icons.alternate_email,
                  AppColors.textPrimary,
                  false,
                ),
                const SizedBox(height: 12),
                _buildSocialPlatformCard(
                  'LinkedIn',
                  'Connect your LinkedIn profile',
                  Icons.business,
                  AppColors.info,
                  false,
                ),
                const SizedBox(height: 12),
                _buildSocialPlatformCard(
                  'TikTok',
                  'Connect your TikTok account',
                  Icons.music_note,
                  AppColors.textPrimary,
                  false,
                ),
                const SizedBox(height: 12),
                _buildSocialPlatformCard(
                  'YouTube',
                  'Connect your YouTube channel',
                  Icons.play_arrow,
                  AppColors.error,
                  false,
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSocialPlatformCard(
    String platform,
    String description,
    IconData icon,
    Color color,
    bool isConnected,
  ) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: AppColors.secondaryBorder),
      ),
      child: Row(
        children: [
          Container(
            width: 48,
            height: 48,
            decoration: BoxDecoration(
              color: color.withOpacity(0.1),
              borderRadius: BorderRadius.circular(12),
            ),
            child: Icon(icon, color: color, size: 24),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  platform,
                  style: const TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.w600,
                    color: AppColors.textPrimary,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  description,
                  style: const TextStyle(
                    fontSize: 14,
                    color: AppColors.textSecondary,
                  ),
                ),
              ],
            ),
          ),
          CustomButton(
            text: isConnected ? 'Connected' : 'Connect',
            onPressed: isConnected ? null : () {
              // TODO: Implement connection logic
            },
            type: isConnected ? ButtonType.secondary : ButtonType.primary,
            width: 100,
            height: 36,
          ),
        ],
      ),
    );
  }
}

class SocialScheduleTab extends StatelessWidget {
  const SocialScheduleTab({super.key});

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              const Text(
                'Scheduled Posts',
                style: TextStyle(
                  fontSize: 20,
                  fontWeight: FontWeight.bold,
                  color: AppColors.textPrimary,
                ),
              ),
              const Spacer(),
              CustomButton(
                text: 'Create Post',
                onPressed: () {
                  // TODO: Navigate to post creation
                },
                type: ButtonType.primary,
                width: 120,
                height: 36,
              ),
            ],
          ),
          const SizedBox(height: 16),
          
          // Calendar View
          Container(
            height: 200,
            width: double.infinity,
            decoration: BoxDecoration(
              color: AppColors.surface,
              borderRadius: BorderRadius.circular(12),
              border: Border.all(color: AppColors.secondaryBorder),
            ),
            child: const Center(
              child: Text(
                'Calendar View\n(Coming Soon)',
                textAlign: TextAlign.center,
                style: TextStyle(
                  color: AppColors.textSecondary,
                  fontSize: 16,
                ),
              ),
            ),
          ),
          
          const SizedBox(height: 24),
          
          const Text(
            'Upcoming Posts',
            style: TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.bold,
              color: AppColors.textPrimary,
            ),
          ),
          const SizedBox(height: 16),
          
          // Scheduled Posts List
          Expanded(
            child: Container(
              width: double.infinity,
              decoration: BoxDecoration(
                color: AppColors.surface,
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: AppColors.secondaryBorder),
              ),
              child: const Center(
                child: Text(
                  'No scheduled posts yet',
                  style: TextStyle(
                    color: AppColors.textSecondary,
                    fontSize: 16,
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class SocialAnalyticsTab extends StatelessWidget {
  const SocialAnalyticsTab({super.key});

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Social Media Analytics',
            style: TextStyle(
              fontSize: 20,
              fontWeight: FontWeight.bold,
              color: AppColors.textPrimary,
            ),
          ),
          const SizedBox(height: 16),
          
          // Stats Cards
          Row(
            children: [
              Expanded(
                child: _buildStatCard('Total Reach', '0', Icons.visibility),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: _buildStatCard('Engagement', '0%', Icons.favorite),
              ),
            ],
          ),
          const SizedBox(height: 16),
          Row(
            children: [
              Expanded(
                child: _buildStatCard('Followers', '0', Icons.people),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: _buildStatCard('Posts', '0', Icons.post_add),
              ),
            ],
          ),
          
          const SizedBox(height: 24),
          
          // Analytics Chart
          const Text(
            'Performance Chart',
            style: TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.bold,
              color: AppColors.textPrimary,
            ),
          ),
          const SizedBox(height: 16),
          
          Expanded(
            child: Container(
              width: double.infinity,
              decoration: BoxDecoration(
                color: AppColors.surface,
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: AppColors.secondaryBorder),
              ),
              child: const Center(
                child: Text(
                  'Analytics Chart\n(Coming Soon)',
                  textAlign: TextAlign.center,
                  style: TextStyle(
                    color: AppColors.textSecondary,
                    fontSize: 16,
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildStatCard(String title, String value, IconData icon) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: AppColors.secondaryBorder),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Icon(icon, color: AppColors.textSecondary, size: 20),
              const SizedBox(width: 8),
              Text(
                title,
                style: const TextStyle(
                  fontSize: 12,
                  color: AppColors.textSecondary,
                  fontWeight: FontWeight.w500,
                ),
              ),
            ],
          ),
          const SizedBox(height: 8),
          Text(
            value,
            style: const TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.bold,
              color: AppColors.textPrimary,
            ),
          ),
        ],
      ),
    );
  }
}

class InstagramDatabaseTab extends StatelessWidget {
  const InstagramDatabaseTab({super.key});

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Instagram Database',
            style: TextStyle(
              fontSize: 20,
              fontWeight: FontWeight.bold,
              color: AppColors.textPrimary,
            ),
          ),
          const SizedBox(height: 8),
          const Text(
            'Find and export Instagram accounts based on your criteria',
            style: TextStyle(
              fontSize: 14,
              color: AppColors.textSecondary,
            ),
          ),
          const SizedBox(height: 16),
          
          // Search Filters
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: AppColors.surface,
              borderRadius: BorderRadius.circular(12),
              border: Border.all(color: AppColors.secondaryBorder),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Search Filters',
                  style: TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.w600,
                    color: AppColors.textPrimary,
                  ),
                ),
                const SizedBox(height: 16),
                Row(
                  children: [
                    Expanded(
                      child: TextFormField(
                        style: const TextStyle(color: AppColors.textPrimary),
                        decoration: const InputDecoration(
                          labelText: 'Keywords',
                          hintText: 'Enter keywords...',
                        ),
                      ),
                    ),
                    const SizedBox(width: 16),
                    Expanded(
                      child: TextFormField(
                        style: const TextStyle(color: AppColors.textPrimary),
                        decoration: const InputDecoration(
                          labelText: 'Location',
                          hintText: 'Enter location...',
                        ),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 16),
                Row(
                  children: [
                    Expanded(
                      child: TextFormField(
                        style: const TextStyle(color: AppColors.textPrimary),
                        decoration: const InputDecoration(
                          labelText: 'Min Followers',
                          hintText: '1000',
                        ),
                        keyboardType: TextInputType.number,
                      ),
                    ),
                    const SizedBox(width: 16),
                    Expanded(
                      child: TextFormField(
                        style: const TextStyle(color: AppColors.textPrimary),
                        decoration: const InputDecoration(
                          labelText: 'Max Followers',
                          hintText: '100000',
                        ),
                        keyboardType: TextInputType.number,
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 16),
                Row(
                  children: [
                    Expanded(
                      child: CustomButton(
                        text: 'Search',
                        onPressed: () {
                          // TODO: Implement search
                        },
                        type: ButtonType.primary,
                      ),
                    ),
                    const SizedBox(width: 16),
                    Expanded(
                      child: CustomButton(
                        text: 'Export',
                        onPressed: () {
                          // TODO: Implement export
                        },
                        type: ButtonType.secondary,
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
          
          const SizedBox(height: 24),
          
          // Results
          const Text(
            'Search Results',
            style: TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.bold,
              color: AppColors.textPrimary,
            ),
          ),
          const SizedBox(height: 16),
          
          Expanded(
            child: Container(
              width: double.infinity,
              decoration: BoxDecoration(
                color: AppColors.surface,
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: AppColors.secondaryBorder),
              ),
              child: const Center(
                child: Text(
                  'No search results yet\nUse filters above to search',
                  textAlign: TextAlign.center,
                  style: TextStyle(
                    color: AppColors.textSecondary,
                    fontSize: 16,
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}