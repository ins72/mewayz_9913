import 'package:flutter/material.dart';
import '../../config/theme.dart';
import '../../widgets/app_drawer.dart';
import '../../widgets/custom_button.dart';

class EcommerceStoreManagerScreen extends StatefulWidget {
  const EcommerceStoreManagerScreen({super.key});

  @override
  State<EcommerceStoreManagerScreen> createState() => _EcommerceStoreManagerScreenState();
}

class _EcommerceStoreManagerScreenState extends State<EcommerceStoreManagerScreen>
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
        title: const Text('E-commerce Store'),
        backgroundColor: AppColors.background,
        foregroundColor: AppColors.textPrimary,
        elevation: 0,
        bottom: TabBar(
          controller: _tabController,
          labelColor: AppColors.primary,
          unselectedLabelColor: AppColors.textSecondary,
          indicatorColor: AppColors.primary,
          tabs: const [
            Tab(text: 'Products'),
            Tab(text: 'Orders'),
            Tab(text: 'Store'),
            Tab(text: 'Analytics'),
          ],
        ),
      ),
      drawer: const AppDrawer(),
      body: TabBarView(
        controller: _tabController,
        children: const [
          ProductsTab(),
          OrdersTab(),
          StoreTab(),
          StoreAnalyticsTab(),
        ],
      ),
    );
  }
}

class ProductsTab extends StatelessWidget {
  const ProductsTab({super.key});

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
                'Products',
                style: TextStyle(
                  fontSize: 20,
                  fontWeight: FontWeight.bold,
                  color: AppColors.textPrimary,
                ),
              ),
              const Spacer(),
              CustomButton(
                text: 'Add Product',
                onPressed: () {
                  // TODO: Navigate to product creation
                },
                type: ButtonType.primary,
                width: 120,
                height: 36,
              ),
            ],
          ),
          const SizedBox(height: 16),
          
          // Search and Filter
          Row(
            children: [
              Expanded(
                child: TextFormField(
                  style: const TextStyle(color: AppColors.textPrimary),
                  decoration: const InputDecoration(
                    hintText: 'Search products...',
                    prefixIcon: Icon(Icons.search, color: AppColors.textSecondary),
                  ),
                ),
              ),
              const SizedBox(width: 16),
              CustomButton(
                text: 'Filter',
                onPressed: () {
                  // TODO: Show filter options
                },
                type: ButtonType.secondary,
                width: 80,
                height: 48,
              ),
            ],
          ),
          
          const SizedBox(height: 16),
          
          // Stats Cards
          Row(
            children: [
              Expanded(
                child: _buildStatCard('Total Products', '45', Icons.inventory),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: _buildStatCard('In Stock', '38', Icons.check_circle),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: _buildStatCard('Low Stock', '5', Icons.warning),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: _buildStatCard('Out of Stock', '2', Icons.error),
              ),
            ],
          ),
          
          const SizedBox(height: 24),
          
          // Products List
          Expanded(
            child: ListView(
              children: [
                _buildProductCard(
                  'Wireless Headphones',
                  '\$99.99',
                  '25 in stock',
                  'Active',
                  AppColors.success,
                ),
                const SizedBox(height: 12),
                _buildProductCard(
                  'Smart Watch',
                  '\$199.99',
                  '12 in stock',
                  'Active',
                  AppColors.success,
                ),
                const SizedBox(height: 12),
                _buildProductCard(
                  'Bluetooth Speaker',
                  '\$49.99',
                  '3 in stock',
                  'Low Stock',
                  AppColors.warning,
                ),
                const SizedBox(height: 12),
                _buildProductCard(
                  'Phone Case',
                  '\$24.99',
                  'Out of stock',
                  'Inactive',
                  AppColors.error,
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
      padding: const EdgeInsets.all(12),
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
              fontSize: 16,
              fontWeight: FontWeight.bold,
              color: AppColors.textPrimary,
            ),
          ),
          Text(
            title,
            style: const TextStyle(
              fontSize: 10,
              color: AppColors.textSecondary,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildProductCard(
    String name,
    String price,
    String stock,
    String status,
    Color statusColor,
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
          // Product Image Placeholder
          Container(
            width: 60,
            height: 60,
            decoration: BoxDecoration(
              color: AppColors.background,
              borderRadius: BorderRadius.circular(8),
            ),
            child: const Icon(
              Icons.image,
              color: AppColors.textSecondary,
              size: 32,
            ),
          ),
          const SizedBox(width: 16),
          
          // Product Info
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  name,
                  style: const TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.w600,
                    color: AppColors.textPrimary,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  price,
                  style: const TextStyle(
                    fontSize: 14,
                    fontWeight: FontWeight.w600,
                    color: AppColors.success,
                  ),
                ),
                Text(
                  stock,
                  style: const TextStyle(
                    fontSize: 12,
                    color: AppColors.textSecondary,
                  ),
                ),
              ],
            ),
          ),
          
          // Status and Actions
          Column(
            crossAxisAlignment: CrossAxisAlignment.end,
            children: [
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
              const SizedBox(height: 8),
              Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  CustomButton(
                    text: 'Edit',
                    onPressed: () {
                      // TODO: Navigate to edit
                    },
                    type: ButtonType.secondary,
                    width: 50,
                    height: 28,
                  ),
                ],
              ),
            ],
          ),
        ],
      ),
    );
  }
}

