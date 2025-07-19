import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { 
  PlusIcon,
  CheckIcon,
  ExclamationTriangleIcon,
  ArrowPathIcon,
  Cog6ToothIcon,
  TrashIcon,
  LinkIcon,
  EyeIcon,
  ClockIcon
} from '@heroicons/react/24/outline';

const IntegrationHub = () => {
  const [integrations, setIntegrations] = useState([]);
  const [activeIntegrations, setActiveIntegrations] = useState([]);
  const [selectedCategory, setSelectedCategory] = useState('all');
  const [loading, setLoading] = useState(false);

  const categories = [
    { id: 'all', name: 'All Integrations', count: 24 },
    { id: 'analytics', name: 'Analytics', count: 6 },
    { id: 'social', name: 'Social Media', count: 8 },
    { id: 'payment', name: 'Payments', count: 4 },
    { id: 'email', name: 'Email & Marketing', count: 3 },
    { id: 'productivity', name: 'Productivity', count: 3 }
  ];

  const availableIntegrations = [
    {
      id: 'google-analytics',
      name: 'Google Analytics',
      description: 'Track website traffic and user behavior with detailed analytics',
      category: 'analytics',
      icon: '/integrations/google-analytics.png',
      status: 'available',
      difficulty: 'easy',
      setupTime: '5 minutes',
      features: ['Traffic tracking', 'Conversion tracking', 'Audience insights', 'Real-time data'],
      pricing: 'Free',
      popularity: 95,
      website: 'https://analytics.google.com'
    },
    {
      id: 'stripe',
      name: 'Stripe',
      description: 'Accept payments online with the world\'s leading payment processor',
      category: 'payment',
      icon: '/integrations/stripe.png',
      status: 'connected',
      difficulty: 'medium',
      setupTime: '15 minutes',
      features: ['Online payments', 'Subscription billing', 'International support', 'Fraud protection'],
      pricing: '2.9% + 30¢ per transaction',
      popularity: 92,
      website: 'https://stripe.com'
    },
    {
      id: 'instagram',
      name: 'Instagram',
      description: 'Connect your Instagram account to sync content and analytics',
      category: 'social',
      icon: '/integrations/instagram.png',
      status: 'available',
      difficulty: 'easy',
      setupTime: '3 minutes',
      features: ['Auto-post content', 'Story sync', 'Analytics import', 'Direct messages'],
      pricing: 'Free',
      popularity: 89,
      website: 'https://instagram.com'
    },
    {
      id: 'mailchimp',
      name: 'Mailchimp',
      description: 'Sync your email lists and automate email marketing campaigns',
      category: 'email',
      icon: '/integrations/mailchimp.png',
      status: 'available',
      difficulty: 'easy',
      setupTime: '10 minutes',
      features: ['Email automation', 'List management', 'Campaign analytics', 'A/B testing'],
      pricing: 'Free tier available',
      popularity: 86,
      website: 'https://mailchimp.com'
    },
    {
      id: 'zapier',
      name: 'Zapier',
      description: 'Connect Mewayz with 5,000+ apps to automate workflows',
      category: 'productivity',
      icon: '/integrations/zapier.png',
      status: 'available',
      difficulty: 'medium',
      setupTime: '20 minutes',
      features: ['Workflow automation', '5,000+ app connections', 'Custom triggers', 'Multi-step workflows'],
      pricing: 'Free tier available',
      popularity: 84,
      website: 'https://zapier.com'
    },
    {
      id: 'facebook',
      name: 'Facebook',
      description: 'Manage Facebook pages and sync social media content',
      category: 'social',
      icon: '/integrations/facebook.png',
      status: 'connected',
      difficulty: 'easy',
      setupTime: '5 minutes',
      features: ['Page management', 'Post scheduling', 'Analytics sync', 'Ad integration'],
      pricing: 'Free',
      popularity: 88,
      website: 'https://facebook.com'
    },
    {
      id: 'paypal',
      name: 'PayPal',
      description: 'Accept PayPal payments and manage transactions',
      category: 'payment',
      icon: '/integrations/paypal.png',
      status: 'available',
      difficulty: 'easy',
      setupTime: '8 minutes',
      features: ['PayPal payments', 'International support', 'Buyer protection', 'Mobile payments'],
      pricing: '2.9% + fixed fee',
      popularity: 81,
      website: 'https://paypal.com'
    },
    {
      id: 'google-ads',
      name: 'Google Ads',
      description: 'Track Google Ads performance and optimize campaigns',
      category: 'analytics',
      icon: '/integrations/google-ads.png',
      status: 'available',
      difficulty: 'medium',
      setupTime: '25 minutes',
      features: ['Campaign tracking', 'Conversion optimization', 'Performance metrics', 'Budget insights'],
      pricing: 'Free with ad spend',
      popularity: 79,
      website: 'https://ads.google.com'
    },
    {
      id: 'tiktok',
      name: 'TikTok',
      description: 'Connect TikTok for Business to sync content and analytics',
      category: 'social',
      icon: '/integrations/tiktok.png',
      status: 'available',
      difficulty: 'easy',
      setupTime: '5 minutes',
      features: ['Content sync', 'Analytics import', 'Video management', 'Trending insights'],
      pricing: 'Free',
      popularity: 87,
      website: 'https://tiktok.com'
    },
    {
      id: 'youtube',
      name: 'YouTube',
      description: 'Sync YouTube channel data and video analytics',
      category: 'social',
      icon: '/integrations/youtube.png',
      status: 'available',
      difficulty: 'easy',
      setupTime: '7 minutes',
      features: ['Video sync', 'Channel analytics', 'Subscriber tracking', 'Revenue insights'],
      pricing: 'Free',
      popularity: 90,
      website: 'https://youtube.com'
    },
    {
      id: 'slack',
      name: 'Slack',
      description: 'Get Mewayz notifications and updates in your Slack workspace',
      category: 'productivity',
      icon: '/integrations/slack.png',
      status: 'available',
      difficulty: 'easy',
      setupTime: '3 minutes',
      features: ['Real-time notifications', 'Team collaboration', 'Custom alerts', 'Status updates'],
      pricing: 'Free',
      popularity: 75,
      website: 'https://slack.com'
    },
    {
      id: 'shopify',
      name: 'Shopify',
      description: 'Sync your Shopify store with Mewayz for unified e-commerce',
      category: 'payment',
      icon: '/integrations/shopify.png',
      status: 'available',
      difficulty: 'medium',
      setupTime: '30 minutes',
      features: ['Product sync', 'Order management', 'Inventory tracking', 'Customer data'],
      pricing: 'Free with Shopify plan',
      popularity: 83,
      website: 'https://shopify.com'
    }
  ];

  useEffect(() => {
    setIntegrations(availableIntegrations);
    setActiveIntegrations(availableIntegrations.filter(i => i.status === 'connected'));
  }, []);

  const filteredIntegrations = integrations.filter(integration => 
    selectedCategory === 'all' || integration.category === selectedCategory
  );

  const handleConnect = async (integrationId) => {
    setLoading(true);
    
    // Simulate API call
    setTimeout(() => {
      setIntegrations(prev => 
        prev.map(integration => 
          integration.id === integrationId 
            ? { ...integration, status: 'connected' }
            : integration
        )
      );
      setActiveIntegrations(prev => [
        ...prev,
        integrations.find(i => i.id === integrationId)
      ]);
      setLoading(false);
    }, 2000);
  };

  const handleDisconnect = (integrationId) => {
    setIntegrations(prev => 
      prev.map(integration => 
        integration.id === integrationId 
          ? { ...integration, status: 'available' }
          : integration
      )
    );
    setActiveIntegrations(prev => prev.filter(i => i.id !== integrationId));
  };

  const getDifficultyColor = (difficulty) => {
    switch (difficulty) {
      case 'easy': return 'text-green-600 bg-green-100 dark:bg-green-900 dark:text-green-400';
      case 'medium': return 'text-yellow-600 bg-yellow-100 dark:bg-yellow-900 dark:text-yellow-400';
      case 'hard': return 'text-red-600 bg-red-100 dark:bg-red-900 dark:text-red-400';
      default: return 'text-gray-600 bg-gray-100 dark:bg-gray-900 dark:text-gray-400';
    }
  };

  const getStatusIcon = (status) => {
    switch (status) {
      case 'connected':
        return <CheckIcon className="h-5 w-5 text-green-500" />;
      case 'connecting':
        return <ArrowPathIcon className="h-5 w-5 text-blue-500 animate-spin" />;
      case 'error':
        return <ExclamationTriangleIcon className="h-5 w-5 text-red-500" />;
      default:
        return <PlusIcon className="h-5 w-5 text-gray-400" />;
    }
  };

  return (
    <div className="max-w-7xl mx-auto p-6">
      {/* Header */}
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-gray-900 dark:text-white mb-4">
          Integration Hub
        </h1>
        <p className="text-gray-600 dark:text-gray-300">
          Connect Mewayz with your favorite tools and services to streamline your workflow
        </p>
      </div>

      {/* Active Integrations Summary */}
      {activeIntegrations.length > 0 && (
        <div className="mb-8 p-6 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
          <h2 className="text-lg font-semibold text-green-800 dark:text-green-200 mb-4">
            Active Integrations ({activeIntegrations.length})
          </h2>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            {activeIntegrations.map((integration) => (
              <div key={integration.id} className="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                <div className="flex items-center space-x-3">
                  <img
                    src={`https://ui-avatars.io/api/?name=${encodeURIComponent(integration.name)}&background=random`}
                    alt={integration.name}
                    className="w-8 h-8 rounded"
                  />
                  <div>
                    <h3 className="font-medium text-gray-900 dark:text-white">
                      {integration.name}
                    </h3>
                    <p className="text-xs text-green-600 dark:text-green-400">
                      Connected
                    </p>
                  </div>
                </div>
                <div className="flex items-center space-x-2">
                  <button
                    className="p-1 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300"
                    title="Settings"
                  >
                    <Cog6ToothIcon className="h-4 w-4" />
                  </button>
                  <button
                    onClick={() => handleDisconnect(integration.id)}
                    className="p-1 text-gray-500 hover:text-red-500"
                    title="Disconnect"
                  >
                    <TrashIcon className="h-4 w-4" />
                  </button>
                </div>
              </div>
            ))}
          </div>
        </div>
      )}

      {/* Categories */}
      <div className="mb-8">
        <div className="flex flex-wrap gap-3">
          {categories.map((category) => (
            <button
              key={category.id}
              onClick={() => setSelectedCategory(category.id)}
              className={`px-4 py-2 rounded-lg text-sm font-medium transition-colors ${
                selectedCategory === category.id
                  ? 'bg-blue-600 text-white'
                  : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'
              }`}
            >
              {category.name} ({category.count})
            </button>
          ))}
        </div>
      </div>

      {/* Integrations Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {filteredIntegrations.map((integration) => (
          <motion.div
            key={integration.id}
            layout
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            className="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-xl transition-all duration-300"
          >
            {/* Integration Header */}
            <div className="p-6">
              <div className="flex items-start justify-between mb-4">
                <div className="flex items-center space-x-3">
                  <img
                    src={`https://ui-avatars.io/api/?name=${encodeURIComponent(integration.name)}&background=random`}
                    alt={integration.name}
                    className="w-12 h-12 rounded-lg shadow-sm"
                  />
                  <div>
                    <h3 className="text-lg font-semibold text-gray-900 dark:text-white">
                      {integration.name}
                    </h3>
                    <div className="flex items-center space-x-2">
                      {getStatusIcon(integration.status)}
                      <span className={`text-sm font-medium ${
                        integration.status === 'connected' 
                          ? 'text-green-600 dark:text-green-400' 
                          : 'text-gray-500 dark:text-gray-400'
                      }`}>
                        {integration.status === 'connected' ? 'Connected' : 'Available'}
                      </span>
                    </div>
                  </div>
                </div>
                
                <div className="flex flex-col items-end space-y-1">
                  <span className={`px-2 py-1 text-xs font-medium rounded ${getDifficultyColor(integration.difficulty)}`}>
                    {integration.difficulty}
                  </span>
                  <div className="flex items-center text-xs text-gray-500">
                    <ClockIcon className="h-3 w-3 mr-1" />
                    {integration.setupTime}
                  </div>
                </div>
              </div>

              <p className="text-gray-600 dark:text-gray-300 text-sm mb-4">
                {integration.description}
              </p>

              {/* Features */}
              <div className="mb-4">
                <h4 className="text-sm font-medium text-gray-900 dark:text-white mb-2">
                  Key Features:
                </h4>
                <div className="flex flex-wrap gap-2">
                  {integration.features.slice(0, 3).map((feature, index) => (
                    <span
                      key={index}
                      className="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs rounded"
                    >
                      {feature}
                    </span>
                  ))}
                  {integration.features.length > 3 && (
                    <span className="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs rounded">
                      +{integration.features.length - 3} more
                    </span>
                  )}
                </div>
              </div>

              {/* Pricing & Popularity */}
              <div className="flex items-center justify-between mb-4">
                <div>
                  <span className="text-sm font-medium text-gray-900 dark:text-white">
                    {integration.pricing}
                  </span>
                </div>
                <div className="flex items-center space-x-2">
                  <div className="flex items-center">
                    <div className="w-16 bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 mr-2">
                      <div
                        className="bg-blue-600 h-1.5 rounded-full"
                        style={{ width: `${integration.popularity}%` }}
                      ></div>
                    </div>
                    <span className="text-xs text-gray-500">{integration.popularity}%</span>
                  </div>
                </div>
              </div>
            </div>

            {/* Integration Actions */}
            <div className="px-6 py-4 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
              <div className="flex items-center justify-between">
                <div className="flex items-center space-x-2">
                  <button
                    className="p-2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 rounded-lg hover:bg-white dark:hover:bg-gray-800 transition-colors"
                    title="View Details"
                  >
                    <EyeIcon className="h-4 w-4" />
                  </button>
                  <a
                    href={integration.website}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="p-2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 rounded-lg hover:bg-white dark:hover:bg-gray-800 transition-colors"
                    title="Visit Website"
                  >
                    <LinkIcon className="h-4 w-4" />
                  </a>
                </div>
                
                <div className="flex items-center space-x-2">
                  {integration.status === 'connected' ? (
                    <>
                      <button
                        className="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors"
                      >
                        <Cog6ToothIcon className="h-4 w-4 mr-1 inline" />
                        Settings
                      </button>
                      <button
                        onClick={() => handleDisconnect(integration.id)}
                        className="px-3 py-2 text-sm font-medium text-red-600 bg-white dark:bg-gray-700 border border-red-300 dark:border-red-600 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                      >
                        Disconnect
                      </button>
                    </>
                  ) : (
                    <button
                      onClick={() => handleConnect(integration.id)}
                      disabled={loading}
                      className="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    >
                      {loading ? (
                        <ArrowPathIcon className="h-4 w-4 animate-spin inline mr-1" />
                      ) : (
                        <PlusIcon className="h-4 w-4 inline mr-1" />
                      )}
                      Connect
                    </button>
                  )}
                </div>
              </div>
            </div>
          </motion.div>
        ))}
      </div>

      {/* Empty State */}
      {filteredIntegrations.length === 0 && (
        <div className="text-center py-12">
          <LinkIcon className="h-12 w-12 text-gray-400 mx-auto mb-4" />
          <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-2">
            No integrations found
          </h3>
          <p className="text-gray-600 dark:text-gray-300">
            Try selecting a different category or check back later for new integrations
          </p>
        </div>
      )}
    </div>
  );
};

export default IntegrationHub;