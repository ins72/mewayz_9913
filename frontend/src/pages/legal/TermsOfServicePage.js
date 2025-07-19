import React from 'react';
import { Link } from 'react-router-dom';
import { useTheme } from '../../contexts/ThemeContext';
import { SunIcon, MoonIcon } from '@heroicons/react/24/outline';

const TermsOfServicePage = () => {
  const { theme, toggleTheme } = useTheme();

  return (
    <div className="min-h-screen bg-app">
      {/* Header */}
      <header className="nav-bg shadow-sm">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-center h-16">
            <div className="flex items-center">
              <Link to="/" className="text-2xl font-bold text-accent-primary">
                Mewayz
              </Link>
            </div>
            
            <nav className="hidden md:flex space-x-8">
              <Link to="/" className="text-secondary hover:text-primary transition-colors">
                Home
              </Link>
              <Link to="/about" className="text-secondary hover:text-primary transition-colors">
                About
              </Link>
            </nav>
            
            <div className="flex items-center space-x-4">
              <button
                onClick={toggleTheme}
                className="p-2 text-secondary hover:text-primary transition-colors"
              >
                {theme === 'dark' ? <SunIcon className="w-5 h-5" /> : <MoonIcon className="w-5 h-5" />}
              </button>
              
              <div className="flex items-center space-x-3">
                <Link to="/login" className="text-secondary hover:text-primary transition-colors">
                  Login
                </Link>
              </div>
            </div>
          </div>
        </div>
      </header>

      {/* Content */}
      <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div className="card-elevated p-8">
          <h1 className="text-4xl font-bold text-primary mb-8">Terms of Service</h1>
          <div className="text-secondary text-sm mb-8">
            Last updated: July 19, 2025
          </div>

          <div className="prose max-w-none text-primary">
            <h2 className="text-2xl font-semibold text-primary mt-8 mb-4">1. Acceptance of Terms</h2>
            <p className="mb-4">
              By accessing and using Mewayz ("Service"), you accept and agree to be bound by the terms and provision of this agreement.
            </p>

            <h2 className="text-2xl font-semibold text-primary mt-8 mb-4">2. Description of Service</h2>
            <p className="mb-4">
              Mewayz is a comprehensive creator economy platform that provides tools for social media management, e-commerce, course creation, CRM, analytics, email marketing, and bio sites.
            </p>

            <h2 className="text-2xl font-semibold text-primary mt-8 mb-4">3. User Accounts</h2>
            <p className="mb-4">
              To access certain features of the Service, you must register for an account. You are responsible for safeguarding your account information and for all activities that occur under your account.
            </p>

            <h2 className="text-2xl font-semibold text-primary mt-8 mb-4">4. Acceptable Use</h2>
            <p className="mb-4">
              You agree not to use the Service for any unlawful purpose or in any way that could damage, disable, overburden, or impair the Service.
            </p>

            <h2 className="text-2xl font-semibold text-primary mt-8 mb-4">5. Payment Terms</h2>
            <p className="mb-4">
              Some features of the Service require payment. You agree to pay all charges incurred by your account at the prices in effect when such charges are incurred.
            </p>

            <h2 className="text-2xl font-semibold text-primary mt-8 mb-4">6. Intellectual Property</h2>
            <p className="mb-4">
              The Service and its original content, features, and functionality are owned by Mewayz and are protected by international copyright, trademark, patent, trade secret, and other intellectual property laws.
            </p>

            <h2 className="text-2xl font-semibold text-primary mt-8 mb-4">7. Privacy Policy</h2>
            <p className="mb-4">
              Your privacy is important to us. Please review our Privacy Policy, which also governs your use of the Service.
            </p>

            <h2 className="text-2xl font-semibold text-primary mt-8 mb-4">8. Termination</h2>
            <p className="mb-4">
              We may terminate or suspend your account and bar access to the Service immediately, without prior notice or liability, under our sole discretion, for any reason whatsoever.
            </p>

            <h2 className="text-2xl font-semibold text-primary mt-8 mb-4">9. Limitation of Liability</h2>
            <p className="mb-4">
              In no event shall Mewayz, nor its directors, employees, partners, agents, suppliers, or affiliates, be liable for any indirect, incidental, special, consequential, or punitive damages.
            </p>

            <h2 className="text-2xl font-semibold text-primary mt-8 mb-4">10. Contact Information</h2>
            <p className="mb-4">
              If you have any questions about these Terms of Service, please contact us at legal@mewayz.com.
            </p>
          </div>

          <div className="mt-12 pt-8 border-t border-default">
            <div className="flex items-center justify-between">
              <Link to="/" className="text-accent-primary hover:opacity-80 transition-opacity">
                ← Back to Home
              </Link>
              <div className="flex items-center space-x-4 text-sm text-secondary">
                <Link to="/privacy-policy" className="hover:text-primary transition-colors">Privacy Policy</Link>
                <Link to="/cookie-policy" className="hover:text-primary transition-colors">Cookie Policy</Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default TermsOfServicePage;