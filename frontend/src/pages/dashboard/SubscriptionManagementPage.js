import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { useAuth } from '../../contexts/AuthContext';
import { useNotification } from '../../contexts/NotificationContext';
import {
  CreditCardIcon,
  CheckIcon,
  XMarkIcon,
  StarIcon,
  SparklesIcon,
  TrophyIcon,
  ShieldCheckIcon,
  BoltIcon,
  PlusIcon,
  MinusIcon,
  CogIcon,
  ArrowPathIcon,
  ExclamationTriangleIcon,
  InformationCircleIcon
} from '@heroicons/react/24/outline';
import {
  StarIcon as StarIconSolid,
  TrophyIcon as TrophyIconSolid
} from '@heroicons/react/24/solid';

const SubscriptionManagementPage = () => {
  const { user } = useAuth();
  const { success, error } = useNotification();
  
  const [currentSubscription, setCurrentSubscription] = useState(null);
  const [selectedFeatures, setSelectedFeatures] = useState([]);
  const [paymentMethods, setPaymentMethods] = useState([]);
  const [showAddPayment, setShowAddPayment] = useState(false);
  const [loading, setLoading] = useState(true);
  const [billingCycle, setBillingCycle] = useState('monthly');

  // All 40 available features organized by category
  const allFeatures = [
    // Social Media Management (8 features)
    { 
      id: 'instagram-database', 
      name: 'Instagram Database', 
      description: 'Access to comprehensive Instagram user database with advanced filtering',
      category: 'Social Media',
      essential: true,
      icon: '📸'
    },
    { 
      id: 'post-scheduler', 
      name: 'Post Scheduler', 
      description: 'Schedule posts across multiple social media platforms',
      category: 'Social Media',
      essential: true,
      icon: '📅'
    },
    { 
      id: 'hashtag-research', 
      name: 'Hashtag Research', 
      description: 'Trending hashtag suggestions and performance tracking',
      category: 'Social Media',
      essential: false,
      icon: '#️⃣'
    },
    { 
      id: 'social-analytics', 
      name: 'Social Analytics', 
      description: 'Advanced social media performance analytics',
      category: 'Social Media',
      essential: true,
      icon: '📊'
    },
    { 
      id: 'content-calendar', 
      name: 'Content Calendar', 
      description: 'Visual content planning and management',
      category: 'Social Media',
      essential: false,
      icon: '🗓️'
    },
    { 
      id: 'multi-platform-posting', 
      name: 'Multi-Platform Posting', 
      description: 'Post to Instagram, Facebook, Twitter, LinkedIn, TikTok simultaneously',
      category: 'Social Media',
      essential: false,
      icon: '🌐'
    },
    { 
      id: 'auto-posting', 
      name: 'AI Auto-Posting', 
      description: 'AI-suggested optimal posting times and content',
      category: 'Social Media',
      essential: false,
      icon: '🤖'
    },
    { 
      id: 'social-listening', 
      name: 'Social Listening', 
      description: 'Monitor mentions and conversations across platforms',
      category: 'Social Media',
      essential: false,
      icon: '👂'
    },

    // Link in Bio (6 features)
    { 
      id: 'bio-builder', 
      name: 'Bio Page Builder', 
      description: 'Drag & drop bio page builder with templates',
      category: 'Link in Bio',
      essential: true,
      icon: '🔗'
    },
    { 
      id: 'custom-domains', 
      name: 'Custom Domains', 
      description: 'Connect your own domain to bio pages',
      category: 'Link in Bio',
      essential: false,
      icon: '🌐'
    },
    { 
      id: 'bio-analytics', 
      name: 'Bio Analytics', 
      description: 'Click tracking and visitor analytics for bio pages',
      category: 'Link in Bio',
      essential: true,
      icon: '📈'
    },
    { 
      id: 'qr-generator', 
      name: 'QR Code Generator', 
      description: 'Generate QR codes for offline sharing',
      category: 'Link in Bio',
      essential: false,
      icon: '📱'
    },
    { 
      id: 'bio-templates', 
      name: 'Premium Templates', 
      description: 'Access to premium bio page templates',
      category: 'Link in Bio',
      essential: false,
      icon: '🎨'
    },
    { 
      id: 'dynamic-content', 
      name: 'Dynamic Content', 
      description: 'Real-time updates from social feeds and APIs',
      category: 'Link in Bio',
      essential: false,
      icon: '⚡'
    },

    // E-commerce (8 features)
    { 
      id: 'product-catalog', 
      name: 'Product Catalog', 
      description: 'Unlimited products with variants and descriptions',
      category: 'E-commerce',
      essential: true,
      icon: '🛍️'
    },
    { 
      id: 'payment-processing', 
      name: 'Payment Processing', 
      description: 'Stripe, PayPal, and other payment gateway integration',
      category: 'E-commerce',
      essential: true,
      icon: '💳'
    },
    { 
      id: 'inventory-management', 
      name: 'Inventory Management', 
      description: 'Stock tracking and low-stock alerts',
      category: 'E-commerce',
      essential: false,
      icon: '📦'
    },
    { 
      id: 'order-management', 
      name: 'Order Management', 
      description: 'Complete order processing and tracking system',
      category: 'E-commerce',
      essential: true,
      icon: '📋'
    },
    { 
      id: 'shipping-integration', 
      name: 'Shipping Integration', 
      description: 'Calculate shipping rates and print labels',
      category: 'E-commerce',
      essential: false,
      icon: '🚚'
    },
    { 
      id: 'marketplace', 
      name: 'Marketplace', 
      description: 'Multi-vendor marketplace with seller management',
      category: 'E-commerce',
      essential: false,
      icon: '🏪'
    },
    { 
      id: 'discount-codes', 
      name: 'Discount Codes', 
      description: 'Create and manage promotional discount codes',
      category: 'E-commerce',
      essential: false,
      icon: '🎫'
    },
    { 
      id: 'review-system', 
      name: 'Review System', 
      description: 'Customer reviews and ratings for products',
      category: 'E-commerce',
      essential: false,
      icon: '⭐'
    },

    // Courses & Community (6 features)
    { 
      id: 'video-hosting', 
      name: 'Video Hosting', 
      description: 'Secure video hosting with built-in player',
      category: 'Courses',
      essential: true,
      icon: '🎥'
    },
    { 
      id: 'course-builder', 
      name: 'Course Builder', 
      description: 'Create structured courses with modules and lessons',
      category: 'Courses',
      essential: true,
      icon: '🎓'
    },
    { 
      id: 'quiz-system', 
      name: 'Quiz & Assessments', 
      description: 'Create quizzes and track student progress',
      category: 'Courses',
      essential: false,
      icon: '📝'
    },
    { 
      id: 'community-forums', 
      name: 'Community Forums', 
      description: 'Discussion forums for course communities',
      category: 'Courses',
      essential: false,
      icon: '💬'
    },
    { 
      id: 'certificates', 
      name: 'Certificates', 
      description: 'Generate completion certificates for students',
      category: 'Courses',
      essential: false,
      icon: '🏆'
    },
    { 
      id: 'live-streaming', 
      name: 'Live Streaming', 
      description: 'Host live webinars and Q&A sessions',
      category: 'Courses',
      essential: false,
      icon: '📡'
    },

    // CRM & Email Marketing (6 features)
    { 
      id: 'contact-management', 
      name: 'Contact Management', 
      description: 'Comprehensive customer relationship management',
      category: 'CRM',
      essential: true,
      icon: '👥'
    },
    { 
      id: 'email-campaigns', 
      name: 'Email Campaigns', 
      description: 'Create and send email marketing campaigns',
      category: 'CRM',
      essential: true,
      icon: '📧'
    },
    { 
      id: 'automation-workflows', 
      name: 'Marketing Automation', 
      description: 'Automated email sequences and triggers',
      category: 'CRM',
      essential: false,
      icon: '🔄'
    },
    { 
      id: 'lead-scoring', 
      name: 'Lead Scoring', 
      description: 'Automated lead qualification and scoring',
      category: 'CRM',
      essential: false,
      icon: '🎯'
    },
    { 
      id: 'pipeline-management', 
      name: 'Sales Pipeline', 
      description: 'Visual sales pipeline management',
      category: 'CRM',
      essential: false,
      icon: '📊'
    },
    { 
      id: 'email-templates', 
      name: 'Email Templates', 
      description: 'Professional email templates library',
      category: 'CRM',
      essential: false,
      icon: '📄'
    },

    // Analytics & Business Intelligence (6 features)
    { 
      id: 'performance-analytics', 
      name: 'Performance Analytics', 
      description: 'Comprehensive business performance tracking',
      category: 'Analytics',
      essential: true,
      icon: '📊'
    },
    { 
      id: 'custom-reports', 
      name: 'Custom Reports', 
      description: 'Build custom reports and dashboards',
      category: 'Analytics',
      essential: false,
      icon: '📈'
    },
    { 
      id: 'roi-tracking', 
      name: 'ROI Tracking', 
      description: 'Return on investment tracking and analysis',
      category: 'Analytics',
      essential: false,
      icon: '💰'
    },
    { 
      id: 'gamification', 
      name: 'Gamification', 
      description: 'Gamified analytics with XP, levels, and badges',
      category: 'Analytics',
      essential: false,
      icon: '🎮'
    },
    { 
      id: 'data-export', 
      name: 'Data Export', 
      description: 'Export data in multiple formats (CSV, PDF, Excel)',
      category: 'Analytics',
      essential: false,
      icon: '📤'
    },
    { 
      id: 'predictive-analytics', 
      name: 'AI Insights', 
      description: 'AI-powered predictive analytics and insights',
      category: 'Analytics',
      essential: false,
      icon: '🤖'
    }
  ];

  // Subscription plans
  const subscriptionPlans = [
    {
      id: 'free',
      name: 'Starter',
      subtitle: 'Perfect for trying out',
      monthlyPrice: 0,
      yearlyPrice: 0,
      featuresIncluded: 10,
      maxWorkspaces: 1,
      maxTeamMembers: 2,
      support: 'Community',
      features: [
        'Up to 10 platform features',
        '1 workspace',
        '2 team members',
        'Community support',
        'Basic analytics'
      ],
      limitations: [
        'Limited to essential features only',
        'No white-label branding',
        'Basic support only'
      ],
      popular: false,
      color: 'gray'
    },
    {
      id: 'pro',
      name: 'Professional',
      subtitle: 'Best for growing businesses',
      monthlyPrice: 1,
      yearlyPrice: 10,
      priceType: 'per feature',
      featuresIncluded: 'unlimited',
      maxWorkspaces: 5,
      maxTeamMembers: 10,
      support: 'Priority',
      features: [
        'All platform features available',
        'Pay only for what you use',
        '5 workspaces',
        '10 team members per workspace',
        'Priority email support',
        'Advanced analytics',
        'Custom integrations',
        'API access'
      ],
      limitations: [],
      popular: true,
      color: 'blue'
    },
    {
      id: 'enterprise',
      name: 'Enterprise',
      subtitle: 'For large organizations',
      monthlyPrice: 1.5,
      yearlyPrice: 15,
      priceType: 'per feature',
      featuresIncluded: 'unlimited',
      maxWorkspaces: 'unlimited',
      maxTeamMembers: 'unlimited',
      support: 'Dedicated',
      features: [
        'All Professional features',
        'White-label branding',
        'Unlimited workspaces',
        'Unlimited team members',
        'Dedicated account manager',
        'Custom integrations',
        'SLA guarantees',
        'Advanced security',
        'Custom contracts'
      ],
      limitations: [],
      popular: false,
      color: 'purple',
      extras: true
    }
  ];

  useEffect(() => {
    loadSubscriptionData();
  }, []);

  const loadSubscriptionData = async () => {
    setLoading(true);
    try {
      // Load current subscription
      const subResponse = await fetch(`${process.env.REACT_APP_BACKEND_URL}/api/subscriptions/current`, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        }
      });
      
      if (subResponse.ok) {
        const subData = await subResponse.json();
        setCurrentSubscription(subData);
        setSelectedFeatures(subData.features || []);
      }

      // Load payment methods
      const paymentResponse = await fetch(`${process.env.REACT_APP_BACKEND_URL}/api/payments/methods`, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        }
      });
      
      if (paymentResponse.ok) {
        const paymentData = await paymentResponse.json();
        setPaymentMethods(paymentData);
      }
    } catch (err) {
      error('Failed to load subscription data');
    }
    setLoading(false);
  };

  const handleFeatureToggle = (featureId) => {
    setSelectedFeatures(prev => 
      prev.includes(featureId)
        ? prev.filter(id => id !== featureId)
        : [...prev, featureId]
    );
  };

  const calculatePrice = (plan, features) => {
    if (plan.id === 'free') return 0;
    
    const price = billingCycle === 'yearly' ? plan.yearlyPrice : plan.monthlyPrice;
    return price * features.length;
  };

  const handleSubscriptionUpdate = async (planId) => {
    try {
      const response = await fetch(`${process.env.REACT_APP_BACKEND_URL}/api/subscriptions/update`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        },
        body: JSON.stringify({
          planId,
          features: selectedFeatures,
          billingCycle
        })
      });

      if (response.ok) {
        success('Subscription updated successfully!');
        loadSubscriptionData();
      } else {
        throw new Error('Failed to update subscription');
      }
    } catch (err) {
      error('Failed to update subscription');
    }
  };

  const groupFeaturesByCategory = (features) => {
    const grouped = {};
    features.forEach(feature => {
      if (!grouped[feature.category]) {
        grouped[feature.category] = [];
      }
      grouped[feature.category].push(feature);
    });
    return grouped;
  };

  const getSelectedFeaturesByCategory = () => {
    const selectedFeatureObjects = allFeatures.filter(f => selectedFeatures.includes(f.id));
    return groupFeaturesByCategory(selectedFeatureObjects);
  };

  if (loading) {
    return (
      <div className="p-6 max-w-7xl mx-auto">
        <div className="text-center">
          <div className="w-12 h-12 border-4 border-blue-500 border-t-transparent rounded-full animate-spin mx-auto mb-4" />
          <h2 className="text-xl font-semibold text-primary">Loading subscription details...</h2>
        </div>
      </div>
    );
  }

  return (
    <div className="space-y-8">
      {/* Header */}
      <div className="text-center">
        <h1 className="text-3xl font-bold text-primary mb-4">Subscription Management</h1>
        <p className="text-secondary max-w-2xl mx-auto">
          Manage your subscription, features, and billing. Pay only for the features you use.
        </p>
      </div>

      {/* Current Plan Status */}
      {currentSubscription && (
        <div className="bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl text-white p-6">
          <div className="flex items-center justify-between">
            <div>
              <h2 className="text-xl font-semibold mb-2">
                Current Plan: {currentSubscription.planName}
              </h2>
              <p className="text-blue-100">
                {currentSubscription.featuresCount} features active • 
                Next billing: {new Date(currentSubscription.nextBilling).toLocaleDateString()}
              </p>
            </div>
            <div className="text-right">
              <div className="text-2xl font-bold">
                ${currentSubscription.monthlyAmount}/mo
              </div>
              <div className="text-blue-100 text-sm">
                {currentSubscription.billingCycle}
              </div>
            </div>
          </div>
        </div>
      )}

      {/* Billing Cycle Toggle */}
      <div className="flex items-center justify-center">
        <div className="bg-surface-elevated rounded-xl p-1 flex">
          <button
            onClick={() => setBillingCycle('monthly')}
            className={`px-4 py-2 rounded-lg text-sm font-medium transition-all ${
              billingCycle === 'monthly'
                ? 'bg-blue-500 text-white'
                : 'text-secondary hover:text-primary'
            }`}
          >
            Monthly
          </button>
          <button
            onClick={() => setBillingCycle('yearly')}
            className={`px-4 py-2 rounded-lg text-sm font-medium transition-all ${
              billingCycle === 'yearly'
                ? 'bg-blue-500 text-white'
                : 'text-secondary hover:text-primary'
            }`}
          >
            Yearly
            <span className="ml-2 text-xs bg-green-500 text-white px-2 py-1 rounded-full">
              Save 17%
            </span>
          </button>
        </div>
      </div>

      {/* Subscription Plans */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        {subscriptionPlans.map((plan) => (
          <motion.div
            key={plan.id}
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            className={`relative bg-surface-elevated rounded-xl border-2 p-6 ${
              plan.popular ? 'border-blue-500 shadow-blue-500/20 shadow-lg' : 'border-default'
            }`}
          >
            {plan.popular && (
              <div className="absolute -top-3 left-1/2 transform -translate-x-1/2">
                <span className="bg-blue-500 text-white px-4 py-1 rounded-full text-sm font-medium flex items-center">
                  <StarIconSolid className="h-4 w-4 mr-1" />
                  Most Popular
                </span>
              </div>
            )}

            {plan.id === 'enterprise' && (
              <div className="absolute -top-3 right-4">
                <TrophyIconSolid className="h-6 w-6 text-yellow-500" />
              </div>
            )}

            <div className="text-center mb-6">
              <h3 className="text-xl font-bold text-primary mb-1">{plan.name}</h3>
              <p className="text-secondary text-sm">{plan.subtitle}</p>
              
              <div className="mt-4">
                {plan.monthlyPrice === 0 ? (
                  <div className="text-3xl font-bold text-primary">Free</div>
                ) : (
                  <>
                    <div className="text-3xl font-bold text-primary">
                      ${billingCycle === 'yearly' ? plan.yearlyPrice : plan.monthlyPrice}
                    </div>
                    <div className="text-sm text-secondary">
                      {plan.priceType} {billingCycle === 'yearly' ? 'per year' : 'per month'}
                    </div>
                  </>
                )}
                
                {plan.id !== 'free' && (
                  <div className="mt-2 text-sm text-secondary">
                    Current selection: ${calculatePrice(plan, selectedFeatures)}/{billingCycle === 'yearly' ? 'year' : 'month'}
                  </div>
                )}
              </div>
            </div>

            <div className="space-y-3 mb-6">
              {plan.features.map((feature, index) => (
                <div key={index} className="flex items-start space-x-2">
                  <CheckIcon className="h-5 w-5 text-green-500 flex-shrink-0 mt-0.5" />
                  <span className="text-sm text-secondary">{feature}</span>
                </div>
              ))}
              
              {plan.limitations.length > 0 && (
                <div className="pt-3 border-t border-default">
                  {plan.limitations.map((limitation, index) => (
                    <div key={index} className="flex items-start space-x-2">
                      <XMarkIcon className="h-5 w-5 text-red-500 flex-shrink-0 mt-0.5" />
                      <span className="text-sm text-red-600 dark:text-red-400">{limitation}</span>
                    </div>
                  ))}
                </div>
              )}
            </div>

            <button
              onClick={() => handleSubscriptionUpdate(plan.id)}
              disabled={currentSubscription?.planId === plan.id}
              className={`w-full py-3 px-4 rounded-lg font-medium transition-all ${
                currentSubscription?.planId === plan.id
                  ? 'bg-surface text-secondary cursor-not-allowed'
                  : plan.popular
                    ? 'bg-blue-500 hover:bg-blue-600 text-white'
                    : 'bg-surface border border-default hover:border-blue-500 text-primary hover:bg-blue-50 dark:hover:bg-blue-900/20'
              }`}
            >
              {currentSubscription?.planId === plan.id ? 'Current Plan' : `Switch to ${plan.name}`}
            </button>
          </motion.div>
        ))}
      </div>

      {/* Feature Selection */}
      <div className="bg-surface-elevated rounded-xl shadow-default p-6">
        <div className="flex items-center justify-between mb-6">
          <div>
            <h2 className="text-xl font-semibold text-primary">Select Your Features</h2>
            <p className="text-secondary">Choose only the features you need. You can change this anytime.</p>
          </div>
          <div className="text-right">
            <div className="text-lg font-semibold text-primary">
              {selectedFeatures.length} features selected
            </div>
            <div className="text-sm text-secondary">
              {selectedFeatures.length > 10 && currentSubscription?.planId === 'free' && (
                <span className="text-orange-500">Exceeds free plan limit</span>
              )}
            </div>
          </div>
        </div>

        <div className="space-y-6">
          {Object.entries(groupFeaturesByCategory(allFeatures)).map(([category, features]) => (
            <div key={category} className="border border-default rounded-lg p-4">
              <div className="flex items-center justify-between mb-4">
                <h3 className="text-lg font-medium text-primary">{category}</h3>
                <div className="text-sm text-secondary">
                  {features.filter(f => selectedFeatures.includes(f.id)).length} of {features.length} selected
                </div>
              </div>
              
              <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
                {features.map((feature) => (
                  <motion.div
                    key={feature.id}
                    whileHover={{ scale: 1.02 }}
                    className={`p-4 rounded-lg border cursor-pointer transition-all ${
                      selectedFeatures.includes(feature.id)
                        ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                        : 'border-default hover:border-blue-300'
                    }`}
                    onClick={() => handleFeatureToggle(feature.id)}
                  >
                    <div className="flex items-start space-x-3">
                      <div className="text-2xl">{feature.icon}</div>
                      <div className="flex-1 min-w-0">
                        <div className="flex items-center justify-between">
                          <h4 className="font-medium text-primary">{feature.name}</h4>
                          {selectedFeatures.includes(feature.id) && (
                            <CheckIcon className="h-5 w-5 text-blue-500" />
                          )}
                        </div>
                        <p className="text-sm text-secondary mt-1">{feature.description}</p>
                        {feature.essential && (
                          <span className="inline-block mt-2 text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full">
                            Essential
                          </span>
                        )}
                      </div>
                    </div>
                  </motion.div>
                ))}
              </div>
            </div>
          ))}
        </div>
      </div>

      {/* Selected Features Summary */}
      {selectedFeatures.length > 0 && (
        <div className="bg-surface-elevated rounded-xl shadow-default p-6">
          <h3 className="text-lg font-semibold text-primary mb-4">Selected Features Summary</h3>
          
          <div className="space-y-4">
            {Object.entries(getSelectedFeaturesByCategory()).map(([category, features]) => (
              <div key={category} className="flex items-center justify-between p-3 bg-surface rounded-lg">
                <div>
                  <span className="font-medium text-primary">{category}</span>
                  <span className="text-sm text-secondary ml-2">({features.length} features)</span>
                </div>
                <div className="flex flex-wrap gap-1">
                  {features.slice(0, 3).map((feature) => (
                    <span key={feature.id} className="text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded">
                      {feature.name}
                    </span>
                  ))}
                  {features.length > 3 && (
                    <span className="text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded">
                      +{features.length - 3} more
                    </span>
                  )}
                </div>
              </div>
            ))}
          </div>
        </div>
      )}

      {/* Billing Information */}
      <div className="bg-surface-elevated rounded-xl shadow-default p-6">
        <div className="flex items-center justify-between mb-6">
          <h3 className="text-lg font-semibold text-primary">Payment Methods</h3>
          <button
            onClick={() => setShowAddPayment(!showAddPayment)}
            className="btn btn-primary btn-sm flex items-center space-x-2"
          >
            <PlusIcon className="h-4 w-4" />
            <span>Add Payment Method</span>
          </button>
        </div>

        {paymentMethods.length === 0 ? (
          <div className="text-center py-8 text-secondary">
            <CreditCardIcon className="h-12 w-12 mx-auto mb-4 opacity-50" />
            <p>No payment methods added yet</p>
          </div>
        ) : (
          <div className="space-y-3">
            {paymentMethods.map((method) => (
              <div key={method.id} className="flex items-center justify-between p-4 border border-default rounded-lg">
                <div className="flex items-center space-x-3">
                  <CreditCardIcon className="h-6 w-6 text-secondary" />
                  <div>
                    <p className="font-medium text-primary">•••• •••• •••• {method.last4}</p>
                    <p className="text-sm text-secondary">{method.brand} • Expires {method.expMonth}/{method.expYear}</p>
                  </div>
                  {method.isDefault && (
                    <span className="text-xs px-2 py-1 bg-green-100 text-green-800 rounded-full">
                      Default
                    </span>
                  )}
                </div>
                <button className="text-red-500 hover:text-red-700">Remove</button>
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  );
};

export default SubscriptionManagementPage;