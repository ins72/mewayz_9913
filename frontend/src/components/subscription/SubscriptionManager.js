import React, { useState, useEffect, useContext } from 'react';
import { motion } from 'framer-motion';
import { 
  CreditCardIcon, 
  CheckIcon,
  SparklesIcon,
  StarIcon,
  ShieldCheckIcon,
  LightningBoltIcon
} from '@heroicons/react/24/outline';
import { AuthContext } from '../contexts/AuthContext';

const SubscriptionManager = () => {
  const { user } = useContext(AuthContext);
  const [loading, setLoading] = useState(false);
  const [subscriptions, setSubscriptions] = useState([]);
  const [currentPlan, setCurrentPlan] = useState(null);
  const [usage, setUsage] = useState(null);

  const plans = [
    {
      id: 'free',
      name: 'Free Starter',
      price: 0,
      interval: 'month',
      description: 'Perfect for getting started',
      features: [
        '1 Workspace',
        '3 Bio Sites',
        'Basic AI Features (10 requests/month)',
        'Community Support',
        'Basic Analytics'
      ],
      limitations: [
        'No custom domains',
        'Mewayz branding',
        'Limited storage (1GB)'
      ],
      color: 'from-gray-500 to-gray-600',
      popular: false
    },
    {
      id: 'pro',
      name: 'Professional',
      price: 29,
      interval: 'month',
      description: 'Best for creators and small businesses',
      features: [
        '5 Workspaces',
        'Unlimited Bio Sites',
        'Advanced AI Features (1000 requests/month)',
        'Priority Support',
        'Custom Domains',
        'Advanced Analytics',
        'White-label Options',
        'Team Collaboration',
        'API Access'
      ],
      limitations: [],
      color: 'from-blue-500 to-purple-600',
      popular: true
    },
    {
      id: 'enterprise',
      name: 'Enterprise',
      price: 99,
      interval: 'month',
      description: 'For larger teams and businesses',
      features: [
        'Unlimited Workspaces',
        'Unlimited Bio Sites',
        'Enterprise AI Features (Unlimited)',
        '24/7 Phone Support',
        'Custom Integrations',
        'Advanced Security',
        'Dedicated Account Manager',
        'Custom Training',
        'SLA Guarantee',
        'On-premise Option'
      ],
      limitations: [],
      color: 'from-purple-600 to-pink-600',
      popular: false
    }
  ];

  const addOns = [
    {
      id: 'extra_ai',
      name: 'Extra AI Credits',
      description: 'Additional AI requests for power users',
      price: 10,
      unit: '1000 requests'
    },
    {
      id: 'premium_support',
      name: 'Premium Support',
      description: 'Priority support with faster response times',
      price: 15,
      unit: 'month'
    },
    {
      id: 'custom_integration',
      name: 'Custom Integration',
      description: 'Connect with your favorite tools',
      price: 50,
      unit: 'one-time'
    }
  ];

  useEffect(() => {
    fetchSubscriptionData();
  }, []);

  const fetchSubscriptionData = async () => {
    try {
      const response = await fetch(`${process.env.REACT_APP_BACKEND_URL}/api/subscriptions/current`, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        }
      });
      
      if (response.ok) {
        const data = await response.json();
        setCurrentPlan(data.current_plan);
        setUsage(data.usage);
        setSubscriptions(data.subscriptions || []);
      }
    } catch (error) {
      console.error('Failed to fetch subscription data:', error);
      // Set default data for demo
      setCurrentPlan({
        id: 'free',
        status: 'active',
        current_period_end: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString()
      });
      setUsage({
        ai_requests: 5,
        ai_requests_limit: 10,
        workspaces: 1,
        workspaces_limit: 1,
        bio_sites: 2,
        bio_sites_limit: 3,
        storage_used: 0.2,
        storage_limit: 1
      });
    }
  };

  const handleUpgrade = async (planId) => {
    setLoading(true);
    try {
      const response = await fetch(`${process.env.REACT_APP_BACKEND_URL}/api/subscriptions/upgrade`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        },
        body: JSON.stringify({ plan_id: planId })
      });

      if (response.ok) {
        const data = await response.json();
        if (data.checkout_url) {
          window.location.href = data.checkout_url;
        }
      } else {
        alert('Failed to initiate upgrade. Please try again.');
      }
    } catch (error) {
      console.error('Upgrade failed:', error);
      alert('Failed to initiate upgrade. Please try again.');
    } finally {
      setLoading(false);
    }
  };

  const handleCancelSubscription = async () => {
    if (!confirm('Are you sure you want to cancel your subscription? You\'ll lose access to premium features at the end of your billing period.')) {
      return;
    }

    setLoading(true);
    try {
      const response = await fetch(`${process.env.REACT_APP_BACKEND_URL}/api/subscriptions/cancel`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        }
      });

      if (response.ok) {
        alert('Subscription cancelled successfully. You\'ll retain access until the end of your billing period.');
        fetchSubscriptionData();
      }
    } catch (error) {
      console.error('Cancellation failed:', error);
      alert('Failed to cancel subscription. Please try again.');
    } finally {
      setLoading(false);
    }
  };

  const getUsagePercentage = (used, limit) => {
    return Math.min((used / limit) * 100, 100);
  };

  const getUsageColor = (percentage) => {
    if (percentage >= 90) return 'text-red-500';
    if (percentage >= 75) return 'text-yellow-500';
    return 'text-green-500';
  };

  return (
    <div className="max-w-7xl mx-auto p-6 space-y-8">
      {/* Current Plan Overview */}
      <div className="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
        <h2 className="text-2xl font-bold text-gray-900 dark:text-white mb-6">
          Subscription Overview
        </h2>
        
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          {/* Current Plan */}
          <div className="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg p-6 text-white">
            <h3 className="text-lg font-semibold mb-2">Current Plan</h3>
            <p className="text-2xl font-bold">
              {plans.find(p => p.id === currentPlan?.id)?.name || 'Free Starter'}
            </p>
            <p className="text-blue-100 mt-1">
              ${plans.find(p => p.id === currentPlan?.id)?.price || 0}/month
            </p>
            {currentPlan?.current_period_end && (
              <p className="text-sm text-blue-100 mt-4">
                {currentPlan.status === 'active' ? 'Renews' : 'Expires'} on{' '}
                {new Date(currentPlan.current_period_end).toLocaleDateString()}
              </p>
            )}
          </div>

          {/* Usage Stats */}
          <div className="col-span-2 space-y-4">
            {usage && (
              <>
                {/* AI Requests */}
                <div>
                  <div className="flex justify-between items-center mb-2">
                    <span className="text-sm font-medium text-gray-700 dark:text-gray-300">
                      AI Requests
                    </span>
                    <span className={`text-sm font-semibold ${getUsageColor(getUsagePercentage(usage.ai_requests, usage.ai_requests_limit))}`}>
                      {usage.ai_requests} / {usage.ai_requests_limit}
                    </span>
                  </div>
                  <div className="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div
                      className={`h-2 rounded-full transition-all ${
                        getUsagePercentage(usage.ai_requests, usage.ai_requests_limit) >= 90
                          ? 'bg-red-500'
                          : getUsagePercentage(usage.ai_requests, usage.ai_requests_limit) >= 75
                          ? 'bg-yellow-500'
                          : 'bg-green-500'
                      }`}
                      style={{ width: `${getUsagePercentage(usage.ai_requests, usage.ai_requests_limit)}%` }}
                    />
                  </div>
                </div>

                {/* Workspaces */}
                <div>
                  <div className="flex justify-between items-center mb-2">
                    <span className="text-sm font-medium text-gray-700 dark:text-gray-300">
                      Workspaces
                    </span>
                    <span className="text-sm font-semibold text-gray-600 dark:text-gray-400">
                      {usage.workspaces} / {usage.workspaces_limit}
                    </span>
                  </div>
                  <div className="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div
                      className="bg-blue-500 h-2 rounded-full transition-all"
                      style={{ width: `${getUsagePercentage(usage.workspaces, usage.workspaces_limit)}%` }}
                    />
                  </div>
                </div>

                {/* Bio Sites */}
                <div>
                  <div className="flex justify-between items-center mb-2">
                    <span className="text-sm font-medium text-gray-700 dark:text-gray-300">
                      Bio Sites
                    </span>
                    <span className="text-sm font-semibold text-gray-600 dark:text-gray-400">
                      {usage.bio_sites} / {usage.bio_sites_limit === -1 ? '∞' : usage.bio_sites_limit}
                    </span>
                  </div>
                  <div className="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div
                      className="bg-purple-500 h-2 rounded-full transition-all"
                      style={{ 
                        width: usage.bio_sites_limit === -1 
                          ? '20%' 
                          : `${getUsagePercentage(usage.bio_sites, usage.bio_sites_limit)}%` 
                      }}
                    />
                  </div>
                </div>

                {/* Storage */}
                <div>
                  <div className="flex justify-between items-center mb-2">
                    <span className="text-sm font-medium text-gray-700 dark:text-gray-300">
                      Storage
                    </span>
                    <span className="text-sm font-semibold text-gray-600 dark:text-gray-400">
                      {usage.storage_used}GB / {usage.storage_limit === -1 ? '∞' : `${usage.storage_limit}GB`}
                    </span>
                  </div>
                  <div className="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div
                      className="bg-indigo-500 h-2 rounded-full transition-all"
                      style={{ 
                        width: usage.storage_limit === -1 
                          ? '5%' 
                          : `${getUsagePercentage(usage.storage_used, usage.storage_limit)}%` 
                      }}
                    />
                  </div>
                </div>
              </>
            )}
          </div>
        </div>
      </div>

      {/* Available Plans */}
      <div className="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
        <h2 className="text-2xl font-bold text-gray-900 dark:text-white mb-6">
          Available Plans
        </h2>
        
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          {plans.map((plan) => (
            <motion.div
              key={plan.id}
              whileHover={{ scale: 1.02 }}
              className={`relative border-2 rounded-xl p-6 transition-all ${
                currentPlan?.id === plan.id
                  ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                  : 'border-gray-200 dark:border-gray-700 hover:border-gray-300'
              } ${plan.popular ? 'ring-2 ring-blue-500 ring-offset-2' : ''}`}
            >
              {plan.popular && (
                <div className="absolute -top-3 left-1/2 transform -translate-x-1/2">
                  <span className={`bg-gradient-to-r ${plan.color} text-white px-4 py-1 rounded-full text-sm font-medium flex items-center`}>
                    <StarIcon className="h-4 w-4 mr-1" />
                    Most Popular
                  </span>
                </div>
              )}
              
              {currentPlan?.id === plan.id && (
                <div className="absolute -top-3 right-4">
                  <span className="bg-green-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                    Current Plan
                  </span>
                </div>
              )}

              <div className="text-center mb-6">
                <h3 className="text-xl font-bold text-gray-900 dark:text-white mb-2">
                  {plan.name}
                </h3>
                <div className="mb-2">
                  <span className="text-4xl font-bold text-gray-900 dark:text-white">
                    ${plan.price}
                  </span>
                  <span className="text-gray-600 dark:text-gray-400 ml-1">
                    /{plan.interval}
                  </span>
                </div>
                <p className="text-gray-600 dark:text-gray-400">
                  {plan.description}
                </p>
              </div>

              <ul className="space-y-3 mb-6">
                {plan.features.map((feature, index) => (
                  <li key={index} className="flex items-start">
                    <CheckIcon className="h-5 w-5 text-green-500 mr-2 mt-0.5 flex-shrink-0" />
                    <span className="text-sm text-gray-700 dark:text-gray-300">
                      {feature}
                    </span>
                  </li>
                ))}
                {plan.limitations.length > 0 && (
                  <li className="pt-2 border-t border-gray-200 dark:border-gray-700">
                    <div className="text-xs text-gray-500 dark:text-gray-400 font-medium mb-2">
                      Limitations:
                    </div>
                    {plan.limitations.map((limitation, index) => (
                      <div key={index} className="text-xs text-gray-500 dark:text-gray-400">
                        • {limitation}
                      </div>
                    ))}
                  </li>
                )}
              </ul>

              <div className="space-y-2">
                {currentPlan?.id === plan.id ? (
                  currentPlan?.id !== 'free' ? (
                    <button
                      onClick={handleCancelSubscription}
                      disabled={loading}
                      className="w-full py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors disabled:opacity-50"
                    >
                      Cancel Subscription
                    </button>
                  ) : (
                    <div className="w-full py-2 bg-gray-100 dark:bg-gray-700 text-gray-500 rounded-lg text-center">
                      Current Plan
                    </div>
                  )
                ) : (
                  <button
                    onClick={() => handleUpgrade(plan.id)}
                    disabled={loading}
                    className={`w-full py-3 rounded-lg font-medium transition-colors disabled:opacity-50 ${
                      plan.popular
                        ? `bg-gradient-to-r ${plan.color} text-white hover:opacity-90`
                        : 'bg-gray-900 dark:bg-white text-white dark:text-gray-900 hover:bg-gray-800 dark:hover:bg-gray-100'
                    }`}
                  >
                    {loading ? 'Processing...' : 
                     currentPlan?.id === 'free' ? 'Upgrade' : 
                     plan.price > (plans.find(p => p.id === currentPlan?.id)?.price || 0) ? 'Upgrade' : 'Downgrade'}
                  </button>
                )}
              </div>
            </motion.div>
          ))}
        </div>
      </div>

      {/* Add-ons */}
      <div className="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
        <h2 className="text-2xl font-bold text-gray-900 dark:text-white mb-6">
          Add-ons & Extras
        </h2>
        
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          {addOns.map((addon) => (
            <div key={addon.id} className="border border-gray-200 dark:border-gray-700 rounded-lg p-6">
              <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                {addon.name}
              </h3>
              <p className="text-gray-600 dark:text-gray-400 text-sm mb-4">
                {addon.description}
              </p>
              <div className="flex items-center justify-between">
                <span className="text-xl font-bold text-gray-900 dark:text-white">
                  ${addon.price}
                </span>
                <span className="text-sm text-gray-500">
                  per {addon.unit}
                </span>
              </div>
              <button className="w-full mt-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                Add to Plan
              </button>
            </div>
          ))}
        </div>
      </div>

      {/* Billing History */}
      {subscriptions.length > 0 && (
        <div className="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
          <h2 className="text-2xl font-bold text-gray-900 dark:text-white mb-6">
            Billing History
          </h2>
          
          <div className="space-y-4">
            {subscriptions.map((subscription) => (
              <div key={subscription.id} className="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                <div>
                  <p className="font-medium text-gray-900 dark:text-white">
                    {subscription.plan_name}
                  </p>
                  <p className="text-sm text-gray-600 dark:text-gray-400">
                    {new Date(subscription.created_at).toLocaleDateString()} - {new Date(subscription.current_period_end).toLocaleDateString()}
                  </p>
                </div>
                <div className="text-right">
                  <p className="font-bold text-gray-900 dark:text-white">
                    ${subscription.amount}
                  </p>
                  <span className={`inline-block px-2 py-1 rounded-full text-xs ${
                    subscription.status === 'active' 
                      ? 'bg-green-100 text-green-800' 
                      : 'bg-gray-100 text-gray-800'
                  }`}>
                    {subscription.status}
                  </span>
                </div>
              </div>
            ))}
          </div>
        </div>
      )}
    </div>
  );
};

export default SubscriptionManager;