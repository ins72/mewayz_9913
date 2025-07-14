import 'package:flutter/material.dart';
import '../../config/colors.dart';
import '../../utils/responsive_layout.dart';
import '../../widgets/layout/main_layout.dart';
import '../../widgets/cards/contact_card.dart';
import '../../widgets/forms/create_contact_form.dart';

class EnhancedCrmScreen extends StatefulWidget {
  const EnhancedCrmScreen({super.key});

  @override
  State<EnhancedCrmScreen> createState() => _EnhancedCrmScreenState();
}

class _EnhancedCrmScreenState extends State<EnhancedCrmScreen>
    with SingleTickerProviderStateMixin {
  late TabController _tabController;
  String _searchQuery = '';

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
    return MainLayout(
      currentRoute: '/crm',
      title: 'CRM',
      actions: [
        IconButton(
          icon: const Icon(Icons.search, color: AppColors.textPrimary),
          onPressed: () => _showSearchDialog(context),
        ),
        IconButton(
          icon: const Icon(Icons.person_add, color: AppColors.textPrimary),
          onPressed: () => _showCreateContactDialog(context),
        ),
      ],
      child: Column(
        children: [
          _buildStatsSection(),
          _buildTabBar(),
          Expanded(
            child: TabBarView(
              controller: _tabController,
              children: [
                _buildContactsTab(),
                _buildLeadsTab(),
                _buildPipelineTab(),
                _buildAnalyticsTab(),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildStatsSection() {
    return Container(
      padding: const EdgeInsets.all(24),
      color: AppColors.surface,
      child: Row(
        children: [
          Expanded(
            child: _buildStatCard('Total Contacts', '1,234', Icons.people, const Color(0xFF4ECDC4)),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: _buildStatCard('Active Leads', '89', Icons.trending_up, const Color(0xFF45B7D1)),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: _buildStatCard('Conversions', '23', Icons.check_circle, const Color(0xFF26DE81)),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: _buildStatCard('Revenue', '\$45.2K', Icons.attach_money, const Color(0xFFF9CA24)),
          ),
        ],
      ),
    );
  }

  Widget _buildStatCard(String title, String value, IconData icon, Color color) {
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
          Icon(icon, color: color, size: 24),
          const SizedBox(height: 8),
          Text(
            value,
            style: const TextStyle(
              fontSize: 20,
              fontWeight: FontWeight.bold,
              color: AppColors.textPrimary,
            ),
          ),
          const SizedBox(height: 4),
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

  Widget _buildTabBar() {
    return Container(
      color: AppColors.surface,
      child: TabBar(
        controller: _tabController,
        labelColor: AppColors.primary,
        unselectedLabelColor: AppColors.textSecondary,
        indicatorColor: AppColors.primary,
        tabs: const [
          Tab(text: 'Contacts'),
          Tab(text: 'Leads'),
          Tab(text: 'Pipeline'),
          Tab(text: 'Analytics'),
        ],
      ),
    );
  }

  Widget _buildContactsTab() {
    return SingleChildScrollView(
      padding: EdgeInsets.all(ResponsiveHelper.getSidePadding(context)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text(
                'All Contacts',
                style: TextStyle(
                  fontSize: 20,
                  fontWeight: FontWeight.bold,
                  color: AppColors.textPrimary,
                ),
              ),
              ElevatedButton.icon(
                onPressed: () => _showCreateContactDialog(context),
                icon: const Icon(Icons.add),
                label: const Text('Add Contact'),
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
              childAspectRatio: 1.2,
              crossAxisSpacing: 16,
              mainAxisSpacing: 16,
            ),
            itemCount: 6,
            itemBuilder: (context, index) {
              final contacts = [
                _ContactData('John Doe', 'john@example.com', 'Lead', 'Hot', const Color(0xFFFF4757)),
                _ContactData('Jane Smith', 'jane@example.com', 'Customer', 'Active', const Color(0xFF26DE81)),
                _ContactData('Mike Johnson', 'mike@example.com', 'Prospect', 'Warm', const Color(0xFFF9CA24)),
                _ContactData('Sarah Wilson', 'sarah@example.com', 'Lead', 'Cold', const Color(0xFF45B7D1)),
                _ContactData('Alex Brown', 'alex@example.com', 'Customer', 'Active', const Color(0xFF26DE81)),
                _ContactData('Emily Davis', 'emily@example.com', 'Prospect', 'Warm', const Color(0xFFF9CA24)),
              ];
              final contact = contacts[index];
              return ContactCard(
                name: contact.name,
                email: contact.email,
                type: contact.type,
                status: contact.status,
                statusColor: contact.statusColor,
                onTap: () => _handleContactTap(contact),
              );
            },
          ),
        ],
      ),
    );
  }

  Widget _buildLeadsTab() {
    return SingleChildScrollView(
      padding: EdgeInsets.all(ResponsiveHelper.getSidePadding(context)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Active Leads',
            style: TextStyle(
              fontSize: 20,
              fontWeight: FontWeight.bold,
              color: AppColors.textPrimary,
            ),
          ),
          const SizedBox(height: 16),
          ListView.builder(
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            itemCount: 4,
            itemBuilder: (context, index) {
              final leads = [
                _LeadData('John Doe', 'Interested in premium plan', 'Hot', 85, const Color(0xFFFF4757)),
                _LeadData('Jane Smith', 'Requested demo', 'Warm', 65, const Color(0xFFF9CA24)),
                _LeadData('Mike Johnson', 'Downloaded whitepaper', 'Cold', 30, const Color(0xFF45B7D1)),
                _LeadData('Sarah Wilson', 'Signed up for trial', 'Hot', 90, const Color(0xFFFF4757)),
              ];
              final lead = leads[index];
              return _buildLeadCard(lead);
            },
          ),
        ],
      ),
    );
  }

  Widget _buildLeadCard(_LeadData lead) {
    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: AppColors.border),
      ),
      child: Row(
        children: [
          CircleAvatar(
            backgroundColor: lead.statusColor.withOpacity(0.1),
            child: Text(
              lead.name.split(' ').map((e) => e[0]).join(''),
              style: TextStyle(
                color: lead.statusColor,
                fontWeight: FontWeight.bold,
              ),
            ),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  lead.name,
                  style: const TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.bold,
                    color: AppColors.textPrimary,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  lead.note,
                  style: const TextStyle(
                    fontSize: 12,
                    color: AppColors.textSecondary,
                  ),
                ),
                const SizedBox(height: 8),
                Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                      decoration: BoxDecoration(
                        color: lead.statusColor.withOpacity(0.1),
                        borderRadius: BorderRadius.circular(12),
                      ),
                      child: Text(
                        lead.status,
                        style: TextStyle(
                          fontSize: 10,
                          fontWeight: FontWeight.w600,
                          color: lead.statusColor,
                        ),
                      ),
                    ),
                    const SizedBox(width: 16),
                    Text(
                      'Score: ${lead.score}%',
                      style: const TextStyle(
                        fontSize: 12,
                        color: AppColors.textSecondary,
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
          IconButton(
            icon: const Icon(Icons.more_vert, color: AppColors.textSecondary),
            onPressed: () {
              // TODO: Show lead actions
            },
          ),
        ],
      ),
    );
  }

  Widget _buildPipelineTab() {
    return SingleChildScrollView(
      padding: EdgeInsets.all(ResponsiveHelper.getSidePadding(context)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Sales Pipeline',
            style: TextStyle(
              fontSize: 20,
              fontWeight: FontWeight.bold,
              color: AppColors.textPrimary,
            ),
          ),
          const SizedBox(height: 16),
          ResponsiveLayout(
            mobile: _buildMobilePipeline(),
            tablet: _buildTabletPipeline(),
            desktop: _buildDesktopPipeline(),
          ),
        ],
      ),
    );
  }

  Widget _buildMobilePipeline() {
    return Column(
      children: [
        _buildPipelineStage('Prospects', 12, const Color(0xFF45B7D1)),
        const SizedBox(height: 16),
        _buildPipelineStage('Qualified', 8, const Color(0xFFF9CA24)),
        const SizedBox(height: 16),
        _buildPipelineStage('Proposal', 5, const Color(0xFF4ECDC4)),
        const SizedBox(height: 16),
        _buildPipelineStage('Closed', 3, const Color(0xFF26DE81)),
      ],
    );
  }

  Widget _buildTabletPipeline() {
    return Row(
      children: [
        Expanded(child: _buildPipelineStage('Prospects', 12, const Color(0xFF45B7D1))),
        const SizedBox(width: 16),
        Expanded(child: _buildPipelineStage('Qualified', 8, const Color(0xFFF9CA24))),
        const SizedBox(width: 16),
        Expanded(child: _buildPipelineStage('Proposal', 5, const Color(0xFF4ECDC4))),
        const SizedBox(width: 16),
        Expanded(child: _buildPipelineStage('Closed', 3, const Color(0xFF26DE81))),
      ],
    );
  }

  Widget _buildDesktopPipeline() {
    return _buildTabletPipeline();
  }

  Widget _buildPipelineStage(String title, int count, Color color) {
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
              Text(
                title,
                style: const TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.bold,
                  color: AppColors.textPrimary,
                ),
              ),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                decoration: BoxDecoration(
                  color: color.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Text(
                  count.toString(),
                  style: TextStyle(
                    fontSize: 12,
                    fontWeight: FontWeight.w600,
                    color: color,
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          Container(
            height: 4,
            decoration: BoxDecoration(
              color: color.withOpacity(0.2),
              borderRadius: BorderRadius.circular(2),
            ),
            child: FractionallySizedBox(
              widthFactor: count / 12,
              child: Container(
                decoration: BoxDecoration(
                  color: color,
                  borderRadius: BorderRadius.circular(2),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildAnalyticsTab() {
    return SingleChildScrollView(
      padding: EdgeInsets.all(ResponsiveHelper.getSidePadding(context)),
      child: const Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            'CRM Analytics',
            style: TextStyle(
              fontSize: 20,
              fontWeight: FontWeight.bold,
              color: AppColors.textPrimary,
            ),
          ),
          SizedBox(height: 16),
          Text(
            'Analytics features coming soon...',
            style: TextStyle(
              fontSize: 16,
              color: AppColors.textSecondary,
            ),
          ),
        ],
      ),
    );
  }

  void _showSearchDialog(BuildContext context) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        backgroundColor: AppColors.surface,
        title: const Text('Search Contacts', style: TextStyle(color: AppColors.textPrimary)),
        content: TextField(
          onChanged: (value) => setState(() => _searchQuery = value),
          decoration: const InputDecoration(
            hintText: 'Search by name or email...',
            hintStyle: TextStyle(color: AppColors.textSecondary),
            border: OutlineInputBorder(),
          ),
          style: const TextStyle(color: AppColors.textPrimary),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('Cancel', style: TextStyle(color: AppColors.textSecondary)),
          ),
          ElevatedButton(
            onPressed: () {
              Navigator.pop(context);
              // TODO: Implement search
            },
            style: ElevatedButton.styleFrom(
              backgroundColor: AppColors.primary,
              foregroundColor: Colors.white,
            ),
            child: const Text('Search'),
          ),
        ],
      ),
    );
  }

  void _showCreateContactDialog(BuildContext context) {
    showDialog(
      context: context,
      builder: (context) => Dialog(
        backgroundColor: AppColors.surface,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(16),
        ),
        child: const CreateContactForm(),
      ),
    );
  }

  void _handleContactTap(_ContactData contact) {
    // TODO: Show contact details
    print('Tapped on ${contact.name}');
  }
}

class _ContactData {
  final String name;
  final String email;
  final String type;
  final String status;
  final Color statusColor;

  _ContactData(this.name, this.email, this.type, this.status, this.statusColor);
}

class _LeadData {
  final String name;
  final String note;
  final String status;
  final int score;
  final Color statusColor;

  _LeadData(this.name, this.note, this.status, this.score, this.statusColor);
}