import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../config/colors.dart';
import '../../utils/responsive_layout.dart';
import '../../widgets/cards/feature_card.dart';
import '../../widgets/cards/pricing_card.dart';
import '../../widgets/cards/demo_card.dart';

class LandingScreen extends StatefulWidget {
  const LandingScreen({super.key});

  @override
  State<LandingScreen> createState() => _LandingScreenState();
}

class _LandingScreenState extends State<LandingScreen>
    with SingleTickerProviderStateMixin {
  late AnimationController _animationController;
  late Animation<double> _fadeAnimation;
  late Animation<Offset> _slideAnimation;

  @override
  void initState() {
    super.initState();
    _animationController = AnimationController(
      duration: const Duration(milliseconds: 1500),
      vsync: this,
    );
    _fadeAnimation = Tween<double>(
      begin: 0.0,
      end: 1.0,
    ).animate(CurvedAnimation(
      parent: _animationController,
      curve: Curves.easeInOut,
    ));
    _slideAnimation = Tween<Offset>(
      begin: const Offset(0, 0.5),
      end: Offset.zero,
    ).animate(CurvedAnimation(
      parent: _animationController,
      curve: Curves.easeOutCubic,
    ));
    _animationController.forward();
  }

  @override
  void dispose() {
    _animationController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      body: SingleChildScrollView(
        child: Column(
          children: [
            _buildHeader(),
            _buildHeroSection(),
            _buildFeaturesSection(),
            _buildDemoSection(),
            _buildStatsSection(),
            _buildPricingSection(),
            _buildCTASection(),
            _buildFooter(),
          ],
        ),
      ),
    );
  }

  Widget _buildHeader() {
    return Container(
      padding: EdgeInsets.symmetric(
        horizontal: ResponsiveHelper.getSidePadding(context),
        vertical: 16,
      ),
      decoration: const BoxDecoration(
        color: AppColors.surface,
        border: Border(
          bottom: BorderSide(color: AppColors.border),
        ),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Row(
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
                  fontSize: 20,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ],
          ),
          Row(
            children: [
              TextButton(
                onPressed: () => context.go('/login'),
                child: const Text(
                  'Login',
                  style: TextStyle(color: AppColors.textSecondary),
                ),
              ),
              const SizedBox(width: 8),
              ElevatedButton(
                onPressed: () => context.go('/register'),
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppColors.primary,
                  foregroundColor: Colors.white,
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(8),
                  ),
                ),
                child: const Text('Get Started'),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildHeroSection() {
    return Container(
      padding: EdgeInsets.symmetric(
        horizontal: ResponsiveHelper.getSidePadding(context),
        vertical: ResponsiveHelper.isDesktop(context) ? 80 : 60,
      ),
      child: FadeTransition(
        opacity: _fadeAnimation,
        child: SlideTransition(
          position: _slideAnimation,
          child: ResponsiveLayout(
            mobile: _buildMobileHero(),
            tablet: _buildTabletHero(),
            desktop: _buildDesktopHero(),
          ),
        ),
      ),
    );
  }

  Widget _buildMobileHero() {
    return Column(
      children: [
        _buildHeroContent(),
        const SizedBox(height: 40),
        _buildHeroDemo(),
      ],
    );
  }

  Widget _buildTabletHero() {
    return Column(
      children: [
        _buildHeroContent(),
        const SizedBox(height: 50),
        _buildHeroDemo(),
      ],
    );
  }

  Widget _buildDesktopHero() {
    return Row(
      children: [
        Expanded(
          flex: 1,
          child: _buildHeroContent(),
        ),
        const SizedBox(width: 60),
        Expanded(
          flex: 1,
          child: _buildHeroDemo(),
        ),
      ],
    );
  }

  Widget _buildHeroContent() {
    return Column(
      crossAxisAlignment: ResponsiveHelper.isDesktop(context) 
          ? CrossAxisAlignment.start 
          : CrossAxisAlignment.center,
      children: [
        RichText(
          textAlign: ResponsiveHelper.isDesktop(context) 
              ? TextAlign.start 
              : TextAlign.center,
          text: TextSpan(
            style: TextStyle(
              fontSize: ResponsiveHelper.isDesktop(context) ? 48 : 36,
              fontWeight: FontWeight.bold,
              color: AppColors.textPrimary,
              height: 1.2,
            ),
            children: [
              const TextSpan(text: 'All-in-One Business Platform for '),
              TextSpan(
                text: 'Modern Creators',
                style: TextStyle(
                  background: Paint()
                    ..shader = const LinearGradient(
                      colors: [AppColors.primary, Color(0xFF45B7D1)],
                    ).createShader(const Rect.fromLTWH(0, 0, 200, 70)),
                ),
              ),
            ],
          ),
        ),
        const SizedBox(height: 20),
        Text(
          'Manage your social media, create bio sites, track leads, run email campaigns, sell products, and create courses - all from one powerful platform.',
          style: TextStyle(
            fontSize: ResponsiveHelper.isDesktop(context) ? 18 : 16,
            color: AppColors.textSecondary,
            height: 1.5,
          ),
          textAlign: ResponsiveHelper.isDesktop(context) 
              ? TextAlign.start 
              : TextAlign.center,
        ),
        const SizedBox(height: 32),
        ResponsiveLayout(
          mobile: Column(
            children: [
              _buildPrimaryButton(),
              const SizedBox(height: 16),
              _buildSecondaryButton(),
            ],
          ),
          tablet: Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              _buildPrimaryButton(),
              const SizedBox(width: 16),
              _buildSecondaryButton(),
            ],
          ),
          desktop: Row(
            children: [
              _buildPrimaryButton(),
              const SizedBox(width: 16),
              _buildSecondaryButton(),
            ],
          ),
        ),
        const SizedBox(height: 40),
        _buildStatsRow(),
      ],
    );
  }

  Widget _buildPrimaryButton() {
    return ElevatedButton(
      onPressed: () => context.go('/register'),
      style: ElevatedButton.styleFrom(
        backgroundColor: AppColors.primary,
        foregroundColor: Colors.white,
        padding: const EdgeInsets.symmetric(horizontal: 32, vertical: 16),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(12),
        ),
      ),
      child: const Text(
        'Start Free Trial',
        style: TextStyle(
          fontSize: 16,
          fontWeight: FontWeight.w600,
        ),
      ),
    );
  }

  Widget _buildSecondaryButton() {
    return OutlinedButton(
      onPressed: () {
        // Scroll to demo section
      },
      style: OutlinedButton.styleFrom(
        foregroundColor: AppColors.textPrimary,
        side: const BorderSide(color: AppColors.border),
        padding: const EdgeInsets.symmetric(horizontal: 32, vertical: 16),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(12),
        ),
      ),
      child: const Text(
        'Explore Features',
        style: TextStyle(
          fontSize: 16,
          fontWeight: FontWeight.w600,
        ),
      ),
    );
  }

  Widget _buildStatsRow() {
    return ResponsiveLayout(
      mobile: Row(
        mainAxisAlignment: MainAxisAlignment.spaceEvenly,
        children: [
          _buildStatItem('15+', 'Integrated Tools'),
          _buildStatItem('99.9%', 'Uptime'),
          _buildStatItem('24/7', 'Support'),
        ],
      ),
      tablet: Row(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          _buildStatItem('15+', 'Integrated Tools'),
          const SizedBox(width: 40),
          _buildStatItem('99.9%', 'Uptime'),
          const SizedBox(width: 40),
          _buildStatItem('24/7', 'Support'),
        ],
      ),
      desktop: Row(
        mainAxisAlignment: ResponsiveHelper.isDesktop(context) 
            ? MainAxisAlignment.start 
            : MainAxisAlignment.center,
        children: [
          _buildStatItem('15+', 'Integrated Tools'),
          const SizedBox(width: 40),
          _buildStatItem('99.9%', 'Uptime'),
          const SizedBox(width: 40),
          _buildStatItem('24/7', 'Support'),
        ],
      ),
    );
  }

  Widget _buildStatItem(String value, String label) {
    return Column(
      children: [
        ShaderMask(
          shaderCallback: (bounds) => const LinearGradient(
            colors: [AppColors.primary, Color(0xFF45B7D1)],
          ).createShader(bounds),
          child: Text(
            value,
            style: const TextStyle(
              fontSize: 24,
              fontWeight: FontWeight.bold,
              color: Colors.white,
            ),
          ),
        ),
        const SizedBox(height: 4),
        Text(
          label,
          style: const TextStyle(
            fontSize: 12,
            color: AppColors.textSecondary,
          ),
        ),
      ],
    );
  }

  Widget _buildHeroDemo() {
    return Container(
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
        border: Border.all(
          color: AppColors.primary.withOpacity(0.2),
        ),
      ),
      child: const DemoCard(
        title: 'Dashboard Preview',
        isAnimated: true,
      ),
    );
  }

  Widget _buildFeaturesSection() {
    return Container(
      padding: EdgeInsets.symmetric(
        horizontal: ResponsiveHelper.getSidePadding(context),
        vertical: 80,
      ),
      color: AppColors.surface,
      child: Column(
        children: [
          const Text(
            'Everything You Need to Grow Your Business',
            style: TextStyle(
              fontSize: 32,
              fontWeight: FontWeight.bold,
              color: AppColors.textPrimary,
            ),
            textAlign: TextAlign.center,
          ),
          const SizedBox(height: 16),
          const Text(
            'Powerful tools designed for modern creators and entrepreneurs',
            style: TextStyle(
              fontSize: 18,
              color: AppColors.textSecondary,
            ),
            textAlign: TextAlign.center,
          ),
          const SizedBox(height: 60),
          GridView.builder(
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
              crossAxisCount: ResponsiveHelper.isDesktop(context) ? 3 : 2,
              childAspectRatio: 1.1,
              crossAxisSpacing: 24,
              mainAxisSpacing: 24,
            ),
            itemCount: _features.length,
            itemBuilder: (context, index) {
              final feature = _features[index];
              return FeatureCard(
                icon: feature.icon,
                title: feature.title,
                description: feature.description,
                features: feature.features,
                color: feature.color,
              );
            },
          ),
        ],
      ),
    );
  }

  Widget _buildDemoSection() {
    return Container(
      padding: EdgeInsets.symmetric(
        horizontal: ResponsiveHelper.getSidePadding(context),
        vertical: 80,
      ),
      child: Column(
        children: [
          const Text(
            'See Mewayz in Action',
            style: TextStyle(
              fontSize: 32,
              fontWeight: FontWeight.bold,
              color: AppColors.textPrimary,
            ),
            textAlign: TextAlign.center,
          ),
          const SizedBox(height: 16),
          const Text(
            'Experience the power of our all-in-one platform',
            style: TextStyle(
              fontSize: 18,
              color: AppColors.textSecondary,
            ),
            textAlign: TextAlign.center,
          ),
          const SizedBox(height: 60),
          ResponsiveLayout(
            mobile: Column(
              children: [
                _buildDemoItem('Mobile Application', 'Native mobile experience optimized for creators on the go.', '/login'),
                const SizedBox(height: 40),
                _buildDemoItem('Web Dashboard', 'Full-featured desktop experience with advanced analytics.', 'https://25e964ca-2888-4e35-ab3a-9bbceddc4571.preview.emergentagent.com/'),
              ],
            ),
            tablet: Row(
              children: [
                Expanded(
                  child: _buildDemoItem('Mobile Application', 'Native mobile experience optimized for creators on the go.', '/login'),
                ),
                const SizedBox(width: 40),
                Expanded(
                  child: _buildDemoItem('Web Dashboard', 'Full-featured desktop experience with advanced analytics.', 'https://25e964ca-2888-4e35-ab3a-9bbceddc4571.preview.emergentagent.com/'),
                ),
              ],
            ),
            desktop: Row(
              children: [
                Expanded(
                  child: _buildDemoItem('Mobile Application', 'Native mobile experience optimized for creators on the go.', '/login'),
                ),
                const SizedBox(width: 60),
                Expanded(
                  child: _buildDemoItem('Web Dashboard', 'Full-featured desktop experience with advanced analytics.', 'https://25e964ca-2888-4e35-ab3a-9bbceddc4571.preview.emergentagent.com/'),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildDemoItem(String title, String description, String route) {
    return Column(
      children: [
        Text(
          title,
          style: const TextStyle(
            fontSize: 24,
            fontWeight: FontWeight.bold,
            color: AppColors.textPrimary,
          ),
          textAlign: TextAlign.center,
        ),
        const SizedBox(height: 16),
        Text(
          description,
          style: const TextStyle(
            fontSize: 16,
            color: AppColors.textSecondary,
          ),
          textAlign: TextAlign.center,
        ),
        const SizedBox(height: 24),
        const DemoCard(
          title: 'Preview',
          isAnimated: false,
        ),
        const SizedBox(height: 24),
        ElevatedButton(
          onPressed: () => context.go(route),
          style: ElevatedButton.styleFrom(
            backgroundColor: AppColors.primary,
            foregroundColor: Colors.white,
            padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(8),
            ),
          ),
          child: const Text('Try Now'),
        ),
      ],
    );
  }

  Widget _buildStatsSection() {
    return Container(
      padding: EdgeInsets.symmetric(
        horizontal: ResponsiveHelper.getSidePadding(context),
        vertical: 60,
      ),
      color: AppColors.surface,
      child: ResponsiveLayout(
        mobile: Column(
          children: [
            _buildStatCard('50K+', 'Active Users', Icons.people),
            const SizedBox(height: 24),
            _buildStatCard('1M+', 'Links Created', Icons.link),
            const SizedBox(height: 24),
            _buildStatCard('99.9%', 'Uptime', Icons.speed),
            const SizedBox(height: 24),
            _buildStatCard('24/7', 'Support', Icons.support),
          ],
        ),
        tablet: Row(
          children: [
            Expanded(child: _buildStatCard('50K+', 'Active Users', Icons.people)),
            const SizedBox(width: 24),
            Expanded(child: _buildStatCard('1M+', 'Links Created', Icons.link)),
            const SizedBox(width: 24),
            Expanded(child: _buildStatCard('99.9%', 'Uptime', Icons.speed)),
            const SizedBox(width: 24),
            Expanded(child: _buildStatCard('24/7', 'Support', Icons.support)),
          ],
        ),
        desktop: Row(
          children: [
            Expanded(child: _buildStatCard('50K+', 'Active Users', Icons.people)),
            const SizedBox(width: 32),
            Expanded(child: _buildStatCard('1M+', 'Links Created', Icons.link)),
            const SizedBox(width: 32),
            Expanded(child: _buildStatCard('99.9%', 'Uptime', Icons.speed)),
            const SizedBox(width: 32),
            Expanded(child: _buildStatCard('24/7', 'Support', Icons.support)),
          ],
        ),
      ),
    );
  }

  Widget _buildStatCard(String value, String label, IconData icon) {
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: AppColors.background,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: AppColors.border),
      ),
      child: Column(
        children: [
          Icon(
            icon,
            size: 32,
            color: AppColors.primary,
          ),
          const SizedBox(height: 16),
          ShaderMask(
            shaderCallback: (bounds) => const LinearGradient(
              colors: [AppColors.primary, Color(0xFF45B7D1)],
            ).createShader(bounds),
            child: Text(
              value,
              style: const TextStyle(
                fontSize: 28,
                fontWeight: FontWeight.bold,
                color: Colors.white,
              ),
            ),
          ),
          const SizedBox(height: 8),
          Text(
            label,
            style: const TextStyle(
              fontSize: 14,
              color: AppColors.textSecondary,
            ),
            textAlign: TextAlign.center,
          ),
        ],
      ),
    );
  }

  Widget _buildPricingSection() {
    return Container(
      padding: EdgeInsets.symmetric(
        horizontal: ResponsiveHelper.getSidePadding(context),
        vertical: 80,
      ),
      child: Column(
        children: [
          const Text(
            'Simple, Transparent Pricing',
            style: TextStyle(
              fontSize: 32,
              fontWeight: FontWeight.bold,
              color: AppColors.textPrimary,
            ),
            textAlign: TextAlign.center,
          ),
          const SizedBox(height: 16),
          const Text(
            'Choose the perfect plan for your business needs',
            style: TextStyle(
              fontSize: 18,
              color: AppColors.textSecondary,
            ),
            textAlign: TextAlign.center,
          ),
          const SizedBox(height: 60),
          ResponsiveLayout(
            mobile: Column(
              children: [
                PricingCard(
                  title: 'Starter',
                  price: '\$29',
                  period: '/month',
                  description: 'Perfect for individuals',
                  features: const [
                    '5 Bio Sites',
                    '3 Social Accounts',
                    'Basic Analytics',
                    'Email Support',
                  ],
                  isPopular: false,
                  onTap: () => context.go('/register'),
                ),
                const SizedBox(height: 24),
                PricingCard(
                  title: 'Professional',
                  price: '\$79',
                  period: '/month',
                  description: 'For growing businesses',
                  features: const [
                    '25 Bio Sites',
                    '10 Social Accounts',
                    'Advanced Analytics',
                    'Email Marketing',
                    'CRM & Leads',
                    'Priority Support',
                  ],
                  isPopular: true,
                  onTap: () => context.go('/register'),
                ),
                const SizedBox(height: 24),
                PricingCard(
                  title: 'Enterprise',
                  price: '\$199',
                  period: '/month',
                  description: 'For large organizations',
                  features: const [
                    'Unlimited Bio Sites',
                    'Unlimited Social Accounts',
                    'Custom Analytics',
                    'White-label Options',
                    'API Access',
                    '24/7 Support',
                  ],
                  isPopular: false,
                  onTap: () => context.go('/register'),
                ),
              ],
            ),
            tablet: Row(
              children: [
                Expanded(
                  child: PricingCard(
                    title: 'Starter',
                    price: '\$29',
                    period: '/month',
                    description: 'Perfect for individuals',
                    features: const [
                      '5 Bio Sites',
                      '3 Social Accounts',
                      'Basic Analytics',
                      'Email Support',
                    ],
                    isPopular: false,
                    onTap: () => context.go('/register'),
                  ),
                ),
                const SizedBox(width: 24),
                Expanded(
                  child: PricingCard(
                    title: 'Professional',
                    price: '\$79',
                    period: '/month',
                    description: 'For growing businesses',
                    features: const [
                      '25 Bio Sites',
                      '10 Social Accounts',
                      'Advanced Analytics',
                      'Email Marketing',
                      'CRM & Leads',
                      'Priority Support',
                    ],
                    isPopular: true,
                    onTap: () => context.go('/register'),
                  ),
                ),
                const SizedBox(width: 24),
                Expanded(
                  child: PricingCard(
                    title: 'Enterprise',
                    price: '\$199',
                    period: '/month',
                    description: 'For large organizations',
                    features: const [
                      'Unlimited Bio Sites',
                      'Unlimited Social Accounts',
                      'Custom Analytics',
                      'White-label Options',
                      'API Access',
                      '24/7 Support',
                    ],
                    isPopular: false,
                    onTap: () => context.go('/register'),
                  ),
                ),
              ],
            ),
            desktop: Row(
              children: [
                Expanded(
                  child: PricingCard(
                    title: 'Starter',
                    price: '\$29',
                    period: '/month',
                    description: 'Perfect for individuals',
                    features: const [
                      '5 Bio Sites',
                      '3 Social Accounts',
                      'Basic Analytics',
                      'Email Support',
                    ],
                    isPopular: false,
                    onTap: () => context.go('/register'),
                  ),
                ),
                const SizedBox(width: 32),
                Expanded(
                  child: PricingCard(
                    title: 'Professional',
                    price: '\$79',
                    period: '/month',
                    description: 'For growing businesses',
                    features: const [
                      '25 Bio Sites',
                      '10 Social Accounts',
                      'Advanced Analytics',
                      'Email Marketing',
                      'CRM & Leads',
                      'Priority Support',
                    ],
                    isPopular: true,
                    onTap: () => context.go('/register'),
                  ),
                ),
                const SizedBox(width: 32),
                Expanded(
                  child: PricingCard(
                    title: 'Enterprise',
                    price: '\$199',
                    period: '/month',
                    description: 'For large organizations',
                    features: const [
                      'Unlimited Bio Sites',
                      'Unlimited Social Accounts',
                      'Custom Analytics',
                      'White-label Options',
                      'API Access',
                      '24/7 Support',
                    ],
                    isPopular: false,
                    onTap: () => context.go('/register'),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildCTASection() {
    return Container(
      padding: EdgeInsets.symmetric(
        horizontal: ResponsiveHelper.getSidePadding(context),
        vertical: 80,
      ),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [
            AppColors.primary.withOpacity(0.1),
            AppColors.primary.withOpacity(0.05),
          ],
        ),
      ),
      child: Column(
        children: [
          const Text(
            'Ready to Transform Your Business?',
            style: TextStyle(
              fontSize: 32,
              fontWeight: FontWeight.bold,
              color: AppColors.textPrimary,
            ),
            textAlign: TextAlign.center,
          ),
          const SizedBox(height: 16),
          const Text(
            'Join thousands of creators who trust Mewayz to grow their online presence',
            style: TextStyle(
              fontSize: 18,
              color: AppColors.textSecondary,
            ),
            textAlign: TextAlign.center,
          ),
          const SizedBox(height: 40),
          ResponsiveLayout(
            mobile: Column(
              children: [
                ElevatedButton(
                  onPressed: () => context.go('/register'),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppColors.primary,
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(horizontal: 40, vertical: 20),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                  ),
                  child: const Text(
                    'Start Free Trial',
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ),
                const SizedBox(height: 16),
                OutlinedButton(
                  onPressed: () {
                    // TODO: Book demo
                  },
                  style: OutlinedButton.styleFrom(
                    foregroundColor: AppColors.textPrimary,
                    side: const BorderSide(color: AppColors.primary),
                    padding: const EdgeInsets.symmetric(horizontal: 40, vertical: 20),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                  ),
                  child: const Text(
                    'Book a Demo',
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ),
              ],
            ),
            tablet: Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                ElevatedButton(
                  onPressed: () => context.go('/register'),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppColors.primary,
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(horizontal: 40, vertical: 20),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                  ),
                  child: const Text(
                    'Start Free Trial',
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ),
                const SizedBox(width: 24),
                OutlinedButton(
                  onPressed: () {
                    // TODO: Book demo
                  },
                  style: OutlinedButton.styleFrom(
                    foregroundColor: AppColors.textPrimary,
                    side: const BorderSide(color: AppColors.primary),
                    padding: const EdgeInsets.symmetric(horizontal: 40, vertical: 20),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                  ),
                  child: const Text(
                    'Book a Demo',
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ),
              ],
            ),
            desktop: Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                ElevatedButton(
                  onPressed: () => context.go('/register'),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppColors.primary,
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(horizontal: 40, vertical: 20),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                  ),
                  child: const Text(
                    'Start Free Trial',
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ),
                const SizedBox(width: 24),
                OutlinedButton(
                  onPressed: () {
                    // TODO: Book demo
                  },
                  style: OutlinedButton.styleFrom(
                    foregroundColor: AppColors.textPrimary,
                    side: const BorderSide(color: AppColors.primary),
                    padding: const EdgeInsets.symmetric(horizontal: 40, vertical: 20),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                  ),
                  child: const Text(
                    'Book a Demo',
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildFooter() {
    return Container(
      padding: EdgeInsets.symmetric(
        horizontal: ResponsiveHelper.getSidePadding(context),
        vertical: 40,
      ),
      decoration: const BoxDecoration(
        color: AppColors.surface,
        border: Border(
          top: BorderSide(color: AppColors.border),
        ),
      ),
      child: Column(
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Container(
                width: 32,
                height: 32,
                decoration: BoxDecoration(
                  color: AppColors.primary,
                  borderRadius: BorderRadius.circular(6),
                ),
                child: const Center(
                  child: Text(
                    'M',
                    style: TextStyle(
                      color: Colors.white,
                      fontSize: 16,
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
          const SizedBox(height: 16),
          const Text(
            'The all-in-one platform for modern creators and entrepreneurs.',
            style: TextStyle(
              color: AppColors.textSecondary,
              fontSize: 14,
            ),
            textAlign: TextAlign.center,
          ),
          const SizedBox(height: 24),
          const Text(
            '© 2025 Mewayz. All rights reserved.',
            style: TextStyle(
              color: AppColors.textSecondary,
              fontSize: 12,
            ),
            textAlign: TextAlign.center,
          ),
        ],
      ),
    );
  }
}

class _FeatureData {
  final String icon;
  final String title;
  final String description;
  final List<String> features;
  final Color color;

  _FeatureData({
    required this.icon,
    required this.title,
    required this.description,
    required this.features,
    required this.color,
  });
}

final List<_FeatureData> _features = [
  _FeatureData(
    icon: '📱',
    title: 'Social Media Management',
    description: 'Schedule posts, manage multiple accounts, and track engagement across all major platforms.',
    features: ['Multi-platform posting', 'Content scheduling', 'Analytics & insights'],
    color: AppColors.primary,
  ),
  _FeatureData(
    icon: '🔗',
    title: 'Link in Bio Builder',
    description: 'Create stunning bio pages with custom links, themes, and analytics tracking.',
    features: ['Custom themes', 'Click tracking', 'QR code generation'],
    color: const Color(0xFF45B7D1),
  ),
  _FeatureData(
    icon: '👥',
    title: 'CRM & Lead Management',
    description: 'Manage contacts, track leads, and nurture relationships with powerful CRM tools.',
    features: ['Contact management', 'Lead scoring', 'Pipeline tracking'],
    color: const Color(0xFF26DE81),
  ),
  _FeatureData(
    icon: '📧',
    title: 'Email Marketing',
    description: 'Create, send, and track email campaigns with beautiful templates and automation.',
    features: ['Campaign builder', 'Email templates', 'Automation workflows'],
    color: const Color(0xFFF9CA24),
  ),
  _FeatureData(
    icon: '🛒',
    title: 'E-commerce Store',
    description: 'Sell products directly through your bio pages with integrated payment processing.',
    features: ['Product catalog', 'Payment processing', 'Order management'],
    color: const Color(0xFFFF4757),
  ),
  _FeatureData(
    icon: '🎓',
    title: 'Course Creation',
    description: 'Create and sell online courses with video lessons, quizzes, and student tracking.',
    features: ['Video hosting', 'Student progress', 'Certificate generation'],
    color: const Color(0xFF6C5CE7),
  ),
];