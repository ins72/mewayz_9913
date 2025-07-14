import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/auth_provider.dart';
import '../../config/colors.dart';
import '../../widgets/quick_action_card.dart';
import '../../widgets/stats_card.dart';
import '../../widgets/recent_activity_card.dart';
import '../../widgets/custom_app_bar.dart';

class DashboardScreen extends StatefulWidget {
  const DashboardScreen({super.key});

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> with TickerProviderStateMixin {
  late AnimationController _animationController;
  late List<AnimationController> _cardAnimationControllers;
  late List<Animation<Offset>> _slideAnimations;
  late List<Animation<double>> _fadeAnimations;

  @override
  void initState() {
    super.initState();
    _setupAnimations();
    _animationController.forward();
  }

  void _setupAnimations() {
    _animationController = AnimationController(
      duration: const Duration(milliseconds: 1200),
      vsync: this,
    );

    // Create individual animation controllers for staggered effect
    _cardAnimationControllers = List.generate(
      8, // Number of cards
      (index) => AnimationController(
        duration: Duration(milliseconds: 800 + (index * 100)),
        vsync: this,
      ),
    );

    _slideAnimations = _cardAnimationControllers.map((controller) {
      return Tween<Offset>(
        begin: const Offset(0, 0.5),
        end: Offset.zero,
      ).animate(CurvedAnimation(
        parent: controller,
        curve: Curves.easeOutCubic,
      ));
    }).toList();

    _fadeAnimations = _cardAnimationControllers.map((controller) {
      return Tween<double>(
        begin: 0.0,
        end: 1.0,
      ).animate(CurvedAnimation(
        parent: controller,
        curve: Curves.easeIn,
      ));
    }).toList();

    // Start animations with stagger
    for (int i = 0; i < _cardAnimationControllers.length; i++) {
      Future.delayed(Duration(milliseconds: i * 150), () {
        if (mounted) {
          _cardAnimationControllers[i].forward();
        }
      });
    }
  }

  @override
  void dispose() {
    _animationController.dispose();
    for (var controller in _cardAnimationControllers) {
      controller.dispose();
    }
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      body: SafeArea(
        child: Column(
          children: [
            // Custom App Bar
            const CustomAppBar(
              title: 'Dashboard',
              showBackButton: false,
            ),
            
            // Main Content
            Expanded(
              child: RefreshIndicator(
                onRefresh: () async {
                  // TODO: Implement refresh logic
                  await Future.delayed(const Duration(seconds: 1));
                },
                color: AppColors.primary,
                backgroundColor: AppColors.surface,
                child: SingleChildScrollView(
                  padding: const EdgeInsets.all(16),
                  physics: const AlwaysScrollableScrollPhysics(),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // Welcome Section
                      _buildWelcomeSection(),
                      
                      const SizedBox(height: 24),
                      
                      // Stats Cards
                      _buildStatsSection(),
                      
                      const SizedBox(height: 24),
                      
                      // Quick Actions
                      _buildQuickActionsSection(),
                      
                      const SizedBox(height: 24),
                      
                      // Recent Activity
                      _buildRecentActivitySection(),
                      
                      const SizedBox(height: 24),
                    ],
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildWelcomeSection() {
    return Consumer<AuthProvider>(
      builder: (context, authProvider, _) {
        return FadeTransition(
          opacity: _fadeAnimations[0],
          child: SlideTransition(
            position: _slideAnimations[0],
            child: Container(
              padding: const EdgeInsets.all(20),
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
              child: Row(
                children: [
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'Welcome back,',
                          style: TextStyle(
                            fontSize: 14,
                            color: AppColors.textSecondary,
                          ),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          authProvider.user?.name ?? 'User',
                          style: TextStyle(
                            fontSize: 24,
                            fontWeight: FontWeight.bold,
                            color: AppColors.textPrimary,
                          ),
                        ),
                        const SizedBox(height: 8),
                        Text(
                          'Ready to grow your business today?',
                          style: TextStyle(
                            fontSize: 14,
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
              ),
            ),
          ),
        );
      },
    );
  }

  Widget _buildStatsSection() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          'Overview',
          style: TextStyle(
            fontSize: 18,
            fontWeight: FontWeight.bold,
            color: AppColors.textPrimary,
          ),
        ),
        const SizedBox(height: 16),
        Row(
          children: [
            Expanded(
              child: FadeTransition(
                opacity: _fadeAnimations[1],
                child: SlideTransition(
                  position: _slideAnimations[1],
                  child: const StatsCard(
                    title: 'Total Views',
                    value: '12.4K',
                    change: '+12%',
                    isPositive: true,
                    icon: Icons.visibility,
                    color: Color(0xFF4ECDC4),
                  ),
                ),
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: FadeTransition(
                opacity: _fadeAnimations[2],
                child: SlideTransition(
                  position: _slideAnimations[2],
                  child: const StatsCard(
                    title: 'Link Clicks',
                    value: '3.2K',
                    change: '+8%',
                    isPositive: true,
                    icon: Icons.mouse,
                    color: Color(0xFF45B7D1),
                  ),
                ),
              ),
            ),
          ],
        ),
        const SizedBox(height: 12),
        Row(
          children: [
            Expanded(
              child: FadeTransition(
                opacity: _fadeAnimations[3],
                child: SlideTransition(
                  position: _slideAnimations[3],
                  child: const StatsCard(
                    title: 'Social Posts',
                    value: '24',
                    change: '+4',
                    isPositive: true,
                    icon: Icons.share,
                    color: Color(0xFFF9CA24),
                  ),
                ),
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: FadeTransition(
                opacity: _fadeAnimations[4],
                child: SlideTransition(
                  position: _slideAnimations[4],
                  child: const StatsCard(
                    title: 'Revenue',
                    value: '\$2.1K',
                    change: '+15%',
                    isPositive: true,
                    icon: Icons.trending_up,
                    color: Color(0xFF26DE81),
                  ),
                ),
              ),
            ),
          ],
        ),
      ],
    );
  }

  Widget _buildQuickActionsSection() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          'Quick Actions',
          style: TextStyle(
            fontSize: 18,
            fontWeight: FontWeight.bold,
            color: AppColors.textPrimary,
          ),
        ),
        const SizedBox(height: 16),
        GridView.count(
          shrinkWrap: true,
          physics: const NeverScrollableScrollPhysics(),
          crossAxisCount: 2,
          crossAxisSpacing: 12,
          mainAxisSpacing: 12,
          childAspectRatio: 1.5,
          children: [
            FadeTransition(
              opacity: _fadeAnimations[5],
              child: SlideTransition(
                position: _slideAnimations[5],
                child: const QuickActionCard(
                  title: 'Bio Sites',
                  subtitle: 'Manage links',
                  icon: Icons.link,
                  color: Color(0xFF6C5CE7),
                  route: '/bio-sites',
                ),
              ),
            ),
            FadeTransition(
              opacity: _fadeAnimations[6],
              child: SlideTransition(
                position: _slideAnimations[6],
                child: const QuickActionCard(
                  title: 'Social Media',
                  subtitle: 'Schedule posts',
                  icon: Icons.share,
                  color: Color(0xFF4ECDC4),
                  route: '/social-media',
                ),
              ),
            ),
            FadeTransition(
              opacity: _fadeAnimations[7],
              child: SlideTransition(
                position: _slideAnimations[7],
                child: const QuickActionCard(
                  title: 'Analytics',
                  subtitle: 'View insights',
                  icon: Icons.analytics,
                  color: Color(0xFF45B7D1),
                  route: '/analytics',
                ),
              ),
            ),
            FadeTransition(
              opacity: _fadeAnimations[5],
              child: SlideTransition(
                position: _slideAnimations[5],
                child: const QuickActionCard(
                  title: 'Settings',
                  subtitle: 'Preferences',
                  icon: Icons.settings,
                  color: Color(0xFF7B7B7B),
                  route: '/settings',
                ),
              ),
            ),
          ],
        ),
      ],
    );
  }

  Widget _buildRecentActivitySection() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Text(
              'Recent Activity',
              style: TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.bold,
                color: AppColors.textPrimary,
              ),
            ),
            TextButton(
              onPressed: () {
                // TODO: Navigate to full activity list
              },
              child: Text(
                'View All',
                style: TextStyle(
                  color: AppColors.primary,
                  fontWeight: FontWeight.w500,
                ),
              ),
            ),
          ],
        ),
        const SizedBox(height: 16),
        FadeTransition(
          opacity: _fadeAnimations[6],
          child: SlideTransition(
            position: _slideAnimations[6],
            child: const RecentActivityCard(),
          ),
        ),
      ],
    );
  }
}