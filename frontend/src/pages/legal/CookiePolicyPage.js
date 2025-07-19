import React from 'react';
import { Link } from 'react-router-dom';
import { motion } from 'framer-motion';
import { useTheme } from '../../contexts/ThemeContext';
import { SunIcon, MoonIcon } from '@heroicons/react/24/outline';

const CookiePolicyPage = () => {
  const { theme, toggleTheme } = useTheme();

  return (
    <div className="min-h-screen bg-gray-50 dark:bg-gray-900">
      <nav className="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-center h-16">
            <Link to="/" className="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
              Mewayz
            </Link>
            <div className="flex items-center space-x-4">
              <button onClick={toggleTheme} className="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                {theme === 'dark' ? <SunIcon className="w-5 h-5" /> : <MoonIcon className="w-5 h-5" />}
              </button>
              <Link to="/" className="text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                Back to Home
              </Link>
            </div>
          </div>
        </div>
      </nav>

      <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <motion.div initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.6 }}>
          <h1 className="text-3xl font-bold text-gray-900 dark:text-white mb-8">Cookie Policy</h1>
          <p className="text-gray-600 dark:text-gray-400 mb-6">Last updated: January 1, 2025</p>
          <div className="space-y-8 text-gray-700 dark:text-gray-300">
            <section>
              <h2 className="text-xl font-semibold text-gray-900 dark:text-white mb-4">What are cookies?</h2>
              <p>Cookies are small text files that are placed on your computer or mobile device when you visit our website. They help us provide you with a better experience and allow certain features to work.</p>
            </section>
            <section>
              <h2 className="text-xl font-semibold text-gray-900 dark:text-white mb-4">How we use cookies</h2>
              <p>We use cookies for various purposes including authentication, security, preferences, analytics, and advertising. This helps us understand how you use our service and improve your experience.</p>
            </section>
            <section>
              <h2 className="text-xl font-semibold text-gray-900 dark:text-white mb-4">Types of cookies we use</h2>
              <ul className="list-disc list-inside space-y-2">
                <li><strong>Essential cookies:</strong> Required for the website to function properly</li>
                <li><strong>Analytics cookies:</strong> Help us understand how visitors use our website</li>
                <li><strong>Functional cookies:</strong> Remember your preferences and settings</li>
                <li><strong>Advertising cookies:</strong> Used to deliver relevant advertisements</li>
              </ul>
            </section>
            <section>
              <h2 className="text-xl font-semibold text-gray-900 dark:text-white mb-4">Managing cookies</h2>
              <p>You can control and manage cookies through your browser settings. However, disabling certain cookies may affect the functionality of our website.</p>
            </section>
          </div>
        </motion.div>
      </div>
    </div>
  );
export default CookiePolicyPage;