class OrdersTab extends StatelessWidget {
  const OrdersTab({super.key});

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Orders',
            style: TextStyle(
              fontSize: 20,
              fontWeight: FontWeight.bold,
              color: AppColors.textPrimary,
            ),
          ),
          const SizedBox(height: 16),
          
          // Order Stats
          Row(
            children: [
              Expanded(
                child: _buildStatCard('Total Orders', '127', Icons.shopping_cart),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: _buildStatCard('Pending', '8', Icons.pending),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: _buildStatCard('Completed', '115', Icons.check_circle),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: _buildStatCard('Cancelled', '4', Icons.cancel),
              ),
            ],
          ),
          
          const SizedBox(height: 24),
          
          // Orders List
          Expanded(
            child: ListView(
              children: [
                _buildOrderCard(
                  '#1001',
                  'John Doe',
                  '\$199.98',
                  'Pending',
                  AppColors.warning,
                  '2 items',
                ),
                const SizedBox(height: 12),
                _buildOrderCard(
                  '#1002',
                  'Jane Smith',
                  '\$49.99',
                  'Completed',
                  AppColors.success,
                  '1 item',
                ),
                const SizedBox(height: 12),
                _buildOrderCard(
                  '#1003',
                  'Mike Johnson',
                  '\$149.97',
                  'Shipped',
                  AppColors.info,
                  '3 items',
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
      padding: const EdgeInsets.all(12),
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
              fontSize: 16,
              fontWeight: FontWeight.bold,
              color: AppColors.textPrimary,
            ),
          ),
          Text(
            title,
            style: const TextStyle(
              fontSize: 10,
              color: AppColors.textSecondary,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildOrderCard(
    String orderNumber,
    String customer,
    String amount,
    String status,
    Color statusColor,
    String items,
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
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    Text(
                      orderNumber,
                      style: const TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.w600,
                        color: AppColors.textPrimary,
                      ),
                    ),
                    const SizedBox(width: 8),
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
                const SizedBox(height: 4),
                Text(
                  customer,
                  style: const TextStyle(
                    fontSize: 14,
                    color: AppColors.textSecondary,
                  ),
                ),
                Text(
                  items,
                  style: const TextStyle(
                    fontSize: 12,
                    color: AppColors.textSecondary,
                  ),
                ),
              ],
            ),
          ),
          Column(
            crossAxisAlignment: CrossAxisAlignment.end,
            children: [
              Text(
                amount,
                style: const TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.w600,
                  color: AppColors.success,
                ),
              ),
              const SizedBox(height: 8),
              CustomButton(
                text: 'View',
                onPressed: () {
                  // TODO: View order details
                },
                type: ButtonType.secondary,
                width: 60,
                height: 28,
              ),
            ],
          ),
        ],
      ),
    );
  }
}

class StoreTab extends StatelessWidget {
  const StoreTab({super.key});

  @override
  Widget build(BuildContext context) {
    return const Center(
      child: Text(
        'Store Settings\n(Coming Soon)',
        textAlign: TextAlign.center,
        style: TextStyle(
          color: AppColors.textSecondary,
          fontSize: 16,
        ),
      ),
    );
  }
}

class StoreAnalyticsTab extends StatelessWidget {
  const StoreAnalyticsTab({super.key});

  @override
  Widget build(BuildContext context) {
    return const Center(
      child: Text(
        'Store Analytics\n(Coming Soon)',
        textAlign: TextAlign.center,
        style: TextStyle(
          color: AppColors.textSecondary,
          fontSize: 16,
        ),
      ),
    );
  }
}