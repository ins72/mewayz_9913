import React from 'react';
import { Link } from 'react-router-dom';
import { motion } from 'framer-motion';
import { useAuth } from '../contexts/AuthContext';
import { useTheme } from '../contexts/ThemeContext';
import Button from '../components/Button';
import {
  ChartBarIcon,
  ShoppingBagIcon,
  AcademicCapIcon,
  UsersIcon,
  GlobeAltIcon,
  SparklesIcon,
  CheckIcon,
  ArrowRightIcon,
  SunIcon,
  MoonIcon,
} from '@heroicons/react/24/outline';

const LandingPage = () => {
  const { isAuthenticated } = useAuth();
  const { theme, toggleTheme } = useTheme();

  const features = [
    {
      icon: ChartBarIcon,
      title: 'Social Media Management',
      description: 'Manage all your social media accounts from one powerful dashboard.',
    },
    {
      icon: ShoppingBagIcon,
      title: 'E-commerce Platform',
      description: 'Build and manage your online store with advanced e-commerce tools.',
    },
    {
      icon: AcademicCapIcon,
      title: 'Course Creation',
      description: 'Create and sell online courses with our comprehensive learning management system.',
    },
    {
      icon: UsersIcon,
      title: 'CRM System',
      description: 'Manage your customers and leads with our powerful CRM tools.',
    },
    {
      icon: GlobeAltIcon,
      title: 'Bio Sites & Website Builder',
      description: 'Create stunning bio sites and websites with our drag-and-drop builder.',
    },
    {
      icon: SparklesIcon,
      title: 'AI-Powered Features',
      description: 'Leverage AI for content generation, SEO optimization, and analytics.',
    },
  ];

  const benefits = [
    'All-in-one creator economy platform',
    'Professional analytics and reporting',
    'Secure payment processing',
    'Email marketing automation',
    'Advanced booking system',
    'Team collaboration tools',
    '24/7 customer support',
    'Mobile-responsive design',
  ];

  return (
    <div className="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
      {/* Navigation */}
      <nav className="relative bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-700">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-center h-16">
            <div className="flex items-center">
              <h1 className="text-2xl font-bold text-primary">
                Mewayz
              </h1>
            </div>
            
            <div className="flex items-center space-x-4">
              <button
                onClick={toggleTheme}
                className="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors"
              >
                {theme === 'dark' ? (
                  <SunIcon className="w-5 h-5" />
                ) : (
                  <MoonIcon className="w-5 h-5" />
                )}
              </button>
              
              <Link to="/about" className="text-secondary hover:text-primary transition-colors">
                About
              </Link>
              
              {isAuthenticated ? (
                <Link to="/dashboard">
                  <Button>Dashboard</Button>
                </Link>
              ) : (
                <div className="flex items-center space-x-2">
                  <Link to="/login">
                    <Button variant="outline">Login</Button>
                  </Link>
                  <Link to="/register">
                    <Button>Get Started</Button>
                  </Link>
                </div>
              )}
            </div>
          </div>
        </div>
      </nav>

      {/* Hero Section */}
      <section className="relative py-20 lg:py-32">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center">
            <motion.h1
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6 }}
              className="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 dark:text-white mb-6"
            >
              Complete Creator
              <span className="block text-primary">
                Economy Platform
              </span>
            </motion.h1>
            
            <motion.p
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6, delay: 0.1 }}
              className="text-xl text-gray-600 dark:text-gray-300 mb-8 max-w-3xl mx-auto"
            >
              Everything you need to build, manage, and scale your creator business. 
              From social media management to e-commerce, courses, and beyond.
            </motion.p>
            
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6, delay: 0.2 }}
              className="flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-4"
            >
              {!isAuthenticated && (
                <Link to="/register">
                  <Button size="large" icon={<ArrowRightIcon className="w-5 h-5" />}>
                    Start Free Trial
                  </Button>
                </Link>
              )}
              <Link to="/about">
                <Button variant="outline" size="large">
                  Learn More
                </Button>
              </Link>
            </motion.div>
          </div>
        </div>
      </section>

      {/* Features Section */}
      <section className="py-20 bg-white dark:bg-gray-800">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-3xl font-bold text-gray-900 dark:text-white mb-4">
              Everything You Need in One Platform
            </h2>
            <p className="text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
              Powerful tools designed specifically for creators, entrepreneurs, and businesses
              looking to thrive in the digital economy.
            </p>
          </div>
          
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {features.map((feature, index) => (
              <motion.div
                key={feature.title}
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.6, delay: index * 0.1 }}
                className="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 hover:shadow-lg transition-shadow"
              >
                <feature.icon className="w-12 h-12 text-accent mb-4" />
                <h3 className="text-xl font-semibold text-primary mb-2">
                  {feature.title}
                </h3>
                <p className="text-secondary">
                  {feature.description}
                </p>
              </motion.div>
            ))}
          </div>
        </div>
      </section>

      {/* Benefits Section */}
      <section className="py-20 bg-gray-50 dark:bg-gray-900">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
              <h2 className="text-3xl font-bold text-gray-900 dark:text-white mb-6">
                Why Choose Mewayz?
              </h2>
              <p className="text-lg text-gray-600 dark:text-gray-300 mb-8">
                Join thousands of creators who have streamlined their business operations
                and increased their revenue with our comprehensive platform.
              </p>
              
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {benefits.map((benefit, index) => (
                  <motion.div
                    key={benefit}
                    initial={{ opacity: 0, x: -20 }}
                    animate={{ opacity: 1, x: 0 }}
                    transition={{ duration: 0.6, delay: index * 0.1 }}
                    className="flex items-center space-x-3"
                  >
                    <CheckIcon className="w-5 h-5 text-green-500 flex-shrink-0" />
                    <span className="text-gray-700 dark:text-gray-300">{benefit}</span>
                  </motion.div>
                ))}
              </div>
            </div>
            
            <div className="bg-white dark:bg-gray-800 rounded-lg p-8 shadow-xl">
              <h3 className="text-2xl font-bold text-gray-900 dark:text-white mb-6">
                Ready to Get Started?
              </h3>
              <p className="text-gray-600 dark:text-gray-300 mb-6">
                Join our platform today and take your creator business to the next level.
              </p>
              {!isAuthenticated && (
                <Link to="/register">
                  <Button fullWidth size="large">
                    Create Your Account
                  </Button>
                </Link>
              )}
            </div>
          </div>
        </div>
      </section>

      {/* Footer */}
      <footer className="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
          <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
              <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Mewayz
              </h3>
              <p className="text-gray-600 dark:text-gray-300">
                The complete creator economy platform for modern entrepreneurs.
              </p>
            </div>
            
            <div>
              <h4 className="font-semibold text-primary mb-4">Features</h4>
              <ul className="space-y-2 text-secondary">
                <li><Link to="#" className="hover:text-primary">Social Media</Link></li>
                <li><Link to="#" className="hover:text-primary">E-commerce</Link></li>
                <li><Link to="#" className="hover:text-primary">Courses</Link></li>
                <li><Link to="#" className="hover:text-primary">Analytics</Link></li>
              </ul>
            </div>
            
            <div>
              <h4 className="font-semibold text-primary mb-4">Company</h4>
              <ul className="space-y-2 text-secondary">
                <li><Link to="/about" className="hover:text-primary">About</Link></li>
                <li><Link to="#" className="hover:text-primary">Blog</Link></li>
                <li><Link to="#" className="hover:text-primary">Careers</Link></li>
                <li><Link to="#" className="hover:text-primary">Contact</Link></li>
              </ul>
            </div>
            
            <div>
              <h4 className="font-semibold text-primary mb-4">Legal</h4>
              <ul className="space-y-2 text-secondary">
                <li><Link to="/terms-of-service" className="hover:text-primary">Terms of Service</Link></li>
                <li><Link to="/privacy-policy" className="hover:text-primary">Privacy Policy</Link></li>
                <li><Link to="/cookie-policy" className="hover:text-primary">Cookie Policy</Link></li>
                <li><Link to="/accessibility" className="hover:text-primary">Accessibility</Link></li>
              </ul>
            </div>
          </div>
          
          <div className="border-t border-gray-200 dark:border-gray-700 mt-8 pt-8 text-center">
            <p className="text-gray-600 dark:text-gray-300">
              © 2025 Mewayz. All rights reserved.
            </p>
          </div>
        </div>
      </footer>
    </div>
  );
};

export default LandingPage;