import 'package:flutter/material.dart';
import '../../config/colors.dart';
import '../../utils/responsive_layout.dart';
import '../../widgets/layout/main_layout.dart';
import '../../widgets/cards/bio_site_card.dart';
import '../../widgets/forms/create_bio_site_form.dart';

class EnhancedBioSitesScreen extends StatefulWidget {
  const EnhancedBioSitesScreen({super.key});

  @override
  State<EnhancedBioSitesScreen> createState() => _EnhancedBioSitesScreenState();
}

class _EnhancedBioSitesScreenState extends State<EnhancedBioSitesScreen> {
  @override
  Widget build(BuildContext context) {
    return MainLayout(
      currentRoute: '/bio-sites',
      title: 'Bio Sites',
      actions: [
        IconButton(
          icon: const Icon(Icons.add, color: AppColors.textPrimary),
          onPressed: () => _showCreateBioSiteDialog(context),
        ),
        IconButton(
          icon: const Icon(Icons.analytics, color: AppColors.textPrimary),
          onPressed: () {
            // TODO: Show analytics
          },
        ),
      ],
      child: ResponsiveLayout(
        mobile: _buildMobileLayout(),
        tablet: _buildTabletLayout(),
        desktop: _buildDesktopLayout(),
      ),
    );
  }

  Widget _buildMobileLayout() {
    return SingleChildScrollView(
      padding: EdgeInsets.all(ResponsiveHelper.getSidePadding(context)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          _buildOverviewSection(),
          const SizedBox(height: 24),
          _buildBioSitesList(),
        ],
      ),
    );
  }

  Widget _buildTabletLayout() {
    return SingleChildScrollView(
      padding: EdgeInsets.all(ResponsiveHelper.getSidePadding(context)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          _buildOverviewSection(),
          const SizedBox(height: 24),
          _buildBioSitesList(),
        ],
      ),
    );
  }

  Widget _buildDesktopLayout() {
    return SingleChildScrollView(
      padding: EdgeInsets.all(ResponsiveHelper.getSidePadding(context)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          _buildOverviewSection(),
          const SizedBox(height: 32),
          _buildBioSitesList(),
        ],
      ),
    );
  }

  Widget _buildOverviewSection() {
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
          const Text(
            'Bio Sites Overview',
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
                _MetricData('Total Sites', '3', '+1', true, Icons.link, const Color(0xFF4ECDC4)),
                _MetricData('Total Clicks', '2.4K', '+12%', true, Icons.mouse, const Color(0xFF45B7D1)),
                _MetricData('Active Sites', '2', '0', false, Icons.check_circle, const Color(0xFF26DE81)),
                _MetricData('Conversion', '3.2%', '+0.5%', true, Icons.trending_up, const Color(0xFFF9CA24)),
              ];
              final metric = metrics[index];
              return _buildMetricCard(metric);
            },
          ),
        ],
      ),
    );
  }

  Widget _buildMetricCard(_MetricData metric) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: AppColors.background,
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
              if (metric.change.isNotEmpty)
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

  Widget _buildBioSitesList() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            const Text(
              'Your Bio Sites',
              style: TextStyle(
                fontSize: 20,
                fontWeight: FontWeight.bold,
                color: AppColors.textPrimary,
              ),
            ),
            ElevatedButton.icon(
              onPressed: () => _showCreateBioSiteDialog(context),
              icon: const Icon(Icons.add),
              label: const Text('Create Bio Site'),
              style: ElevatedButton.styleFrom(
                backgroundColor: AppColors.primary,
                foregroundColor: Colors.white,
              ),
            ),
          ],
        ),
        const SizedBox(height: 16),
        GridView.builder(
          shrinkWrap: true,
          physics: const NeverScrollableScrollPhysics(),
          gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
            crossAxisCount: ResponsiveHelper.isDesktop(context) ? 3 : 2,
            childAspectRatio: 0.8,
            crossAxisSpacing: 16,
            mainAxisSpacing: 16,
          ),
          itemCount: 3,
          itemBuilder: (context, index) {
            final sites = [
              _BioSiteData(
                'Personal Brand',
                'mewayz.com/personal',
                'Active',
                1240,
                'https://picsum.photos/300/200?random=1',
                true,
              ),
              _BioSiteData(
                'Business Links',
                'mewayz.com/business',
                'Active',
                980,
                'https://picsum.photos/300/200?random=2',
                true,
              ),
              _BioSiteData(
                'Portfolio',
                'mewayz.com/portfolio',
                'Draft',
                0,
                'https://picsum.photos/300/200?random=3',
                false,
              ),
            ];
            final site = sites[index];
            return BioSiteCard(
              title: site.title,
              url: site.url,
              status: site.status,
              clicks: site.clicks,
              thumbnailUrl: site.thumbnailUrl,
              isActive: site.isActive,
              onTap: () => _handleBioSiteTap(site),
              onEdit: () => _editBioSite(site),
              onDelete: () => _deleteBioSite(site),
            );
          },
        ),
      ],
    );
  }

  void _showCreateBioSiteDialog(BuildContext context) {
    showDialog(
      context: context,
      builder: (context) => Dialog(
        backgroundColor: AppColors.surface,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(16),
        ),
        child: const CreateBioSiteForm(),
      ),
    );
  }

  void _handleBioSiteTap(_BioSiteData site) {
    // TODO: Navigate to bio site editor
    print('Tapped on ${site.title}');
  }

  void _editBioSite(_BioSiteData site) {
    // TODO: Open edit dialog
    print('Edit ${site.title}');
  }

  void _deleteBioSite(_BioSiteData site) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        backgroundColor: AppColors.surface,
        title: const Text('Delete Bio Site', style: TextStyle(color: AppColors.textPrimary)),
        content: Text('Are you sure you want to delete "${site.title}"?', style: const TextStyle(color: AppColors.textSecondary)),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('Cancel', style: TextStyle(color: AppColors.textSecondary)),
          ),
          ElevatedButton(
            onPressed: () {
              Navigator.pop(context);
              // TODO: Implement deletion
              ScaffoldMessenger.of(context).showSnackBar(
                const SnackBar(
                  content: Text('Bio site deleted successfully'),
                  backgroundColor: Color(0xFF26DE81),
                ),
              );
            },
            style: ElevatedButton.styleFrom(
              backgroundColor: const Color(0xFFFF4757),
              foregroundColor: Colors.white,
            ),
            child: const Text('Delete'),
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

  _MetricData(this.title, this.value, this.change, this.isPositive, this.icon, this.color);
}

class _BioSiteData {
  final String title;
  final String url;
  final String status;
  final int clicks;
  final String thumbnailUrl;
  final bool isActive;

  _BioSiteData(this.title, this.url, this.status, this.clicks, this.thumbnailUrl, this.isActive);
}