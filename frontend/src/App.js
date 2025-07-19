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

// Components
import ProtectedRoute from './components/ProtectedRoute';
import LoadingSpinner from './components/LoadingSpinner';

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
        
        {/* Protected Dashboard Routes */}
        <Route path="/dashboard" element={<ProtectedRoute><DashboardLayout /></ProtectedRoute>}>
          <Route index element={<DashboardHome />} />
          <Route path="social-media" element={<SocialMediaPage />} />
          <Route path="ecommerce" element={<EcommercePage />} />
          <Route path="courses" element={<CoursesPage />} />
          <Route path="crm" element={<CRMPage />} />
          <Route path="analytics" element={<AnalyticsPage />} />
          <Route path="bio-sites" element={<BioSitesPage />} />
          <Route path="email-marketing" element={<EmailMarketingPage />} />
          <Route path="payments" element={<PaymentsPage />} />
        </Route>

        {/* Catch all - redirect to home */}
        <Route path="*" element={<Navigate to="/" replace />} />
      </Routes>
    </div>
  );
}

export default App;