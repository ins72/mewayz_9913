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
      description: 'Leverage artificial intelligence to automate and enhance your business processes.',
    },
  ];

  const pricing = [
    {
      name: 'Starter',
      price: '$9',
      period: '/month',
      features: [
        'Up to 3 social media accounts',
        'Basic analytics',
        'Email support',
        '1 bio site',
        'Basic templates',
      ],
    },
    {
      name: 'Professional',
      price: '$29',
      period: '/month',
      popular: true,
      features: [
        'Unlimited social media accounts',
        'Advanced analytics',
        'Priority support',
        'Unlimited bio sites',
        'Premium templates',
        'E-commerce features',
      ],
    },
    {
      name: 'Enterprise',
      price: '$99',
      period: '/month',
      features: [
        'Everything in Professional',
        'Team collaboration',
        'Custom integrations',
        'White-label options',
        'Dedicated account manager',
      ],
    },
  ];

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
              <Link to="/about" className="text-secondary hover:text-primary transition-colors">
                About
              </Link>
              <Link to="#features" className="text-secondary hover:text-primary transition-colors">
                Features
              </Link>
              <Link to="#pricing" className="text-secondary hover:text-primary transition-colors">
                Pricing
              </Link>
            </nav>
            
            <div className="flex items-center space-x-4">
              <button
                onClick={toggleTheme}
                className="p-2 text-secondary hover:text-primary transition-colors"
              >
                {theme === 'dark' ? <SunIcon className="w-5 h-5" /> : <MoonIcon className="w-5 h-5" />}
              </button>
              
              {isAuthenticated ? (
                <Link to="/dashboard">
                  <Button>Dashboard</Button>
                </Link>
              ) : (
                <div className="flex items-center space-x-3">
                  <Link to="/login">
                    <Button variant="secondary">Login</Button>
                  </Link>
                  <Link to="/register">
                    <Button>Get Started</Button>
                  </Link>
                </div>
              )}
            </div>
          </div>
        </div>
      </header>

      {/* Hero Section */}
      <section className="bg-gradient-hero py-24">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center">
            <motion.h1
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6 }}
              className="text-5xl md:text-6xl font-bold text-primary mb-6"
            >
              Complete Creator
              <br />
              <span className="text-accent-primary">Economy Platform</span>
            </motion.h1>
            
            <motion.p
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6, delay: 0.1 }}
              className="text-xl text-secondary mb-8 max-w-3xl mx-auto"
            >
              Everything you need to build, manage, and scale your creator business. From social media management to e-commerce, courses, and beyond.
            </motion.p>
            
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6, delay: 0.2 }}
              className="flex flex-col sm:flex-row gap-4 justify-center"
            >
              <Link to="/register">
                <Button size="large" className="flex items-center">
                  <ArrowRightIcon className="w-5 h-5 mr-2" />
                  Start Free Trial
                </Button>
              </Link>
              <Button variant="secondary" size="large">
                Learn More
              </Button>
            </motion.div>
          </div>
        </div>
      </section>

      {/* Features Section */}
      <section id="features" className="py-24 bg-app">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-4xl font-bold text-primary mb-4">
              Everything You Need in One Platform
            </h2>
            <p className="text-xl text-secondary max-w-3xl mx-auto">
              Powerful tools designed specifically for creators, entrepreneurs, and businesses looking to thrive in the digital economy.
            </p>
          </div>
          
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {features.map((feature, index) => (
              <motion.div
                key={feature.title}
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.6, delay: index * 0.1 }}
                className="card-elevated p-6 text-center"
              >
                <div className="bg-gradient-primary w-12 h-12 rounded-lg flex items-center justify-center mx-auto mb-4">
                  <feature.icon className="w-6 h-6 text-white" />
                </div>
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

      {/* Pricing Section */}
      <section id="pricing" className="py-24 bg-gradient-surface">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-4xl font-bold text-primary mb-4">
              Simple, Transparent Pricing
            </h2>
            <p className="text-xl text-secondary">
              Choose the plan that's right for your business
            </p>
          </div>
          
          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            {pricing.map((plan, index) => (
              <motion.div
                key={plan.name}
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.6, delay: index * 0.1 }}
                className={`card-elevated p-8 text-center relative ${
                  plan.popular ? 'ring-2 ring-accent-primary' : ''
                }`}
              >
                {plan.popular && (
                  <div className="absolute -top-4 left-1/2 transform -translate-x-1/2">
                    <span className="bg-gradient-primary text-white px-4 py-1 rounded-full text-sm font-medium">
                      Most Popular
                    </span>
                  </div>
                )}
                
                <h3 className="text-2xl font-bold text-primary mb-4">
                  {plan.name}
                </h3>
                <div className="mb-6">
                  <span className="text-4xl font-bold text-accent-primary">
                    {plan.price}
                  </span>
                  <span className="text-secondary">{plan.period}</span>
                </div>
                
                <ul className="space-y-3 mb-8">
                  {plan.features.map((feature) => (
                    <li key={feature} className="flex items-center">
                      <CheckIcon className="w-5 h-5 text-accent-success mr-3" />
                      <span className="text-secondary">{feature}</span>
                    </li>
                  ))}
                </ul>
                
                <Link to="/register">
                  <Button
                    variant={plan.popular ? 'primary' : 'secondary'}
                    fullWidth
                  >
                    Get Started
                  </Button>
                </Link>
              </motion.div>
            ))}
          </div>
        </div>
      </section>

      {/* Footer */}
      <footer className="bg-surface border-t border-default py-12">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
              <h3 className="text-lg font-semibold text-primary mb-4">Mewayz</h3>
              <p className="text-secondary">
                The complete creator economy platform for modern businesses.
              </p>
            </div>
            
            <div>
              <h4 className="text-sm font-semibold text-primary uppercase tracking-wider mb-4">
                Product
              </h4>
              <ul className="space-y-2">
                <li><Link to="#" className="text-secondary hover:text-primary">Features</Link></li>
                <li><Link to="#" className="text-secondary hover:text-primary">Pricing</Link></li>
                <li><Link to="#" className="text-secondary hover:text-primary">Templates</Link></li>
              </ul>
            </div>
            
            <div>
              <h4 className="text-sm font-semibold text-primary uppercase tracking-wider mb-4">
                Company
              </h4>
              <ul className="space-y-2">
                <li><Link to="/about" className="text-secondary hover:text-primary">About</Link></li>
                <li><Link to="#" className="text-secondary hover:text-primary">Blog</Link></li>
                <li><Link to="#" className="text-secondary hover:text-primary">Contact</Link></li>
              </ul>
            </div>
            
            <div>
              <h4 className="text-sm font-semibold text-primary uppercase tracking-wider mb-4">
                Legal
              </h4>
              <ul className="space-y-2">
                <li><Link to="/terms-of-service" className="text-secondary hover:text-primary">Terms</Link></li>
                <li><Link to="/privacy-policy" className="text-secondary hover:text-primary">Privacy</Link></li>
                <li><Link to="/cookie-policy" className="text-secondary hover:text-primary">Cookies</Link></li>
              </ul>
            </div>
          </div>
          
          <div className="border-t border-default mt-8 pt-8 text-center">
            <p className="text-secondary">
              © 2025 Mewayz. All rights reserved.
            </p>
          </div>
        </div>
      </footer>
    </div>
  );
};

export default LandingPage;