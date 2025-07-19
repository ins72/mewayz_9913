import React from 'react';
import { Link } from 'react-router-dom';
import { useTheme } from '../../contexts/ThemeContext';
import { SunIcon, MoonIcon } from '@heroicons/react/24/outline';

const PrivacyPolicyPage = () => {
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
          <h1 className="text-4xl font-bold text-primary mb-8">Privacy Policy</h1>
          <div className="text-secondary text-sm mb-8">
            Last updated: July 19, 2025
          </div>

          <div className="prose max-w-none text-primary">
            <h2 className="text-2xl font-semibold text-primary mt-8 mb-4">1. Information We Collect</h2>
            <p className="mb-4">
              We collect information you provide directly to us, such as when you create an account, use our services, or contact us for support.
            </p>

            <h2 className="text-2xl font-semibold text-primary mt-8 mb-4">2. How We Use Your Information</h2>
            <p className="mb-4">
              We use the information we collect to provide, maintain, and improve our services, process transactions, and communicate with you.
            </p>

            <h2 className="text-2xl font-semibold text-primary mt-8 mb-4">3. Information Sharing</h2>
            <p className="mb-4">
              We do not sell, trade, or otherwise transfer your personal information to third parties without your consent, except as described in this policy.
            </p>

            <h2 className="text-2xl font-semibold text-primary mt-8 mb-4">4. Data Security</h2>
            <p className="mb-4">
              We implement appropriate security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.
            </p>

            <h2 className="text-2xl font-semibold text-primary mt-8 mb-4">5. Cookies and Tracking</h2>
            <p className="mb-4">
              We use cookies and similar tracking technologies to enhance your experience and gather information about visitors and visits to our website.
            </p>

            <h2 className="text-2xl font-semibold text-primary mt-8 mb-4">6. Third-Party Services</h2>
            <p className="mb-4">
              Our service may contain links to third-party websites or services. We are not responsible for the privacy practices of these third parties.
            </p>

            <h2 className="text-2xl font-semibold text-primary mt-8 mb-4">7. Data Retention</h2>
            <p className="mb-4">
              We retain your personal information for as long as necessary to provide our services and fulfill the purposes outlined in this policy.
            </p>

            <h2 className="text-2xl font-semibold text-primary mt-8 mb-4">8. Your Rights</h2>
            <p className="mb-4">
              You have the right to access, update, or delete your personal information. You may also opt out of certain communications from us.
            </p>

            <h2 className="text-2xl font-semibold text-primary mt-8 mb-4">9. International Transfers</h2>
            <p className="mb-4">
              Your information may be transferred to and processed in countries other than your own. We ensure appropriate safeguards are in place.
            </p>

            <h2 className="text-2xl font-semibold text-primary mt-8 mb-4">10. Contact Us</h2>
            <p className="mb-4">
              If you have any questions about this Privacy Policy, please contact us at privacy@mewayz.com.
            </p>
          </div>

          <div className="mt-12 pt-8 border-t border-default">
            <div className="flex items-center justify-between">
              <Link to="/" className="text-accent-primary hover:opacity-80 transition-opacity">
                ← Back to Home
              </Link>
              <div className="flex items-center space-x-4 text-sm text-secondary">
                <Link to="/terms-of-service" className="hover:text-primary transition-colors">Terms of Service</Link>
                <Link to="/cookie-policy" className="hover:text-primary transition-colors">Cookie Policy</Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default PrivacyPolicyPage;