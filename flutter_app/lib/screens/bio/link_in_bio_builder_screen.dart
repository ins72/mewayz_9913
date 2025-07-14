import 'package:flutter/material.dart';
import '../../config/theme.dart';
import '../../widgets/app_drawer.dart';
import '../../widgets/custom_button.dart';

class LinkInBioBuilderScreen extends StatefulWidget {
  const LinkInBioBuilderScreen({super.key});

  @override
  State<LinkInBioBuilderScreen> createState() => _LinkInBioBuilderScreenState();
}

class _LinkInBioBuilderScreenState extends State<LinkInBioBuilderScreen>
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
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: const Text('Link in Bio Builder'),
        backgroundColor: AppColors.background,
        foregroundColor: AppColors.textPrimary,
        elevation: 0,
        bottom: TabBar(
          controller: _tabController,
          labelColor: AppColors.primary,
          unselectedLabelColor: AppColors.textSecondary,
          indicatorColor: AppColors.primary,
          tabs: const [
            Tab(text: 'Bio Sites'),
            Tab(text: 'Builder'),
            Tab(text: 'Analytics'),
          ],
        ),
      ),
      drawer: const AppDrawer(),
      body: TabBarView(
        controller: _tabController,
        children: const [
          BioSitesTab(),
          BioBuilderTab(),
          BioAnalyticsTab(),
        ],
      ),
    );
  }
}

class BioSitesTab extends StatelessWidget {
  const BioSitesTab({super.key});

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
                'Your Bio Sites',
                style: TextStyle(
                  fontSize: 20,
                  fontWeight: FontWeight.bold,
                  color: AppColors.textPrimary,
                ),
              ),
              const Spacer(),
              CustomButton(
                text: 'Create New',
                onPressed: () {
                  // TODO: Navigate to bio site creation
                },
                type: ButtonType.primary,
                width: 120,
                height: 36,
              ),
            ],
          ),
          const SizedBox(height: 16),
          
          // Bio Sites List
          Expanded(
            child: ListView(
              children: [
                _buildBioSiteCard(
                  'Personal Bio',
                  'mewayz.com/john',
                  'Active',
                  '1.2k clicks',
                  AppColors.success,
                ),
                const SizedBox(height: 12),
                _buildBioSiteCard(
                  'Business Bio',
                  'mewayz.com/mybusiness',
                  'Draft',
                  '0 clicks',
                  AppColors.warning,
                ),
                const SizedBox(height: 12),
                _buildBioSiteCard(
                  'Portfolio',
                  'mewayz.com/portfolio',
                  'Active',
                  '856 clicks',
                  AppColors.success,
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildBioSiteCard(
    String title,
    String url,
    String status,
    String clicks,
    Color statusColor,
  ) {
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
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      title,
                      style: const TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.w600,
                        color: AppColors.textPrimary,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      url,
                      style: const TextStyle(
                        fontSize: 14,
                        color: AppColors.textSecondary,
                      ),
                    ),
                  ],
                ),
              ),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                decoration: BoxDecoration(
                  color: statusColor.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(4),
                ),
                child: Text(
                  status,
                  style: TextStyle(
                    fontSize: 12,
                    color: statusColor,
                    fontWeight: FontWeight.w500,
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          Row(
            children: [
              Icon(Icons.mouse, color: AppColors.textSecondary, size: 16),
              const SizedBox(width: 4),
              Text(
                clicks,
                style: const TextStyle(
                  fontSize: 14,
                  color: AppColors.textSecondary,
                ),
              ),
              const Spacer(),
              CustomButton(
                text: 'Edit',
                onPressed: () {
                  // TODO: Navigate to edit
                },
                type: ButtonType.secondary,
                width: 60,
                height: 32,
              ),
              const SizedBox(width: 8),
              CustomButton(
                text: 'View',
                onPressed: () {
                  // TODO: Open preview
                },
                type: ButtonType.primary,
                width: 60,
                height: 32,
              ),
            ],
          ),
        ],
      ),
    );
  }
}

