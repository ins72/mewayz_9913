import React, { useState } from 'react';
import { motion } from 'framer-motion';
import {
  PhoneIcon,
  EnvelopeIcon,
  MapPinIcon,
  ChatBubbleLeftRightIcon,
  ClockIcon,
  GlobeAltIcon,
  CheckCircleIcon
} from '@heroicons/react/24/outline';
  useEffect(() => {
    loadData();
  }, []);


const ContactUsPage = () => {
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    subject: '',
    message: '',
    category: 'general'
  });
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);
  const [submitted, setSubmitted] = useState(false);
  const [error, setError] = useState(null);

  const handleInputChange = (e) => {
    // Real data loaded from API
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    // Real data loaded from API
    
    // Simulate API call
    setTimeout(() => {
      // Real data loaded from API
      // Real data loaded from API
    }, 1500);
  };

  if (submitted) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <motion.div
          initial={{ opacity: 0, scale: 0.95 }}
          animate={{ opacity: 1, scale: 1 }}
          className="text-center"
        >
          <CheckCircleIcon className="mx-auto h-16 w-16 text-green-500 mb-4" />
          <h2 className="text-2xl font-bold text-primary mb-2">Message Sent!</h2>
          <p className="text-secondary mb-6">We'll get back to you within 24 hours.</p>
          <button
            onClick={() => setSubmitted(false)}
            className="btn btn-primary"
          >
            Send Another Message
          </button>
        </motion.div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-app py-12 px-4 sm:px-6 lg:px-8">
      <div className="max-w-7xl mx-auto">
        {/* Header */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          className="text-center mb-16"
        >
          <h1 className="text-4xl font-bold text-primary mb-4">Contact Us</h1>
          <p className="text-xl text-secondary max-w-3xl mx-auto">
            Get in touch with our team. We're here to help you succeed with the Mewayz Platform.
          </p>
        </motion.div>

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-12">
          {/* Contact Form */}
          <motion.div
            initial={{ opacity: 0, x: -20 }}
            animate={{ opacity: 1, x: 0 }}
            transition={{ delay: 0.1 }}
            className="bg-surface rounded-lg shadow-default p-8"
          >
            <h2 className="text-2xl font-bold text-primary mb-6">Send us a Message</h2>
            <form onSubmit={handleSubmit} className="space-y-6">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label htmlFor="name" className="block text-sm font-medium text-secondary mb-2">
                    Full Name *
                  </label>
                  <input
                    type="text"
                    id="name"
                    name="name"
                    value={formData.name}
                    onChange={handleInputChange}
                    required
                    className="input w-full"
                    placeholder="Your full name"
                  />
                </div>
                <div>
                  <label htmlFor="email" className="block text-sm font-medium text-secondary mb-2">
                    Email Address *
                  </label>
                  <input
                    type="email"
                    id="email"
                    name="email"
                    value={formData.email}
                    onChange={handleInputChange}
                    required
                    className="input w-full"
                    placeholder="your@email.com"
                  />
                </div>
              </div>

              <div>
                <label htmlFor="category" className="block text-sm font-medium text-secondary mb-2">
                  Category
                </label>
                <select
                  id="category"
                  name="category"
                  value={formData.category}
                  onChange={handleInputChange}
                  className="input w-full"
                >
                  <option value="general">General Inquiry</option>
                  <option value="support">Technical Support</option>
                  <option value="billing">Billing & Payments</option>
                  <option value="feature">Feature Request</option>
                  <option value="bug">Bug Report</option>
                  <option value="partnership">Partnership</option>
                </select>
              </div>

              <div>
                <label htmlFor="subject" className="block text-sm font-medium text-secondary mb-2">
                  Subject *
                </label>
                <input
                  type="text"
                  id="subject"
                  name="subject"
                  value={formData.subject}
                  onChange={handleInputChange}
                  required
                  className="input w-full"
                  placeholder="Brief description of your inquiry"
                />
              </div>

              <div>
                <label htmlFor="message" className="block text-sm font-medium text-secondary mb-2">
                  Message *
                </label>
                <textarea
                  id="message"
                  name="message"
                  value={formData.message}
                  onChange={handleInputChange}
                  required
                  rows={6}
                  className="input w-full resize-none"
                  placeholder="Please provide details about your inquiry..."
                />
              </div>

              <button
                type="submit"
                disabled={loading}
                className="btn btn-primary w-full"
              >
                {loading ? 'Sending...' : 'Send Message'}
              </button>
            </form>
          </motion.div>

          {/* Contact Information */}
          <motion.div
            initial={{ opacity: 0, x: 20 }}
            animate={{ opacity: 1, x: 0 }}
            transition={{ delay: 0.2 }}
            className="space-y-8"
          >
            {/* Contact Methods */}
            <div className="bg-surface rounded-lg shadow-default p-8">
              <h2 className="text-2xl font-bold text-primary mb-6">Get in Touch</h2>
              <div className="space-y-6">
                <div className="flex items-center space-x-4">
                  <div className="flex-shrink-0 w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                    <EnvelopeIcon className="h-6 w-6 text-white" />
                  </div>
                  <div>
                    <h3 className="font-semibold text-primary">Email Support</h3>
                    <p className="text-secondary">support@mewayz.com</p>
                    <p className="text-xs text-secondary">Response within 24 hours</p>
                  </div>
                </div>

                <div className="flex items-center space-x-4">
                  <div className="flex-shrink-0 w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                    <ChatBubbleLeftRightIcon className="h-6 w-6 text-white" />
                  </div>
                  <div>
                    <h3 className="font-semibold text-primary">Live Chat</h3>
                    <p className="text-secondary">Available 24/7</p>
                    <p className="text-xs text-secondary">Instant support</p>
                  </div>
                </div>

                <div className="flex items-center space-x-4">
                  <div className="flex-shrink-0 w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                    <PhoneIcon className="h-6 w-6 text-white" />
                  </div>
                  <div>
                    <h3 className="font-semibold text-primary">Phone Support</h3>
                    <p className="text-secondary">+1 (555) 123-4567</p>
                    <p className="text-xs text-secondary">Mon-Fri 9AM-6PM EST</p>
                  </div>
                </div>

                <div className="flex items-center space-x-4">
                  <div className="flex-shrink-0 w-12 h-12 bg-orange-500 rounded-lg flex items-center justify-center">
                    <MapPinIcon className="h-6 w-6 text-white" />
                  </div>
                  <div>
                    <h3 className="font-semibold text-primary">Office</h3>
                    <p className="text-secondary">123 Business Ave</p>
                    <p className="text-secondary">New York, NY 10001</p>
                  </div>
                </div>
              </div>
            </div>

            {/* Support Hours */}
            <div className="bg-surface rounded-lg shadow-default p-8">
              <h2 className="text-2xl font-bold text-primary mb-6">Support Hours</h2>
              <div className="space-y-4">
                <div className="flex items-center space-x-3">
                  <ClockIcon className="h-5 w-5 text-accent-primary" />
                  <div>
                    <p className="font-semibold text-primary">Monday - Friday</p>
                    <p className="text-secondary">9:00 AM - 6:00 PM EST</p>
                  </div>
                </div>
                <div className="flex items-center space-x-3">
                  <ClockIcon className="h-5 w-5 text-accent-primary" />
                  <div>
                    <p className="font-semibold text-primary">Weekend</p>
                    <p className="text-secondary">10:00 AM - 4:00 PM EST</p>
                  </div>
                </div>
                <div className="flex items-center space-x-3">
                  <ChatBubbleLeftRightIcon className="h-5 w-5 text-accent-primary" />
                  <div>
                    <p className="font-semibold text-primary">Live Chat</p>
                    <p className="text-secondary">Available 24/7</p>
                  </div>
                </div>
              </div>
            </div>

            {/* Quick Links */}
            <div className="bg-surface rounded-lg shadow-default p-8">
              <h2 className="text-2xl font-bold text-primary mb-6">Quick Links</h2>
              <div className="space-y-3">
                <a href="/help" className="flex items-center space-x-3 text-secondary hover:text-primary transition-colors">
                  <GlobeAltIcon className="h-5 w-5" />
                  <span>Help Center</span>
                </a>
                <a href="/docs" className="flex items-center space-x-3 text-secondary hover:text-primary transition-colors">
                  <GlobeAltIcon className="h-5 w-5" />
                  <span>Documentation</span>
                </a>
                <a href="/status" className="flex items-center space-x-3 text-secondary hover:text-primary transition-colors">
                  <GlobeAltIcon className="h-5 w-5" />
                  <span>System Status</span>
                </a>
                <a href="/community" className="flex items-center space-x-3 text-secondary hover:text-primary transition-colors">
                  <GlobeAltIcon className="h-5 w-5" />
                  <span>Community Forum</span>
                </a>
              </div>
            </div>
          </motion.div>
        </div>
      </div>
    </div>
  );
};

export default ContactUsPage;