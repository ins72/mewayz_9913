import React from 'react';
import { Routes, Route, Navigate } from 'react-router-dom';
import { useTheme } from './contexts/ThemeContext';
import { useAuth } from './contexts/AuthContext';

// Public pages
import LandingPage from './pages/LandingPage';
import AboutPage from './pages/AboutPage';
import LoginPage from './pages/auth/LoginPage';
import RegisterPage from './pages/auth/RegisterPage';
import ForgotPasswordPage from './pages/auth/ForgotPasswordPage';
import TermsOfServicePage from './pages/legal/TermsOfServicePage';
import PrivacyPolicyPage from './pages/legal/PrivacyPolicyPage';
import CookiePolicyPage from './pages/legal/CookiePolicyPage';
import RefundPolicyPage from './pages/legal/RefundPolicyPage';
import AccessibilityPage from './pages/legal/AccessibilityPage';

// Protected pages
import DashboardLayout from './layouts/DashboardLayout';
import DashboardHome from './pages/dashboard/DashboardHome';
import ProfilePage from './pages/dashboard/ProfilePage';
import SettingsPage from './pages/dashboard/SettingsPage';

// Business features
import SocialMediaPage from './pages/dashboard/SocialMediaPage';
import EcommercePage from './pages/dashboard/EcommercePage';
import CoursesPage from './pages/dashboard/CoursesPage';
import CRMPage from './pages/dashboard/CRMPage';
import AnalyticsPage from './pages/dashboard/AnalyticsPage';
import BioSitesPage from './pages/dashboard/BioSitesPage';
import EmailMarketingPage from './pages/dashboard/EmailMarketingPage';
import BookingPage from './pages/dashboard/BookingPage';
import PaymentsPage from './pages/dashboard/PaymentsPage';
import WebsiteBuilderPage from './pages/dashboard/WebsiteBuilderPage';
import AIFeaturesPage from './pages/dashboard/AIFeaturesPage';
import TeamManagementPage from './pages/dashboard/TeamManagementPage';
import WorkspacePage from './pages/dashboard/WorkspacePage';

// Admin pages
import AdminDashboard from './pages/admin/AdminDashboard';
import AdminUsers from './pages/admin/AdminUsers';
import AdminSettings from './pages/admin/AdminSettings';

// Components
import ProtectedRoute from './components/ProtectedRoute';
import AdminRoute from './components/AdminRoute';
import LoadingSpinner from './components/LoadingSpinner';

function App() {
  const { theme } = useTheme();
  const { loading } = useAuth();

  if (loading) {
    return <LoadingSpinner />;
  }

  return (
    <div className={theme} data-theme={theme}>
      <Routes>
        {/* Public Routes */}
        <Route path="/" element={<LandingPage />} />
        <Route path="/about" element={<AboutPage />} />
        <Route path="/login" element={<LoginPage />} />
        <Route path="/register" element={<RegisterPage />} />
        <Route path="/forgot-password" element={<ForgotPasswordPage />} />
        
        {/* Legal Pages */}
        <Route path="/terms-of-service" element={<TermsOfServicePage />} />
        <Route path="/privacy-policy" element={<PrivacyPolicyPage />} />
        <Route path="/cookie-policy" element={<CookiePolicyPage />} />
        <Route path="/refund-policy" element={<RefundPolicyPage />} />
        <Route path="/accessibility" element={<AccessibilityPage />} />

        {/* Protected Dashboard Routes */}
        <Route path="/dashboard" element={<ProtectedRoute><DashboardLayout /></ProtectedRoute>}>
          <Route index element={<DashboardHome />} />
          <Route path="profile" element={<ProfilePage />} />
          <Route path="settings" element={<SettingsPage />} />
          <Route path="social-media" element={<SocialMediaPage />} />
          <Route path="ecommerce" element={<EcommercePage />} />
          <Route path="courses" element={<CoursesPage />} />
          <Route path="crm" element={<CRMPage />} />
          <Route path="analytics" element={<AnalyticsPage />} />
          <Route path="bio-sites" element={<BioSitesPage />} />
          <Route path="email-marketing" element={<EmailMarketingPage />} />
          <Route path="booking" element={<BookingPage />} />
          <Route path="payments" element={<PaymentsPage />} />
          <Route path="website-builder" element={<WebsiteBuilderPage />} />
          <Route path="ai-features" element={<AIFeaturesPage />} />
          <Route path="team" element={<TeamManagementPage />} />
          <Route path="workspace" element={<WorkspacePage />} />
        </Route>

        {/* Admin Routes */}
        <Route path="/admin" element={<AdminRoute><DashboardLayout isAdmin={true} /></AdminRoute>}>
          <Route index element={<AdminDashboard />} />
          <Route path="users" element={<AdminUsers />} />
          <Route path="settings" element={<AdminSettings />} />
        </Route>

        {/* Catch all - redirect to home */}
        <Route path="*" element={<Navigate to="/" replace />} />
      </Routes>
    </div>
  );
}

export default App;