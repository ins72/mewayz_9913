import React from 'react';
import { Link } from 'react-router-dom';
import { motion } from 'framer-motion';
import { useTheme } from '../contexts/ThemeContext';
import Button from '../components/Button';
import {
  SunIcon,
  MoonIcon,
  CheckIcon,
} from '@heroicons/react/24/outline';

const AboutPage = () => {
  const { theme, toggleTheme } = useTheme();

  const stats = [
    { name: '10,000+', description: 'Active Users' },
    { name: '50+', description: 'Countries' },
    { name: '$10M+', description: 'Revenue Generated' },
    { name: '4.9/5', description: 'Support Rating' },
  ];

  const features = [
    'All-in-one platform',
    'No technical expertise required',
    'Professional analytics',
    'Real-time collaboration',
    'Enterprise security',
    '24/7 customer support',
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
              <Link to="/" className="text-secondary hover:text-primary transition-colors">
                Home
              </Link>
              <Link to="/about" className="text-primary font-medium">
                About
              </Link>
              <Link to="/#features" className="text-secondary hover:text-primary transition-colors">
                Features
              </Link>
              <Link to="/#pricing" className="text-secondary hover:text-primary transition-colors">
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
              
              <div className="flex items-center space-x-3">
                <Link to="/login">
                  <Button variant="secondary">Login</Button>
                </Link>
                <Link to="/register">
                  <Button>Get Started</Button>
                </Link>
              </div>
            </div>
          </div>
        </div>
      </header>

      {/* Hero Section */}
      <section className="bg-gradient-hero py-24">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <motion.h1
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
            className="text-5xl md:text-6xl font-bold text-primary mb-6"
          >
            About Mewayz
          </motion.h1>
          
          <motion.p
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6, delay: 0.1 }}
            className="text-xl text-secondary mb-8 max-w-3xl mx-auto"
          >
            We're building the future of the creator economy with tools that empower entrepreneurs to build, grow, and scale their businesses.
          </motion.p>
        </div>
      </section>

      {/* Stats Section */}
      <section className="py-16 bg-app">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-2 lg:grid-cols-4 gap-8">
            {stats.map((stat, index) => (
              <motion.div
                key={stat.name}
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.6, delay: index * 0.1 }}
                className="text-center"
              >
                <div className="text-4xl font-bold text-accent-primary mb-2">
                  {stat.name}
                </div>
                <div className="text-secondary">
                  {stat.description}
                </div>
              </motion.div>
            ))}
          </div>
        </div>
      </section>

      {/* Mission Section */}
      <section className="py-24 bg-gradient-surface">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <motion.div
              initial={{ opacity: 0, x: -20 }}
              animate={{ opacity: 1, x: 0 }}
              transition={{ duration: 0.6 }}
            >
              <h2 className="text-4xl font-bold text-primary mb-6">
                Our Mission
              </h2>
              <p className="text-lg text-secondary mb-6">
                At Mewayz, we believe every creator deserves access to professional-grade tools to build their dream business. Our mission is to democratize the creator economy by providing an all-in-one platform that combines social media management, e-commerce, course creation, and much more.
              </p>
              <p className="text-lg text-secondary">
                We're passionate about empowering entrepreneurs, creators, and businesses to succeed in the digital age without the complexity of managing multiple tools and platforms.
              </p>
            </motion.div>
            
            <motion.div
              initial={{ opacity: 0, x: 20 }}
              animate={{ opacity: 1, x: 0 }}
              transition={{ duration: 0.6, delay: 0.2 }}
              className="bg-gradient-primary p-8 rounded-lg text-white"
            >
              <h3 className="text-2xl font-bold mb-6">
                Why Choose Mewayz?
              </h3>
              <ul className="space-y-4">
                {features.map((feature) => (
                  <li key={feature} className="flex items-center">
                    <CheckIcon className="w-5 h-5 mr-3 flex-shrink-0" />
                    <span>{feature}</span>
                  </li>
                ))}
              </ul>
            </motion.div>
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="py-24 bg-app">
        <div className="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
          <motion.h2
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
            className="text-4xl font-bold text-primary mb-6"
          >
            Ready to Start Your Journey?
          </motion.h2>
          <motion.p
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6, delay: 0.1 }}
            className="text-xl text-secondary mb-8"
          >
            Join thousands of creators who are already building their dream businesses with Mewayz.
          </motion.p>
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6, delay: 0.2 }}
            className="flex flex-col sm:flex-row gap-4 justify-center"
          >
            <Link to="/register">
              <Button size="large">
                Start Free Trial
              </Button>
            </Link>
            <Link to="/#features">
              <Button variant="secondary" size="large">
                Learn More
              </Button>
            </Link>
          </motion.div>
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
                <li><Link to="/#features" className="text-secondary hover:text-primary">Features</Link></li>
                <li><Link to="/#pricing" className="text-secondary hover:text-primary">Pricing</Link></li>
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
                <li><Link to="/terms" className="text-secondary hover:text-primary">Terms</Link></li>
                <li><Link to="/privacy" className="text-secondary hover:text-primary">Privacy</Link></li>
                <li><Link to="/cookies" className="text-secondary hover:text-primary">Cookies</Link></li>
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

export default AboutPage;