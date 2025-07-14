import 'package:go_router/go_router.dart';
import 'package:flutter/material.dart';
import '../screens/auth/splash_screen.dart';
import '../screens/auth/login_screen.dart';
import '../screens/auth/register_screen.dart';
import '../screens/auth/forgot_password_screen.dart';
import '../screens/dashboard/enhanced_dashboard_screen.dart';
import '../screens/workspace/workspace_selector_screen.dart';
import '../screens/social_media/enhanced_social_media_screen.dart';
import '../screens/bio/enhanced_bio_sites_screen.dart';
import '../screens/crm/enhanced_crm_screen.dart';
import '../screens/email/email_marketing_campaign_screen.dart';
import '../screens/ecommerce/ecommerce_store_manager_screen.dart';
import '../screens/courses/course_creation_platform_screen.dart';
import '../screens/analytics/analytics_dashboard_screen.dart';
import '../screens/settings/settings_screen.dart';

class AppRouter {
  static final GoRouter router = GoRouter(
    initialLocation: '/splash',
    routes: [
      // Auth Routes
      GoRoute(
        path: '/splash',
        builder: (context, state) => const SplashScreen(),
      ),
      GoRoute(
        path: '/login',
        builder: (context, state) => const LoginScreen(),
      ),
      GoRoute(
        path: '/register',
        builder: (context, state) => const RegisterScreen(),
      ),
      GoRoute(
        path: '/forgot-password',
        builder: (context, state) => const ForgotPasswordScreen(),
      ),
      
      // Workspace Routes
      GoRoute(
        path: '/workspace-selector',
        builder: (context, state) => const WorkspaceSelectorScreen(),
      ),
      
      // Main App Routes
      GoRoute(
        path: '/dashboard',
        builder: (context, state) => const DashboardScreen(),
      ),
      GoRoute(
        path: '/social-media',
        builder: (context, state) => const SocialMediaManagerScreen(),
      ),
      GoRoute(
        path: '/link-in-bio',
        builder: (context, state) => const LinkInBioBuilderScreen(),
      ),
      GoRoute(
        path: '/crm',
        builder: (context, state) => const CrmLeadManagementScreen(),
      ),
      GoRoute(
        path: '/email-marketing',
        builder: (context, state) => const EmailMarketingCampaignScreen(),
      ),
      GoRoute(
        path: '/ecommerce',
        builder: (context, state) => const EcommerceStoreManagerScreen(),
      ),
      GoRoute(
        path: '/courses',
        builder: (context, state) => const CourseCreationPlatformScreen(),
      ),
      GoRoute(
        path: '/analytics',
        builder: (context, state) => const AnalyticsDashboardScreen(),
      ),
    ],
  );
}