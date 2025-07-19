import React from 'react';
import { Link } from 'react-router-dom';
import { useTheme } from '../../contexts/ThemeContext';
import { SunIcon, MoonIcon } from '@heroicons/react/24/outline';

const CookiePolicyPage = () => {
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
          <h1 className="text-4xl font-bold text-primary mb-8">Cookie Policy</h1>
          <div className="text-secondary text-sm mb-8">
            Last updated: July 20, 2025
          </div>

          <div className="prose max-w-none text-primary">
            <h2 className="text-2xl font-semibold text-primary mt-8 mb-4">1. What Are Cookies</h2>
            <p className="mb-4">
              Cookies are small data files that are placed on your device when you visit our website. They help us provide you with a better experience and remember your preferences.
            </p>

            <h2 className="text-2xl font-semibold text-primary mt-8 mb-4">2. Types of Cookies We Use</h2>
            <p className="mb-4">
              We use several types of cookies to enhance your experience:
            </p>
            <ul className="list-disc list-inside mb-4 space-y-2">
              <li><strong>Essential Cookies:</strong> Required for the website to function properly</li>
              <li><strong>Analytics Cookies:</strong> Help us understand how visitors interact with our website</li>
              <li><strong>Functional Cookies:</strong> Remember your preferences and settings</li>
              <li><strong>Marketing Cookies:</strong> Used to deliver relevant advertisements</li>
            </ul>

            <h2 className="text-2xl font-semibold text-primary mt-8 mb-4">3. Third-Party Cookies</h2>
            <p className="mb-4">
              We may use third-party services like Google Analytics, which set their own cookies to collect information about your usage patterns.
            </p>

            <h2 className="text-2xl font-semibold text-primary mt-8 mb-4">4. Managing Cookies</h2>
            <p className="mb-4">
              You can control and delete cookies through your browser settings. However, disabling cookies may affect the functionality of our website.
            </p>

            <h2 className="text-2xl font-semibold text-primary mt-8 mb-4">5. Consent</h2>
            <p className="mb-4">
              By continuing to use our website, you consent to our use of cookies as described in this policy. You can withdraw your consent at any time by adjusting your browser settings.
            </p>

            <h2 className="text-2xl font-semibold text-primary mt-8 mb-4">6. Contact Us</h2>
            <p className="mb-4">
              If you have any questions about our use of cookies, please contact us at cookies@mewayz.com.
            </p>
          </div>

          <div className="mt-12 pt-8 border-t border-default">
            <div className="flex items-center justify-between">
              <Link to="/" className="text-accent-primary hover:opacity-80 transition-opacity">
                ← Back to Home
              </Link>
              <div className="flex items-center space-x-4 text-sm text-secondary">
                <Link to="/terms-of-service" className="hover:text-primary transition-colors">Terms of Service</Link>
                <Link to="/privacy-policy" className="hover:text-primary transition-colors">Privacy Policy</Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default CookiePolicyPage;