class BioBuilderTab extends StatelessWidget {
  const BioBuilderTab({super.key});

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Drag & Drop Builder',
            style: TextStyle(
              fontSize: 20,
              fontWeight: FontWeight.bold,
              color: AppColors.textPrimary,
            ),
          ),
          const SizedBox(height: 8),
          const Text(
            'Build your link in bio page with our visual editor',
            style: TextStyle(
              fontSize: 14,
              color: AppColors.textSecondary,
            ),
          ),
          const SizedBox(height: 16),
          
          // Builder Interface
          Expanded(
            child: Row(
              children: [
                // Components Panel
                Container(
                  width: 200,
                  decoration: BoxDecoration(
                    color: AppColors.surface,
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: AppColors.secondaryBorder),
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Padding(
                        padding: EdgeInsets.all(16),
                        child: Text(
                          'Components',
                          style: TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.w600,
                            color: AppColors.textPrimary,
                          ),
                        ),
                      ),
                      Expanded(
                        child: ListView(
                          children: [
                            _buildComponentItem('Link Button', Icons.link),
                            _buildComponentItem('Text Block', Icons.text_fields),
                            _buildComponentItem('Image', Icons.image),
                            _buildComponentItem('Video', Icons.play_circle),
                            _buildComponentItem('Social Icons', Icons.share),
                            _buildComponentItem('Contact Form', Icons.contact_mail),
                            _buildComponentItem('Divider', Icons.horizontal_rule),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
                
                const SizedBox(width: 16),
                
                // Canvas
                Expanded(
                  child: Container(
                    decoration: BoxDecoration(
                      color: AppColors.surface,
                      borderRadius: BorderRadius.circular(12),
                      border: Border.all(color: AppColors.secondaryBorder),
                    ),
                    child: const Center(
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Icon(
                            Icons.phone_android,
                            size: 64,
                            color: AppColors.textSecondary,
                          ),
                          SizedBox(height: 16),
                          Text(
                            'Mobile Preview',
                            style: TextStyle(
                              fontSize: 18,
                              fontWeight: FontWeight.w600,
                              color: AppColors.textPrimary,
                            ),
                          ),
                          SizedBox(height: 8),
                          Text(
                            'Drag components here to build your page',
                            style: TextStyle(
                              fontSize: 14,
                              color: AppColors.textSecondary,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
                
                const SizedBox(width: 16),
                
                // Properties Panel
                Container(
                  width: 200,
                  decoration: BoxDecoration(
                    color: AppColors.surface,
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: AppColors.secondaryBorder),
                  ),
                  child: const Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Padding(
                        padding: EdgeInsets.all(16),
                        child: Text(
                          'Properties',
                          style: TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.w600,
                            color: AppColors.textPrimary,
                          ),
                        ),
                      ),
                      Expanded(
                        child: Center(
                          child: Text(
                            'Select a component\nto edit properties',
                            textAlign: TextAlign.center,
                            style: TextStyle(
                              fontSize: 14,
                              color: AppColors.textSecondary,
                            ),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
          
          const SizedBox(height: 16),
          
          // Actions
          Row(
            children: [
              const Spacer(),
              CustomButton(
                text: 'Preview',
                onPressed: () {
                  // TODO: Show preview
                },
                type: ButtonType.secondary,
                width: 100,
              ),
              const SizedBox(width: 16),
              CustomButton(
                text: 'Save',
                onPressed: () {
                  // TODO: Save bio site
                },
                type: ButtonType.primary,
                width: 100,
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildComponentItem(String title, IconData icon) {
    return ListTile(
      leading: Icon(icon, color: AppColors.textSecondary, size: 20),
      title: Text(
        title,
        style: const TextStyle(
          fontSize: 14,
          color: AppColors.textPrimary,
        ),
      ),
      dense: true,
      contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 0),
    );
  }
}

class BioAnalyticsTab extends StatelessWidget {
  const BioAnalyticsTab({super.key});

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Bio Analytics',
            style: TextStyle(
              fontSize: 20,
              fontWeight: FontWeight.bold,
              color: AppColors.textPrimary,
            ),
          ),
          const SizedBox(height: 16),
          
          // Analytics Stats
          Row(
            children: [
              Expanded(
                child: _buildStatCard('Total Visits', '2,348', Icons.visibility),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: _buildStatCard('Link Clicks', '1,892', Icons.mouse),
              ),
            ],
          ),
          const SizedBox(height: 16),
          Row(
            children: [
              Expanded(
                child: _buildStatCard('Conversion Rate', '80.6%', Icons.trending_up),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: _buildStatCard('Top Source', 'Instagram', Icons.source),
              ),
            ],
          ),
          
          const SizedBox(height: 24),
          
          // Top Links
          const Text(
            'Top Performing Links',
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
                  'Link performance data\n(Coming Soon)',
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