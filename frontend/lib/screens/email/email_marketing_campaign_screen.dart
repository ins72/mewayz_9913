import 'package:flutter/material.dart';
import '../../config/theme.dart';
import '../../widgets/app_drawer.dart';
import '../../widgets/custom_button.dart';

class EmailMarketingCampaignScreen extends StatefulWidget {
  const EmailMarketingCampaignScreen({super.key});

  @override
  State<EmailMarketingCampaignScreen> createState() => _EmailMarketingCampaignScreenState();
}

class _EmailMarketingCampaignScreenState extends State<EmailMarketingCampaignScreen>
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
        title: const Text('Email Marketing'),
        backgroundColor: AppColors.background,
        foregroundColor: AppColors.textPrimary,
        elevation: 0,
        bottom: TabBar(
          controller: _tabController,
          labelColor: AppColors.primary,
          unselectedLabelColor: AppColors.textSecondary,
          indicatorColor: AppColors.primary,
          tabs: const [
            Tab(text: 'Campaigns'),
            Tab(text: 'Templates'),
            Tab(text: 'Audience'),
            Tab(text: 'Analytics'),
          ],
        ),
      ),
      drawer: const AppDrawer(),
      body: TabBarView(
        controller: _tabController,
        children: const [
          CampaignsTab(),
          TemplatesTab(),
          AudienceTab(),
          EmailAnalyticsTab(),
        ],
      ),
    );
  }
}

class CampaignsTab extends StatelessWidget {
  const CampaignsTab({super.key});

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
                'Email Campaigns',
                style: TextStyle(
                  fontSize: 20,
                  fontWeight: FontWeight.bold,
                  color: AppColors.textPrimary,
                ),
              ),
              const Spacer(),
              CustomButton(
                text: 'Create Campaign',
                onPressed: () {
                  // TODO: Navigate to campaign creation
                },
                type: ButtonType.primary,
                width: 140,
                height: 36,
              ),
            ],
          ),
          const SizedBox(height: 16),
          
          // Stats Cards
          Row(
            children: [
              Expanded(
                child: _buildStatCard('Total Sent', '12,456', Icons.send),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: _buildStatCard('Open Rate', '24.3%', Icons.mark_email_read),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: _buildStatCard('Click Rate', '3.2%', Icons.mouse),
              ),
            ],
          ),
          
          const SizedBox(height: 24),
          
          // Campaigns List
          const Text(
            'Recent Campaigns',
            style: TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.bold,
              color: AppColors.textPrimary,
            ),
          ),
          const SizedBox(height: 16),
          
          Expanded(
            child: ListView(
              children: [
                _buildCampaignCard(
                  'Welcome Series',
                  'Automated welcome sequence',
                  'Active',
                  '2,345 sent',
                  '22.1% open',
                  AppColors.success,
                ),
                const SizedBox(height: 12),
                _buildCampaignCard(
                  'Product Launch',
                  'New product announcement',
                  'Scheduled',
                  'Draft',
                  'Pending',
                  AppColors.warning,
                ),
                const SizedBox(height: 12),
                _buildCampaignCard(
                  'Newsletter #23',
                  'Monthly newsletter',
                  'Sent',
                  '5,678 sent',
                  '26.7% open',
                  AppColors.info,
                ),
              ],
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
          Icon(icon, color: AppColors.textSecondary, size: 20),
          const SizedBox(height: 8),
          Text(
            value,
            style: const TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.bold,
              color: AppColors.textPrimary,
            ),
          ),
          Text(
            title,
            style: const TextStyle(
              fontSize: 12,
              color: AppColors.textSecondary,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildCampaignCard(
    String title,
    String description,
    String status,
    String sent,
    String openRate,
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
                      description,
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
              Text(
                sent,
                style: const TextStyle(
                  fontSize: 14,
                  color: AppColors.textSecondary,
                ),
              ),
              const SizedBox(width: 16),
              Text(
                openRate,
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
            ],
          ),
        ],
      ),
    );
  }
}

