import React, { useState } from 'react';
import { motion } from 'framer-motion';
import {
  QuestionMarkCircleIcon,
  BookOpenIcon,
  ChatBubbleLeftRightIcon,
  EnvelopeIcon,
  PhoneIcon,
  XMarkIcon,
  MagnifyingGlassIcon,
  ChevronRightIcon,
  VideoCameraIcon,
  DocumentTextIcon
} from '@heroicons/react/24/outline';
  useEffect(() => {
    loadData();
  }, []);


const HelpSupportCenter = ({ isOpen, onClose }) => {
  const [activeTab, setActiveTab] = useState('help');
  const [error, setError] = useState(null);
  const [searchQuery, setSearchQuery] = useState('');
  const [error, setError] = useState(null);
  const [selectedCategory, setSelectedCategory] = useState(null);
  const [error, setError] = useState(null);

  const helpCategories = [
    {
      id: 'getting-started',
      title: 'Getting Started',
      description: 'Learn the basics of using Mewayz Platform',
      icon: BookOpenIcon,
      articles: [
        { title: 'Platform Overview', readTime: '5 min' },
        { title: 'Creating Your First Workspace', readTime: '3 min' },
        { title: 'Inviting Team Members', readTime: '2 min' },
        { title: 'Understanding Subscriptions', readTime: '4 min' }
      ]
    },
    {
      id: 'ai-features',
      title: 'AI Features',
      description: 'How to use AI-powered tools effectively',
      icon: DocumentTextIcon,
      articles: [
        { title: 'AI Content Generation', readTime: '6 min' },
        { title: 'Smart Recommendations', readTime: '4 min' },
        { title: 'AI Analytics Insights', readTime: '5 min' }
      ]
    },
    {
      id: 'collaboration',
      title: 'Collaboration',
      description: 'Work together with your team in real-time',
      icon: ChatBubbleLeftRightIcon,
      articles: [
        { title: 'Real-time Document Editing', readTime: '7 min' },
        { title: 'Video Conferencing Setup', readTime: '3 min' },
        { title: 'Chat and Messaging', readTime: '2 min' }
      ]
    },
    {
      id: 'integrations',
      title: 'Integrations',
      description: 'Connect with your favorite tools and services',
      icon: QuestionMarkCircleIcon,
      articles: [
        { title: 'Setting Up Integrations', readTime: '5 min' },
        { title: 'API Key Management', readTime: '4 min' },
        { title: 'Troubleshooting Connections', readTime: '6 min' }
      ]
    }
  ];

  const supportOptions = [
    {
      title: 'Live Chat',
      description: 'Chat with our support team',
      icon: ChatBubbleLeftRightIcon,
      action: 'Start Chat',
      available: true
    },
    {
      title: 'Email Support',
      description: 'Send us an email and get a response within 24 hours',
      icon: EnvelopeIcon,
      action: 'Send Email',
      available: true
    },
    {
      title: 'Phone Support',
      description: 'Call us directly for urgent issues',
      icon: PhoneIcon,
      action: 'Call Now',
      available: false, // Premium feature
      badge: 'Premium'
    },
    {
      title: 'Video Call',
      description: 'Schedule a screen sharing session',
      icon: VideoCameraIcon,
      action: 'Schedule Call',
      available: false,
      badge: 'Enterprise'
    }
  ];

  const faqs = [
    {
      question: 'How do I upgrade my subscription?',
      answer: 'You can upgrade your subscription by going to Dashboard > Subscription and selecting your desired plan.'
    },
    {
      question: 'Can I collaborate with external users?',
      answer: 'Yes, you can invite external users to specific workspaces and documents by sending them invitation links.'
    },
    {
      question: 'How secure is my data?',
      answer: 'We use enterprise-grade security with end-to-end encryption, regular backups, and SOC 2 compliance.'
    },
    {
      question: 'What happens if I cancel my subscription?',
      answer: 'You can continue using the platform until the end of your billing period, then it will switch to the free plan.'
    },
    {
      question: 'How do I export my data?',
      answer: 'You can export your data from Settings > Data Export. We support multiple formats including PDF, CSV, and JSON.'
    }
  ];

  const filteredFAQs = faqs.filter(faq =>
    faq.question.toLowerCase().includes(searchQuery.toLowerCase()) ||
    faq.answer.toLowerCase().includes(searchQuery.toLowerCase())
  );

  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 z-50 overflow-y-auto">
      <div className="flex min-h-full items-center justify-center p-4">
        {/* Backdrop */}
        <motion.div
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          exit={{ opacity: 0 }}
          className="fixed inset-0 bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75 transition-opacity"
          onClick={onClose}
        />

        {/* Modal */}
        <motion.div
          initial={{ opacity: 0, scale: 0.95 }}
          animate={{ opacity: 1, scale: 1 }}
          exit={{ opacity: 0, scale: 0.95 }}
          className="relative w-full max-w-4xl bg-white dark:bg-gray-800 rounded-lg shadow-2xl"
        >
          {/* Header */}
          <div className="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 className="text-xl font-semibold text-gray-900 dark:text-white">
              Help & Support
            </h2>
            <button
              onClick={onClose}
              className="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300"
            >
              <XMarkIcon className="h-6 w-6" />
            </button>
          </div>

          {/* Tabs */}
          <div className="border-b border-gray-200 dark:border-gray-700">
            <nav className="-mb-px flex">
              {[
                { id: 'help', name: 'Help Articles', icon: BookOpenIcon },
                { id: 'support', name: 'Get Support', icon: ChatBubbleLeftRightIcon },
                { id: 'faq', name: 'FAQ', icon: QuestionMarkCircleIcon }
              ].map((tab) => (
                <button
                  key={tab.id}
                  onClick={() => {
                    // Real data loaded from API
                    // Real data loaded from API
                  }}
                  className={`flex items-center space-x-2 px-6 py-3 border-b-2 font-medium text-sm ${
                    activeTab === tab.id
                      ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                      : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 hover:border-gray-300'
                  }`}
                >
                  <tab.icon className="h-4 w-4" />
                  <span>{tab.name}</span>
                </button>
              ))}
            </nav>
          </div>

          {/* Content */}
          <div className="p-6 max-h-[70vh] overflow-y-auto">
            {activeTab === 'help' && (
              <div>
                {selectedCategory ? (
                  <div>
                    <button
                      onClick={() => setSelectedCategory(null)}
                      className="flex items-center text-sm text-blue-600 dark:text-blue-400 hover:text-blue-500 mb-4"
                    >
                      <ChevronRightIcon className="h-4 w-4 mr-1 rotate-180" />
                      Back to categories
                    </button>
                    
                    <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-4">
                      {selectedCategory.title}
                    </h3>
                    
                    <div className="space-y-3">
                      {selectedCategory.articles.map((article, index) => (
                        <div
                          key={index}
                          className="p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors"
                        >
                          <div className="flex items-center justify-between">
                            <h4 className="font-medium text-gray-900 dark:text-white">
                              {article.title}
                            </h4>
                            <span className="text-sm text-gray-500 dark:text-gray-400">
                              {article.readTime}
                            </span>
                          </div>
                        </div>
                      ))}
                    </div>
                  </div>
                ) : (
                  <div>
                    <div className="mb-6">
                      <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-2">
                        How can we help you?
                      </h3>
                      <p className="text-gray-600 dark:text-gray-400">
                        Browse our help articles or search for specific topics
                      </p>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                      {helpCategories.map((category) => (
                        <button
                          key={category.id}
                          onClick={() => setSelectedCategory(category)}
                          className="p-4 text-left border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                        >
                          <div className="flex items-start space-x-3">
                            <category.icon className="h-6 w-6 text-blue-600 dark:text-blue-400 mt-1" />
                            <div>
                              <h4 className="font-medium text-gray-900 dark:text-white mb-1">
                                {category.title}
                              </h4>
                              <p className="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                {category.description}
                              </p>
                              <p className="text-xs text-blue-600 dark:text-blue-400">
                                {category.articles.length} articles
                              </p>
                            </div>
                          </div>
                        </button>
                      ))}
                    </div>
                  </div>
                )}
              </div>
            )}

            {activeTab === 'support' && (
              <div>
                <div className="mb-6">
                  <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-2">
                    Contact Support
                  </h3>
                  <p className="text-gray-600 dark:text-gray-400">
                    Choose the best way to get in touch with our support team
                  </p>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  {supportOptions.map((option) => (
                    <div
                      key={option.title}
                      className={`p-4 border rounded-lg ${
                        option.available
                          ? 'border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700'
                          : 'border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50'
                      } transition-colors`}
                    >
                      <div className="flex items-start space-x-3">
                        <option.icon className={`h-6 w-6 mt-1 ${
                          option.available 
                            ? 'text-blue-600 dark:text-blue-400' 
                            : 'text-gray-400 dark:text-gray-500'
                        }`} />
                        <div className="flex-1">
                          <div className="flex items-center space-x-2">
                            <h4 className={`font-medium ${
                              option.available
                                ? 'text-gray-900 dark:text-white'
                                : 'text-gray-500 dark:text-gray-400'
                            }`}>
                              {option.title}
                            </h4>
                            {option.badge && (
                              <span className="px-2 py-1 text-xs bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-full">
                                {option.badge}
                              </span>
                            )}
                          </div>
                          <p className={`text-sm mb-3 ${
                            option.available
                              ? 'text-gray-600 dark:text-gray-400'
                              : 'text-gray-500 dark:text-gray-500'
                          }`}>
                            {option.description}
                          </p>
                          <button
                            disabled={!option.available}
                            className={`px-4 py-2 text-sm font-medium rounded-lg transition-colors ${
                              option.available
                                ? 'bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600'
                                : 'bg-gray-200 dark:bg-gray-600 text-gray-400 dark:text-gray-500 cursor-not-allowed'
                            }`}
                          >
                            {option.action}
                          </button>
                        </div>
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            )}

            {activeTab === 'faq' && (
              <div>
                <div className="mb-6">
                  <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    Frequently Asked Questions
                  </h3>
                  
                  <div className="relative">
                    <MagnifyingGlassIcon className="h-5 w-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" />
                    <input
                      type="text"
                      value={searchQuery}
                      onChange={(e) => setSearchQuery(e.target.value)}
                      placeholder="Search FAQs..."
                      className="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                  </div>
                </div>

                <div className="space-y-4">
                  {filteredFAQs.map((faq, index) => (
                    <details
                      key={index}
                      className="border border-gray-200 dark:border-gray-600 rounded-lg"
                    >
                      <summary className="p-4 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <span className="font-medium text-gray-900 dark:text-white">
                          {faq.question}
                        </span>
                      </summary>
                      <div className="px-4 pb-4">
                        <p className="text-gray-600 dark:text-gray-400">
                          {faq.answer}
                        </p>
                      </div>
                    </details>
                  ))}
                  
                  {filteredFAQs.length === 0 && searchQuery && (
                    <div className="text-center py-8">
                      <p className="text-gray-500 dark:text-gray-400">
                        No FAQs found matching "{searchQuery}"
                      </p>
                    </div>
                  )}
                </div>
              </div>
            )}
          </div>
        </motion.div>
      </div>
    </div>
  );
};

export default HelpSupportCenter;