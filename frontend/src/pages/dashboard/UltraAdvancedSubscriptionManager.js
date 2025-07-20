import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { useAuth } from '../../contexts/AuthContext';
import { useNotification } from '../../contexts/NotificationContext';
import {
  CreditCardIcon,
  StarIcon,
  BoltIcon,
  TrophyIcon,
  CheckIcon,
  XMarkIcon,
  ArrowUpIcon,
  ArrowDownIcon,
  CalendarIcon,
  CurrencyDollarIcon,
  ShieldCheckIcon,
  LightningBoltIcon,
  GiftIcon,
  DocumentTextIcon,
  ClockIcon,
  UserGroupIcon,
  ChartBarIcon,
  SparklesIcon,
  RocketLaunchIcon,
  BuildingOfficeIcon,
  PhoneIcon,
  ChatBubbleLeftRightIcon,
  TagIcon,
  PlusIcon
} from '@heroicons/react/24/outline';
import {
  StarIcon as StarIconSolid,
  CrownIcon as CrownIconSolid,
  BoltIcon as BoltIconSolid,
  ShieldCheckIcon as ShieldCheckIconSolid
} from '@heroicons/react/24/solid';

const UltraAdvancedSubscriptionManager = () => {
  const { user } = useAuth();
  const { success, error, info } = useNotification();
  
  const [activeTab, setActiveTab] = useState('plans');
  const [currentPlan, setCurrentPlan] = useState(null);
  const [billingHistory, setBillingHistory] = useState([]);
  const [paymentMethods, setPaymentMethods] = useState([]);
  const [usage, setUsage] = useState({});
  const [showAddPaymentModal, setShowAddPaymentModal] = useState(false);
  const [loading, setLoading] = useState(false);
  
  // Subscription Plans
  const subscriptionPlans = [
    {
      id: 'free',
      name: 'Starter',
      icon: StarIcon,
      price: 0,
      billing: 'forever',
      description: 'Perfect for getting started with basic features',
      maxFeatures: 10,
      features: [
        'Up to 10 features',
        'Basic social media posting',
        'Simple link in bio',
        'Basic analytics',
        'Community support',
        'Standard templates',
        '1 workspace',
        '2 team members'
      ],
      limits: {
        posts_per_month: 100,
        storage_gb: 1,
        api_calls: 1000,
        team_members: 2,
        workspaces: 1,
        custom_domains: 0
      },
      color: 'gray',
      gradient: 'from-gray-400 to-gray-600',
      popular: false
    },
    {
      id: 'pro',
      name: 'Professional',
      icon: BoltIcon,
      price: 1,
      billing: 'per feature/month',
      yearlyPrice: 10,
      yearlyBilling: 'per feature/year',
      description: 'Scale your business with advanced features and automation',
      maxFeatures: 40,
      features: [
        'Up to 40 features',
        'Advanced AI content generation',
        'Multi-platform scheduling',
        'Advanced analytics & reports',
        'Priority support',
        'Premium templates',
        'Up to 5 workspaces',
        'Unlimited team members',
        'Custom domains (3)',
        'White-label options',
        'API access',
        'Advanced integrations'
      ],
      limits: {
        posts_per_month: 1000,
        storage_gb: 50,
        api_calls: 50000,
        team_members: -1,
        workspaces: 5,
        custom_domains: 3
      },
      color: 'blue',
      gradient: 'from-blue-500 to-blue-700',
      popular: true,
      savings: '16%'
    },
    {
      id: 'enterprise',
      name: 'Enterprise',
      icon: CrownIcon,
      price: 1.5,
      billing: 'per feature/month',
      yearlyPrice: 15,
      yearlyBilling: 'per feature/year',
      description: 'Complete business solution with enterprise-grade features',
      maxFeatures: -1,
      features: [
        'Unlimited features',
        'Full white-label solution',
        'Custom branding everywhere',
        'Advanced AI & automation',
        'Dedicated account manager',
        'Custom integrations',
        'Unlimited workspaces',
        'Advanced user roles',
        'Custom domains (unlimited)',
        'Priority phone support',
        'SLA guarantees',
        'Custom training',
        'Advanced security',
        'Custom reports',
        'API priority access'
      ],
      limits: {
        posts_per_month: -1,
        storage_gb: 500,
        api_calls: -1,
        team_members: -1,
        workspaces: -1,
        custom_domains: -1
      },
      color: 'yellow',
      gradient: 'from-yellow-500 to-yellow-700',
      popular: false,
      savings: '16%'
    }
  ];
  
  // Available features for selection
  const availableFeatures = [
    { id: 'social_posting', name: 'Social Media Posting', category: 'Social Media', essential: true },
    { id: 'content_calendar', name: 'Content Calendar', category: 'Social Media', essential: true },
    { id: 'instagram_database', name: 'Instagram Database', category: 'Social Media', essential: false },
    { id: 'hashtag_research', name: 'Hashtag Research', category: 'Social Media', essential: false },
    { id: 'competitor_analysis', name: 'Competitor Analysis', category: 'Social Media', essential: false },
    { id: 'drag_drop_builder', name: 'Drag & Drop Builder', category: 'Link in Bio', essential: true },
    { id: 'custom_domains', name: 'Custom Domains', category: 'Link in Bio', essential: false },
    { id: 'qr_codes', name: 'QR Code Generator', category: 'Link in Bio', essential: false },
    { id: 'link_analytics', name: 'Link Analytics', category: 'Link in Bio', essential: false },
    { id: 'course_builder', name: 'Course Builder', category: 'Courses', essential: true },
    { id: 'video_hosting', name: 'Video Hosting', category: 'Courses', essential: false },
    { id: 'student_management', name: 'Student Management', category: 'Courses', essential: false },
    { id: 'certificates', name: 'Certificates', category: 'Courses', essential: false },
    { id: 'product_management', name: 'Product Management', category: 'E-commerce', essential: true },
    { id: 'inventory_tracking', name: 'Inventory Tracking', category: 'E-commerce', essential: false },
    { id: 'payment_processing', name: 'Payment Processing', category: 'E-commerce', essential: true },
    { id: 'order_management', name: 'Order Management', category: 'E-commerce', essential: false },
    { id: 'contact_management', name: 'Contact Management', category: 'CRM', essential: true },
    { id: 'email_campaigns', name: 'Email Campaigns', category: 'CRM', essential: false },
    { id: 'lead_scoring', name: 'Lead Scoring', category: 'CRM', essential: false },
    { id: 'automation_workflows', name: 'Automation Workflows', category: 'CRM', essential: false },
    { id: 'advanced_analytics', name: 'Advanced Analytics', category: 'Analytics', essential: false },
    { id: 'custom_reports', name: 'Custom Reports', category: 'Analytics', essential: false },
    { id: 'data_export', name: 'Data Export', category: 'Analytics', essential: false },
    { id: 'real_time_dashboard', name: 'Real-time Dashboard', category: 'Analytics', essential: false },
    { id: 'ai_content_generation', name: 'AI Content Generation', category: 'AI Features', essential: false },
    { id: 'ai_analytics', name: 'AI Analytics', category: 'AI Features', essential: false },
    { id: 'chatbot', name: 'AI Chatbot', category: 'AI Features', essential: false },
    { id: 'white_label', name: 'White Label', category: 'Enterprise', essential: false },
    { id: 'api_access', name: 'API Access', category: 'Enterprise', essential: false },
    { id: 'priority_support', name: 'Priority Support', category: 'Enterprise', essential: false },
    { id: 'custom_integrations', name: 'Custom Integrations', category: 'Enterprise', essential: false }
  ];
  
  const [selectedFeatures, setSelectedFeatures] = useState([]);
  const [billingCycle, setBillingCycle] = useState('monthly');
  
  useEffect(() => {
    fetchCurrentPlan();
    fetchBillingHistory();
    fetchPaymentMethods();
    fetchUsageStats();
  }, []);
  
  const fetchCurrentPlan = async () => {
    try {
      const response = await fetch(`${process.env.REACT_APP_BACKEND_URL}/api/subscription/status`, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        }
      });
      
      if (response.ok) {
        const data = await response.json();
        setCurrentPlan(data.data);
      }
    } catch (err) {
      console.error('Failed to fetch current plan:', err);
    }
  };
  
  const fetchBillingHistory = async () => {
    // Mock billing history for now
    setBillingHistory([
      {
        id: '1',
        date: '2025-01-19',
        description: 'Professional Plan - 15 features',
        amount: '$15.00',
        status: 'paid',
        invoice_url: '#'
      },
      {
        id: '2',
        date: '2024-12-19',
        description: 'Professional Plan - 12 features',
        amount: '$12.00',
        status: 'paid',
        invoice_url: '#'
      },
      {
        id: '3',
        date: '2024-11-19',
        description: 'Professional Plan - 10 features',
        amount: '$10.00',
        status: 'paid',
        invoice_url: '#'
      }
    ]);
  };
  
  const fetchPaymentMethods = async () => {
    // Mock payment methods for now
    setPaymentMethods([
      {
        id: '1',
        type: 'card',
        brand: 'visa',
        last4: '4242',
        exp_month: 12,
        exp_year: 2027,
        is_default: true
      }
    ]);
  };
  
  const fetchUsageStats = async () => {
    // Mock usage stats for now
    setUsage({
      current_features: 15,
      posts_this_month: 245,
      storage_used_gb: 12.5,
      api_calls_this_month: 15420,
      team_members: 5,
      workspaces: 2
    });
  };
  
  const calculatePrice = (plan, features, cycle) => {
    if (plan.id === 'free') return 0;
    
    const basePrice = cycle === 'yearly' ? plan.yearlyPrice : plan.price;
    return basePrice * features;
  };
  
  const handlePlanUpgrade = async (planId, features) => {
    setLoading(true);
    try {
      const response = await fetch(`${process.env.REACT_APP_BACKEND_URL}/api/subscription/upgrade`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        },
        body: JSON.stringify({
          plan: planId,
          features: features,
          billing_cycle: billingCycle
        })
      });
      
      if (response.ok) {
        success('Plan upgraded successfully!');
        fetchCurrentPlan();
      } else {
        const errorData = await response.json();
        error(errorData.detail || 'Failed to upgrade plan');
      }
    } catch (err) {
      error('Failed to upgrade plan');
    } finally {
      setLoading(false);
    }
  };
  
  const renderPlanCard = (plan) => {
    const isCurrentPlan = currentPlan?.plan === plan.id;
    const selectedFeatureCount = selectedFeatures.length || (plan.id === 'free' ? 10 : plan.maxFeatures === -1 ? 32 : plan.maxFeatures);
    const totalPrice = calculatePrice(plan, selectedFeatureCount, billingCycle);
    
    return (
      <motion.div
        key={plan.id}
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        className={`relative p-8 rounded-2xl border-2 ${
          plan.popular ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 
          isCurrentPlan ? 'border-green-500 bg-green-50 dark:bg-green-900/20' :
          'border-default bg-surface'
        }`}
      >
        {plan.popular && (
          <div className="absolute -top-4 left-1/2 transform -translate-x-1/2">
            <span className="bg-gradient-to-r from-blue-500 to-blue-700 text-white px-4 py-2 rounded-full text-sm font-medium">
              Most Popular
            </span>
          </div>
        )}
        
        {isCurrentPlan && (
          <div className="absolute -top-4 right-4">
            <span className="bg-green-500 text-white px-3 py-1 rounded-full text-sm font-medium">
              Current Plan
            </span>
          </div>
        )}
        
        <div className="text-center mb-8">
          <div className={`inline-flex p-4 rounded-xl bg-gradient-to-r ${plan.gradient} mb-4`}>
            <plan.icon className="h-8 w-8 text-white" />
          </div>
          <h3 className="text-2xl font-bold text-primary mb-2">{plan.name}</h3>
          <p className="text-secondary mb-6">{plan.description}</p>
          
          <div className="space-y-2">
            {plan.price === 0 ? (
              <div className="text-4xl font-bold text-primary">Free</div>
            ) : (
              <>
                <div className="text-4xl font-bold text-primary">
                  ${totalPrice}
                  <span className="text-lg text-secondary">
                    /{billingCycle === 'yearly' ? 'year' : 'month'}
                  </span>
                </div>
                <div className="text-sm text-secondary">
                  ${billingCycle === 'yearly' ? plan.yearlyPrice : plan.price} per feature
                </div>
                {billingCycle === 'yearly' && plan.savings && (
                  <div className="inline-flex items-center text-green-600 text-sm">
                    <GiftIcon className="h-4 w-4 mr-1" />
                    Save {plan.savings}
                  </div>
                )}
              </>
            )}
          </div>
        </div>
        
        <div className="space-y-4 mb-8">
          {plan.features.map((feature, index) => (
            <div key={index} className="flex items-start">
              <CheckIcon className="h-5 w-5 text-green-500 mr-3 flex-shrink-0 mt-0.5" />
              <span className="text-primary">{feature}</span>
            </div>
          ))}
        </div>
        
        <div className="space-y-4">
          {!isCurrentPlan && (
            <button
              onClick={() => handlePlanUpgrade(plan.id, selectedFeatureCount)}
              disabled={loading}
              className={`w-full py-3 px-4 rounded-lg font-medium transition-colors ${
                plan.popular
                  ? 'bg-blue-600 text-white hover:bg-blue-700'
                  : 'bg-primary text-white hover:bg-primary/90'
              }`}
            >
              {loading ? 'Processing...' : `Upgrade to ${plan.name}`}
            </button>
          )}
          
          {isCurrentPlan && (
            <div className="text-center text-green-600 font-medium">
              ✓ Your Current Plan
            </div>
          )}
        </div>
      </motion.div>
    );
  };
  
  const renderUsageStats = () => (
    <div className="bg-surface-elevated rounded-xl shadow-default p-6">
      <h3 className="text-xl font-semibold text-primary mb-6">Usage This Month</h3>
      
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div className="text-center p-4 rounded-lg bg-surface border border-default">
          <TagIcon className="h-8 w-8 mx-auto mb-2 text-blue-600" />
          <div className="text-2xl font-bold text-primary">{usage.current_features || 0}</div>
          <div className="text-sm text-secondary">Active Features</div>
          <div className="text-xs text-secondary mt-1">
            {currentPlan?.plan === 'free' ? 'Max 10' : currentPlan?.plan === 'pro' ? 'Max 40' : 'Unlimited'}
          </div>
        </div>
        
        <div className="text-center p-4 rounded-lg bg-surface border border-default">
          <DocumentTextIcon className="h-8 w-8 mx-auto mb-2 text-green-600" />
          <div className="text-2xl font-bold text-primary">{usage.posts_this_month || 0}</div>
          <div className="text-sm text-secondary">Posts Published</div>
          <div className="text-xs text-secondary mt-1">
            {currentPlan?.plan === 'free' ? 'Max 100' : currentPlan?.plan === 'pro' ? 'Max 1,000' : 'Unlimited'}
          </div>
        </div>
        
        <div className="text-center p-4 rounded-lg bg-surface border border-default">
          <ChartBarIcon className="h-8 w-8 mx-auto mb-2 text-purple-600" />
          <div className="text-2xl font-bold text-primary">{(usage.storage_used_gb || 0).toFixed(1)}GB</div>
          <div className="text-sm text-secondary">Storage Used</div>
          <div className="text-xs text-secondary mt-1">
            {currentPlan?.plan === 'free' ? 'Max 1GB' : currentPlan?.plan === 'pro' ? 'Max 50GB' : 'Max 500GB'}
          </div>
        </div>
        
        <div className="text-center p-4 rounded-lg bg-surface border border-default">
          <SparklesIcon className="h-8 w-8 mx-auto mb-2 text-yellow-600" />
          <div className="text-2xl font-bold text-primary">{usage.api_calls_this_month || 0}</div>
          <div className="text-sm text-secondary">API Calls</div>
          <div className="text-xs text-secondary mt-1">
            {currentPlan?.plan === 'free' ? 'Max 1K' : currentPlan?.plan === 'pro' ? 'Max 50K' : 'Unlimited'}
          </div>
        </div>
        
        <div className="text-center p-4 rounded-lg bg-surface border border-default">
          <UserGroupIcon className="h-8 w-8 mx-auto mb-2 text-indigo-600" />
          <div className="text-2xl font-bold text-primary">{usage.team_members || 0}</div>
          <div className="text-sm text-secondary">Team Members</div>
          <div className="text-xs text-secondary mt-1">
            {currentPlan?.plan === 'free' ? 'Max 2' : 'Unlimited'}
          </div>
        </div>
        
        <div className="text-center p-4 rounded-lg bg-surface border border-default">
          <BuildingOfficeIcon className="h-8 w-8 mx-auto mb-2 text-pink-600" />
          <div className="text-2xl font-bold text-primary">{usage.workspaces || 0}</div>
          <div className="text-sm text-secondary">Workspaces</div>
          <div className="text-xs text-secondary mt-1">
            {currentPlan?.plan === 'free' ? 'Max 1' : currentPlan?.plan === 'pro' ? 'Max 5' : 'Unlimited'}
          </div>
        </div>
      </div>
    </div>
  );
  
  const renderBillingHistory = () => (
    <div className="bg-surface-elevated rounded-xl shadow-default p-6">
      <div className="flex items-center justify-between mb-6">
        <h3 className="text-xl font-semibold text-primary">Billing History</h3>
        <button className="btn btn-secondary btn-sm">
          <DocumentTextIcon className="h-4 w-4 mr-2" />
          Download All
        </button>
      </div>
      
      <div className="overflow-x-auto">
        <table className="w-full">
          <thead>
            <tr className="border-b border-default">
              <th className="text-left py-3 text-sm font-medium text-secondary">Date</th>
              <th className="text-left py-3 text-sm font-medium text-secondary">Description</th>
              <th className="text-left py-3 text-sm font-medium text-secondary">Amount</th>
              <th className="text-left py-3 text-sm font-medium text-secondary">Status</th>
              <th className="text-left py-3 text-sm font-medium text-secondary">Invoice</th>
            </tr>
          </thead>
          <tbody>
            {billingHistory.map((bill) => (
              <tr key={bill.id} className="border-b border-default hover:bg-surface-hover">
                <td className="py-3 text-sm text-primary">{new Date(bill.date).toLocaleDateString()}</td>
                <td className="py-3 text-sm text-primary">{bill.description}</td>
                <td className="py-3 text-sm font-semibold text-primary">{bill.amount}</td>
                <td className="py-3">
                  <span className={`inline-flex px-2 py-1 rounded-full text-xs font-medium ${
                    bill.status === 'paid' ? 'bg-green-100 text-green-800' :
                    bill.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                    'bg-red-100 text-red-800'
                  }`}>
                    {bill.status}
                  </span>
                </td>
                <td className="py-3">
                  <button className="text-blue-600 hover:text-blue-800 text-sm">
                    Download
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
  
  const renderPaymentMethods = () => (
    <div className="bg-surface-elevated rounded-xl shadow-default p-6">
      <div className="flex items-center justify-between mb-6">
        <h3 className="text-xl font-semibold text-primary">Payment Methods</h3>
        <button 
          onClick={() => setShowAddPaymentModal(true)}
          className="btn btn-primary btn-sm"
        >
          <PlusIcon className="h-4 w-4 mr-2" />
          Add Payment Method
        </button>
      </div>
      
      <div className="space-y-4">
        {paymentMethods.map((method) => (
          <div key={method.id} className="flex items-center justify-between p-4 border border-default rounded-lg">
            <div className="flex items-center">
              <div className="w-12 h-8 bg-gradient-to-r from-blue-600 to-purple-600 rounded mr-4 flex items-center justify-center">
                <CreditCardIcon className="h-5 w-5 text-white" />
              </div>
              <div>
                <div className="font-medium text-primary">
                  •••• •••• •••• {method.last4}
                </div>
                <div className="text-sm text-secondary">
                  Expires {method.exp_month}/{method.exp_year}
                </div>
              </div>
            </div>
            <div className="flex items-center space-x-3">
              {method.is_default && (
                <span className="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">
                  Default
                </span>
              )}
              <button className="text-red-600 hover:text-red-800 text-sm">
                Remove
              </button>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
  
  return (
    <div className="space-y-8">
      {/* Header */}
      <div className="bg-gradient-to-r from-green-600 via-blue-600 to-purple-600 rounded-xl shadow-default p-6 text-white">
        <div className="flex items-center justify-between">
          <div>
            <div className="flex items-center mb-2">
              <CreditCardIcon className="h-8 w-8 mr-3" />
              <h1 className="text-3xl font-bold">Subscription Management</h1>
            </div>
            <p className="text-white/80">Manage your plan, features, and billing settings</p>
          </div>
          <div className="bg-white/20 rounded-xl p-4">
            <div className="text-center">
              <div className="text-2xl font-bold mb-1">
                {currentPlan?.plan ? currentPlan.plan.charAt(0).toUpperCase() + currentPlan.plan.slice(1) : 'Free'}
              </div>
              <div className="text-sm text-white/70">Current Plan</div>
            </div>
          </div>
        </div>
      </div>
      
      {/* Tabs */}
      <div className="border-b border-default">
        <nav className="flex space-x-8">
          {[
            { id: 'plans', name: 'Plans & Pricing', icon: StarIcon },
            { id: 'usage', name: 'Usage & Limits', icon: ChartBarIcon },
            { id: 'billing', name: 'Billing History', icon: DocumentTextIcon },
            { id: 'payment', name: 'Payment Methods', icon: CreditCardIcon }
          ].map((tab) => (
            <button
              key={tab.id}
              onClick={() => setActiveTab(tab.id)}
              className={`flex items-center py-4 px-1 border-b-2 font-medium text-sm ${
                activeTab === tab.id
                  ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                  : 'border-transparent text-secondary hover:text-primary'
              }`}
            >
              <tab.icon className="h-4 w-4 mr-2" />
              {tab.name}
            </button>
          ))}
        </nav>
      </div>
      
      {/* Tab Content */}
      {activeTab === 'plans' && (
        <div className="space-y-8">
          {/* Billing Toggle */}
          <div className="flex items-center justify-center space-x-4">
            <span className={`text-sm ${billingCycle === 'monthly' ? 'text-primary font-medium' : 'text-secondary'}`}>
              Monthly
            </span>
            <button
              onClick={() => setBillingCycle(billingCycle === 'monthly' ? 'yearly' : 'monthly')}
              className={`relative inline-flex h-6 w-11 items-center rounded-full transition-colors ${
                billingCycle === 'yearly' ? 'bg-blue-600' : 'bg-gray-200 dark:bg-gray-700'
              }`}
            >
              <span
                className={`inline-block h-4 w-4 transform rounded-full bg-white transition-transform ${
                  billingCycle === 'yearly' ? 'translate-x-6' : 'translate-x-1'
                }`}
              />
            </button>
            <span className={`text-sm ${billingCycle === 'yearly' ? 'text-primary font-medium' : 'text-secondary'}`}>
              Yearly
              <span className="text-green-600 ml-1">(Save 16%)</span>
            </span>
          </div>
          
          {/* Plans Grid */}
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {subscriptionPlans.map(renderPlanCard)}
          </div>
        </div>
      )}
      
      {activeTab === 'usage' && renderUsageStats()}
      {activeTab === 'billing' && renderBillingHistory()}
      {activeTab === 'payment' && renderPaymentMethods()}
    </div>
  );
};

export default UltraAdvancedSubscriptionManager;