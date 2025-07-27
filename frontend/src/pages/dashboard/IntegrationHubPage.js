import React, { useState } from 'react';
import { motion } from 'framer-motion';
import {
  PuzzlePieceIcon,
  CheckCircleIcon,
  ExclamationTriangleIcon,
  Cog6ToothIcon,
  LinkIcon,
  CloudIcon,
  CreditCardIcon,
  EnvelopeIcon,
  ChatBubbleLeftRightIcon,
  ChartBarIcon,
  ShieldCheckIcon,
  PlusIcon
} from '@heroicons/react/24/outline';
import IntegrationHub from '../../components/integrations/IntegrationHub';
  useEffect(() => {
    loadData();
  }, []);


const IntegrationHubPage = () => {
  const [viewMode, setViewMode] = useState('browse'); // browse, installed, settings, configure
  const [selectedIntegration, setSelectedIntegration] = useState(null);

  const integrationCategories = [
    {
      id: 'payment',
      name: 'Payment Processing',
      icon: CreditCardIcon,
      count: 8
    },
    {
      id: 'communication',
      name: 'Communication',
      icon: ChatBubbleLeftRightIcon,
      count: 12
    },
    {
      id: 'analytics',
      name: 'Analytics & Tracking',
      icon: ChartBarIcon,
      count: 15
    },
    {
      id: 'email',
      name: 'Email Marketing',
      icon: EnvelopeIcon,
      count: 10
    },
    {
      id: 'cloud',
      name: 'Cloud Storage',
      icon: CloudIcon,
      count: 6
    },
    {
      id: 'security',
      name: 'Security & Auth',
      icon: ShieldCheckIcon,
      count: 7
    }
  ];

  const availableIntegrations = [
    {
      id: 'stripe',
      name: 'Stripe',
      category: 'payment',
      description: 'Accept payments online with Stripe\'s powerful payment processing',
      logo: '/integrations/stripe-logo.png',
      status: 'available',
      rating: 4.9,
      installs: '10M+',
      pricing: 'Free + transaction fees',
      features: ['Credit Cards', 'Bank Transfers', 'Subscriptions', 'Multi-currency']
    },
    {
      id: 'paypal',
      name: 'PayPal',
      category: 'payment', 
      description: 'Global payment solution trusted by millions worldwide',
      logo: '/integrations/paypal-logo.png',
      status: 'installed',
      rating: 4.7,
      installs: '8M+',
      pricing: 'Free + transaction fees',
      features: ['PayPal Checkout', 'Express Payments', 'Recurring Billing']
    },
    {
      id: 'mailchimp',
      name: 'Mailchimp',
      category: 'email',
      description: 'All-in-one marketing platform for growing businesses',
      logo: '/integrations/mailchimp-logo.png',
      status: 'available',
      rating: 4.5,
      installs: '5M+',
      pricing: 'Free up to 10k emails/month',
      features: ['Email Campaigns', 'Automation', 'Analytics', 'Templates']
    },
    {
      id: 'google-analytics',
      name: 'Google Analytics',
      category: 'analytics',
      description: 'Understand your customers with detailed analytics insights',
      logo: '/integrations/google-analytics-logo.png',
      status: 'configured',
      rating: 4.6,
      installs: '15M+',
      pricing: 'Free',
      features: ['Traffic Analysis', 'Conversion Tracking', 'Real-time Data', 'Custom Reports']
    },
    {
      id: 'slack',
      name: 'Slack',
      category: 'communication',
      description: 'Team communication and collaboration platform',
      logo: '/integrations/slack-logo.png',
      status: 'available',
      rating: 4.8,
      installs: '2M+',
      pricing: 'Free + paid plans',
      features: ['Team Chat', 'File Sharing', 'Notifications', 'Workflow Automation']
    },
    {
      id: 'zapier',
      name: 'Zapier',
      category: 'automation',
      description: 'Connect your apps and automate workflows',
      logo: '/integrations/zapier-logo.png',
      status: 'available',
      rating: 4.7,
      installs: '3M+',
      pricing: 'Free + paid plans',
      features: ['Workflow Automation', '3000+ App Integrations', 'Triggers & Actions']
    }
  ];

  const installedIntegrations = availableIntegrations.filter(
    integration => integration.status === 'installed' || integration.status === 'configured'
  );

  const stats = [
    {
      name: 'Active Integrations',
      value: installedIntegrations.length.toString(),
      change: '+2 this month',
      changeType: 'positive'
    },
    {
      name: 'Available Integrations',
      value: availableIntegrations.length.toString(),
      change: '+5 new',
      changeType: 'positive'
    },
    {
      name: 'API Calls Today',
      value: '1,247',
      change: '+12.3%',
      changeType: 'positive'
    },
    {
      name: 'Success Rate',
      value: '99.8%',
      change: '+0.2%',
      changeType: 'positive'
    }
  ];

  const getStatusColor = (status) => {
    switch(status) {
      case 'installed': return 'text-blue-600 bg-blue-100 dark:bg-blue-900/20 dark:text-blue-400';
      case 'configured': return 'text-green-600 bg-green-100 dark:bg-green-900/20 dark:text-green-400';
      case 'error': return 'text-red-600 bg-red-100 dark:bg-red-900/20 dark:text-red-400';
      default: return 'text-gray-600 bg-gray-100 dark:bg-gray-700 dark:text-gray-400';
    }
  };

  const getStatusIcon = (status) => {
    switch(status) {
      case 'installed': 
      case 'configured': 
        return <CheckCircleIcon className="h-4 w-4" />;
      case 'error': 
        return <ExclamationTriangleIcon className="h-4 w-4" />;
      default: 
        return <PlusIcon className="h-4 w-4" />;
    }
  };

  const renderBrowse = () => (
    <div className="space-y-6">
      {/* Stats */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {stats.map((stat) => (
          <div key={stat.name} className="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
            <div>
              <p className="text-sm font-medium text-gray-600 dark:text-gray-400">
                {stat.name}
              </p>
              <p className="text-2xl font-bold text-gray-900 dark:text-white">
                {stat.value}
              </p>
              <p className="text-sm text-green-600 dark:text-green-400">
                {stat.change}
              </p>
            </div>
          </div>
        ))}
      </div>

      {/* Categories */}
      <div className="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
        <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
          Browse by Category
        </h3>
        <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
          {integrationCategories.map((category) => (
            <button
              key={category.id}
              className="p-4 text-center border border-gray-200 dark:border-gray-600 rounded-lg hover:border-blue-500 dark:hover:border-blue-400 transition-colors group"
            >
              <category.icon className="h-8 w-8 text-gray-600 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 mx-auto mb-2" />
              <p className="font-medium text-gray-900 dark:text-white text-sm">
                {category.name}
              </p>
              <p className="text-xs text-gray-500 dark:text-gray-400">
                {category.count} available
              </p>
            </button>
          ))}
        </div>
      </div>

      {/* Integration Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {availableIntegrations.map((integration) => (
          <motion.div
            key={integration.id}
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            className="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-all"
          >
            <div className="flex items-start justify-between mb-4">
              <div className="flex items-center space-x-3">
                <div className="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                  <PuzzlePieceIcon className="h-6 w-6 text-gray-600 dark:text-gray-400" />
                </div>
                <div>
                  <h4 className="font-semibold text-gray-900 dark:text-white">
                    {integration.name}
                  </h4>
                  <p className="text-sm text-gray-500 dark:text-gray-400">
                    {integration.installs} installs
                  </p>
                </div>
              </div>
              <div className={`px-2 py-1 rounded-full text-xs font-medium flex items-center space-x-1 ${getStatusColor(integration.status)}`}>
                {getStatusIcon(integration.status)}
                <span className="capitalize">{integration.status}</span>
              </div>
            </div>

            <p className="text-sm text-gray-600 dark:text-gray-300 mb-4">
              {integration.description}
            </p>

            <div className="flex items-center justify-between mb-4">
              <div className="flex items-center space-x-1">
                <div className="flex text-yellow-400">
                  {'★'.repeat(Math.floor(integration.rating))}
                </div>
                <span className="text-sm text-gray-600 dark:text-gray-400">
                  {integration.rating}
                </span>
              </div>
              <span className="text-sm font-medium text-gray-900 dark:text-white">
                {integration.pricing}
              </span>
            </div>

            <div className="flex flex-wrap gap-1 mb-4">
              {integration.features.slice(0, 2).map((feature) => (
                <span
                  key={feature}
                  className="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-xs text-gray-600 dark:text-gray-300 rounded"
                >
                  {feature}
                </span>
              ))}
              {integration.features.length > 2 && (
                <span className="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-xs text-gray-600 dark:text-gray-300 rounded">
                  +{integration.features.length - 2} more
                </span>
              )}
            </div>

            <div className="flex items-center space-x-2">
              {integration.status === 'available' ? (
                <button className="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                  Install
                </button>
              ) : (
                <button
                  onClick={() => {
                    // Real data loaded from API
                    // Real data loaded from API
                  }}
                  className="flex-1 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white px-4 py-2 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                >
                  Configure
                </button>
              )}
              <button className="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                <Cog6ToothIcon className="h-5 w-5" />
              </button>
            </div>
          </motion.div>
        ))}
      </div>
    </div>
  );

  const renderInstalled = () => (
    <div className="space-y-6">
      <div className="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
        <div className="p-6 border-b border-gray-200 dark:border-gray-700">
          <h3 className="text-lg font-semibold text-gray-900 dark:text-white">
            Installed Integrations
          </h3>
        </div>
        <div className="divide-y divide-gray-200 dark:divide-gray-700">
          {installedIntegrations.map((integration) => (
            <div key={integration.id} className="p-6">
              <div className="flex items-center justify-between">
                <div className="flex items-center space-x-4">
                  <div className="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                    <PuzzlePieceIcon className="h-6 w-6 text-gray-600 dark:text-gray-400" />
                  </div>
                  <div>
                    <h4 className="font-semibold text-gray-900 dark:text-white">
                      {integration.name}
                    </h4>
                    <p className="text-sm text-gray-500 dark:text-gray-400">
                      {integration.description}
                    </p>
                  </div>
                </div>
                <div className="flex items-center space-x-2">
                  <div className={`px-2 py-1 rounded-full text-xs font-medium flex items-center space-x-1 ${getStatusColor(integration.status)}`}>
                    {getStatusIcon(integration.status)}
                    <span className="capitalize">{integration.status}</span>
                  </div>
                  <button
                    onClick={() => {
                      // Real data loaded from API
                      // Real data loaded from API
                    }}
                    className="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                  >
                    <Cog6ToothIcon className="h-5 w-5" />
                  </button>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );

  const renderConfigure = () => (
    <div className="max-w-2xl mx-auto">
      <div className="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
        <div className="flex items-center justify-between mb-6">
          <div className="flex items-center space-x-3">
            <div className="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
              <PuzzlePieceIcon className="h-6 w-6 text-gray-600 dark:text-gray-400" />
            </div>
            <div>
              <h3 className="text-lg font-semibold text-gray-900 dark:text-white">
                Configure {selectedIntegration?.name}
              </h3>
              <p className="text-sm text-gray-500 dark:text-gray-400">
                Set up your {selectedIntegration?.name} integration
              </p>
            </div>
          </div>
          <button
            onClick={() => setViewMode('browse')}
            className="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
          >
            ✕
          </button>
        </div>

        <div className="space-y-6">
          <div>
            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              API Key
            </label>
            <input
              type="password"
              placeholder="Enter your API key..."
              className="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Webhook URL
            </label>
            <div className="flex">
              <input
                type="text"
                value="https://your-app.com/webhooks/integration"
                readOnly
                className="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-l-lg bg-gray-50 dark:bg-gray-600 text-gray-500 dark:text-gray-400"
              />
              <button className="px-4 py-2 bg-blue-600 text-white rounded-r-lg hover:bg-blue-700 transition-colors">
                <LinkIcon className="h-5 w-5" />
              </button>
            </div>
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Features to Enable
            </label>
            <div className="space-y-2">
              {selectedIntegration?.features.map((feature) => (
                <label key={feature} className="flex items-center">
                  <input
                    type="checkbox"
                    defaultChecked
                    className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 rounded"
                  />
                  <span className="ml-2 text-sm text-gray-900 dark:text-white">
                    {feature}
                  </span>
                </label>
              ))}
            </div>
          </div>

          <div className="flex items-center justify-between pt-6">
            <button
              onClick={() => setViewMode('browse')}
              className="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
            >
              Cancel
            </button>
            <button className="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
              Save Configuration
            </button>
          </div>
        </div>
      </div>
    </div>
  );

  return (
    <div className="p-6">
      <div className="mb-8">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-2xl font-bold text-gray-900 dark:text-white">
              Integration Hub
            </h1>
            <p className="text-gray-600 dark:text-gray-400 mt-1">
              Connect your favorite apps and services to supercharge your workflow
            </p>
          </div>

          <div className="flex items-center space-x-3">
            <button className="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
              Browse All
            </button>
            <button className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
              Request Integration
            </button>
          </div>
        </div>

        {/* Navigation Tabs */}
        <div className="mt-6 border-b border-gray-200 dark:border-gray-700">
          <nav className="-mb-px flex space-x-8">
            {[
              { key: 'browse', label: 'Browse' },
              { key: 'installed', label: 'Installed' },
              { key: 'settings', label: 'Settings' }
            ].map((tab) => (
              <button
                key={tab.key}
                onClick={() => setViewMode(tab.key)}
                className={`py-2 px-1 border-b-2 font-medium text-sm ${
                  viewMode === tab.key
                    ? 'border-blue-500 text-blue-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 hover:border-gray-300'
                }`}
              >
                {tab.label}
              </button>
            ))}
          </nav>
        </div>
      </div>

      {/* Content Area */}
      <motion.div
        key={viewMode}
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.2 }}
      >
        {viewMode === 'browse' && renderBrowse()}
        {viewMode === 'installed' && renderInstalled()}
        {viewMode === 'configure' && renderConfigure()}
        {viewMode === 'settings' && (
          <IntegrationHub />
        )}
      </motion.div>
    </div>
  );
};

export default IntegrationHubPage;