class TemplatesTab extends StatelessWidget {
  const TemplatesTab({super.key});

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
                'Email Templates',
                style: TextStyle(
                  fontSize: 20,
                  fontWeight: FontWeight.bold,
                  color: AppColors.textPrimary,
                ),
              ),
              const Spacer(),
              CustomButton(
                text: 'Create Template',
                onPressed: () {
                  // TODO: Navigate to template creation
                },
                type: ButtonType.primary,
                width: 140,
                height: 36,
              ),
            ],
          ),
          const SizedBox(height: 16),
          
          // Template Categories
          SingleChildScrollView(
            scrollDirection: Axis.horizontal,
            child: Row(
              children: [
                _buildCategoryChip('All', true),
                const SizedBox(width: 8),
                _buildCategoryChip('Newsletter', false),
                const SizedBox(width: 8),
                _buildCategoryChip('Welcome', false),
                const SizedBox(width: 8),
                _buildCategoryChip('Promotional', false),
                const SizedBox(width: 8),
                _buildCategoryChip('Transactional', false),
              ],
            ),
          ),
          
          const SizedBox(height: 24),
          
          // Templates Grid
          Expanded(
            child: GridView.builder(
              gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                crossAxisCount: 2,
                crossAxisSpacing: 16,
                mainAxisSpacing: 16,
                childAspectRatio: 0.8,
              ),
              itemCount: 6,
              itemBuilder: (context, index) {
                return _buildTemplateCard(
                  'Template ${index + 1}',
                  'Email template description',
                  'assets/images/template_preview.png',
                );
              },
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildCategoryChip(String label, bool isSelected) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      decoration: BoxDecoration(
        color: isSelected ? AppColors.primary : AppColors.surface,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(
          color: isSelected ? AppColors.primary : AppColors.secondaryBorder,
        ),
      ),
      child: Text(
        label,
        style: TextStyle(
          fontSize: 14,
          color: isSelected ? AppColors.primaryText : AppColors.textSecondary,
          fontWeight: FontWeight.w500,
        ),
      ),
    );
  }

  Widget _buildTemplateCard(String title, String description, String imagePath) {
    return Container(
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: AppColors.secondaryBorder),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Template Preview
          Container(
            height: 120,
            width: double.infinity,
            decoration: const BoxDecoration(
              color: AppColors.background,
              borderRadius: BorderRadius.only(
                topLeft: Radius.circular(12),
                topRight: Radius.circular(12),
              ),
            ),
            child: const Center(
              child: Icon(
                Icons.email,
                size: 48,
                color: AppColors.textSecondary,
              ),
            ),
          ),
          
          // Template Info
          Padding(
            padding: const EdgeInsets.all(12),
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
                const SizedBox(height: 4),
                Text(
                  description,
                  style: const TextStyle(
                    fontSize: 12,
                    color: AppColors.textSecondary,
                  ),
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                ),
                const SizedBox(height: 12),
                Row(
                  children: [
                    Expanded(
                      child: CustomButton(
                        text: 'Use',
                        onPressed: () {
                          // TODO: Use template
                        },
                        type: ButtonType.primary,
                        height: 32,
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class AudienceTab extends StatelessWidget {
  const AudienceTab({super.key});

  @override
  Widget build(BuildContext context) {
    return const Center(
      child: Text(
        'Audience Management\n(Coming Soon)',
        textAlign: TextAlign.center,
        style: TextStyle(
          color: AppColors.textSecondary,
          fontSize: 16,
        ),
      ),
    );
  }
}

class EmailAnalyticsTab extends StatelessWidget {
  const EmailAnalyticsTab({super.key});

  @override
  Widget build(BuildContext context) {
    return const Center(
      child: Text(
        'Email Analytics\n(Coming Soon)',
        textAlign: TextAlign.center,
        style: TextStyle(
          color: AppColors.textSecondary,
          fontSize: 16,
        ),
      ),
    );
  }
}