import React from 'react';
import { Routes, Route, Navigate } from 'react-router-dom';
import { useTheme } from './contexts/ThemeContext';
import { useAuth } from './contexts/AuthContext';
import { NotificationProvider } from './contexts/NotificationContext';

// Public pages
import LandingPage from './pages/LandingPage';
import AboutPage from './pages/AboutPage';
import ContactUsPage from './pages/ContactUsPage';
import LoginPage from './pages/auth/LoginPage';
import RegisterPage from './pages/auth/RegisterPage';
import ForgotPasswordPage from './pages/auth/ForgotPasswordPage';
import OnboardingWizard from './pages/OnboardingWizard';
import NotFoundPage from './pages/NotFoundPage';

// Legal pages
import TermsOfServicePage from './pages/legal/TermsOfServicePage';
import PrivacyPolicyPage from './pages/legal/PrivacyPolicyPage';

// Protected pages
import DashboardLayout from './layouts/DashboardLayout';
import DashboardHome from './pages/dashboard/DashboardHome';
import SocialMediaPage from './pages/dashboard/SocialMediaPage';
import EcommercePage from './pages/dashboard/EcommercePage';
import CoursesPage from './pages/dashboard/CoursesPage';
import CRMPage from './pages/dashboard/CRMPage';
import AnalyticsPage from './pages/dashboard/AnalyticsPage';
import BioSitesPage from './pages/dashboard/BioSitesPage';
import EmailMarketingPage from './pages/dashboard/EmailMarketingPage';
import PaymentsPage from './pages/dashboard/PaymentsPage';
import AIFeaturesPageV2 from './pages/dashboard/AIFeaturesPageV2';
import WorkspacePage from './pages/dashboard/WorkspacePage';
import WebsiteBuilderPage from './pages/dashboard/WebsiteBuilderPage';
import AdminDashboard from './pages/dashboard/AdminDashboard';
import AdvancedBookingPage from './pages/dashboard/AdvancedBookingPage';
import FinancialManagementPage from './pages/dashboard/FinancialManagementPage';
import EscrowSystemPage from './pages/dashboard/EscrowSystemPage';
import RealtimeCollaborationPage from './pages/dashboard/RealtimeCollaborationPage';
import IntegrationHubPage from './pages/dashboard/IntegrationHubPage';
import ReferralSystemPage from './pages/dashboard/ReferralSystemPage';
import UserSettingsPage from './pages/dashboard/UserSettingsPage';

// New pages
import LinkShortenerPage from './pages/dashboard/LinkShortenerPage';
import TeamManagementPage from './pages/dashboard/TeamManagementPage';
import FormTemplatesPage from './pages/dashboard/FormTemplatesPage';
import DiscountCodesPage from './pages/dashboard/DiscountCodesPage';

// Components
import ProtectedRoute from './components/ProtectedRoute';
import AdminRoute from './components/AdminRoute';
import LoadingSpinner from './components/LoadingSpinner';
import AdvancedAIFeatures from './components/ai/AdvancedAIFeatures';
import WorkspaceManager from './components/workspace/WorkspaceManager';
import SubscriptionManager from './components/subscription/SubscriptionManager';
import TemplateMarketplace from './components/templates/TemplateMarketplace';
import AdvancedAnalytics from './components/analytics/AdvancedAnalytics';
import RealtimeCollaboration from './components/realtime/RealtimeCollaboration';
import IntegrationHub from './components/integrations/IntegrationHub';
import ReferralSystem from './components/growth/ReferralSystem';
import ErrorBoundary from './components/ErrorBoundary';

function App() {
  const { theme } = useTheme();
  const { loading } = useAuth();

  if (loading) {
    return (
      <div className="min-h-screen bg-app flex items-center justify-center">
        <LoadingSpinner />
      </div>
    );
  }

  return (
    <div className={theme} data-theme={theme}>
      <ErrorBoundary>
        <NotificationProvider>
          <Routes>
            {/* Public Routes */}
            <Route path="/" element={<LandingPage />} />
            <Route path="/about" element={<AboutPage />} />
            <Route path="/contact" element={<ContactUsPage />} />
            <Route path="/login" element={<LoginPage />} />
            <Route path="/register" element={<RegisterPage />} />
            <Route path="/forgot-password" element={<ForgotPasswordPage />} />
            
            {/* Legal Pages */}
            <Route path="/terms-of-service" element={<TermsOfServicePage />} />
            <Route path="/privacy-policy" element={<PrivacyPolicyPage />} />
            
            {/* Protected Dashboard Routes */}
            <Route path="/dashboard" element={<ProtectedRoute><DashboardLayout /></ProtectedRoute>}>
              <Route index element={<DashboardHome />} />
              <Route path="social-media" element={<SocialMediaPage />} />
              <Route path="bio-sites" element={<BioSitesPage />} />
              <Route path="crm" element={<CRMPage />} />
              <Route path="link-shortener" element={<LinkShortenerPage />} />
              <Route path="referrals" element={<ReferralSystemPage />} />
              
              <Route path="website-builder" element={<WebsiteBuilderPage />} />
              <Route path="team-management" element={<TeamManagementPage />} />
              <Route path="form-templates" element={<FormTemplatesPage />} />
              <Route path="discount-codes" element={<DiscountCodesPage />} />
              <Route path="financial-management" element={<FinancialManagementPage />} />
              
              <Route path="courses" element={<CoursesPage />} />
              <Route path="ecommerce" element={<EcommercePage />} />
              <Route path="templates" element={<TemplateMarketplace />} />
              <Route path="escrow-system" element={<EscrowSystemPage />} />
              <Route path="analytics" element={<AdvancedAnalytics />} />
              
              <Route path="ai-features" element={<AdvancedAIFeatures />} />
              <Route path="email-marketing" element={<EmailMarketingPage />} />
              <Route path="advanced-booking" element={<AdvancedBookingPage />} />
              <Route path="realtime-collaboration" element={<RealtimeCollaborationPage />} />
              <Route path="integrations" element={<IntegrationHubPage />} />
              <Route path="workspaces" element={<WorkspaceManager />} />
              <Route path="subscription" element={<SubscriptionManager />} />
              <Route path="settings" element={<UserSettingsPage />} />
              
              {/* Legacy routes for backward compatibility */}
              <Route path="workspace" element={<WorkspacePage />} />
              <Route path="payments" element={<PaymentsPage />} />
              <Route path="advanced-analytics" element={<AnalyticsPage />} />
              
              {/* Admin Routes */}
              <Route path="admin" element={<AdminDashboard />} />
              <Route path="admin/users" element={<div className="p-8 text-center">User management coming soon...</div>} />
              <Route path="admin/system" element={<div className="p-8 text-center">System settings coming soon...</div>} />
              <Route path="admin/security" element={<div className="p-8 text-center">Security center coming soon...</div>} />
            </Route>

            {/* 404 Page */}
            <Route path="/404" element={<NotFoundPage />} />
            
            {/* Catch all - redirect to 404 */}
            <Route path="*" element={<Navigate to="/404" replace />} />
          </Routes>
        </NotificationProvider>
      </ErrorBoundary>
    </div>
  );
}

export default App;