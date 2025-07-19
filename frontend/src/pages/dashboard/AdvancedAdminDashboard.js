import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { 
  UsersIcon, 
  CreditCardIcon, 
  ChartBarIcon,
  CogIcon,
  ExclamationTriangleIcon,
  CheckCircleIcon,
  BanknotesIcon,
  BuildingOfficeIcon,
  GlobeAltIcon,
  ShieldCheckIcon,
  DocumentTextIcon,
  CalendarIcon
} from '@heroicons/react/24/outline';

const AdvancedAdminDashboard = () => {
  const [loading, setLoading] = useState(true);
  const [stats, setStats] = useState({
    totalUsers: 0,
    activeSubscriptions: 0,
    monthlyRevenue: 0,
    totalWorkspaces: 0,
    systemHealth: 'healthy'
  });
  const [recentActivity, setRecentActivity] = useState([]);
  const [subscriptionPlans, setSubscriptionPlans] = useState([]);

  useEffect(() => {
    fetchAdminData();
  }, []);

  const fetchAdminData = async () => {
    try {
      // Simulate API calls for admin data
      setStats({
        totalUsers: 1247,
        activeSubscriptions: 892,
        monthlyRevenue: 15420,
        totalWorkspaces: 567,
        systemHealth: 'healthy'
      });

      setRecentActivity([
        { id: 1, type: 'user_registered', description: 'New user registered: john@example.com', timestamp: '2 minutes ago' },
        { id: 2, type: 'subscription_upgraded', description: 'User upgraded to Pro plan: sarah@example.com', timestamp: '5 minutes ago' },
        { id: 3, type: 'workspace_created', description: 'New workspace created: Marketing Agency Pro', timestamp: '12 minutes ago' },
        { id: 4, type: 'payment_processed', description: 'Payment processed: $149.99 from alex@example.com', timestamp: '15 minutes ago' },
        { id: 5, type: 'feature_enabled', description: 'Instagram Management enabled for workspace #234', timestamp: '23 minutes ago' }
      ]);

      // Fetch subscription plans
      const response = await fetch(`${process.env.REACT_APP_BACKEND_URL}/api/subscription/plans`);
      if (response.ok) {
        const result = await response.json();
        if (result.success) {
          setSubscriptionPlans(result.plans);
        }
      }
    } catch (error) {
      console.error('Failed to fetch admin data:', error);
    } finally {
      setLoading(false);
    }
  };

  const StatCard = ({ title, value, icon: Icon, change, changeType = 'positive', color = 'blue' }) => (
    <motion.div
      whileHover={{ scale: 1.02 }}
      className="bg-card border border-default rounded-xl p-6"
    >
      <div className="flex items-center justify-between">
        <div>
          <p className="text-sm font-medium text-secondary">{title}</p>
          <p className="text-3xl font-bold text-primary mt-2">{value}</p>
          {change && (
            <p className={`text-sm mt-1 ${changeType === 'positive' ? 'text-green-600' : 'text-red-600'}`}>
              {changeType === 'positive' ? '↗' : '↘'} {change}
            </p>
          )}
        </div>
        <div className={`p-3 rounded-lg bg-${color}-50 dark:bg-${color}-900/20`}>
          <Icon className={`w-6 h-6 text-${color}-600`} />
        </div>
      </div>
    </motion.div>
  );

  const ActivityItem = ({ activity }) => {
    const getIcon = (type) => {
      switch (type) {
        case 'user_registered': return <UsersIcon className="w-4 h-4" />;
        case 'subscription_upgraded': return <CreditCardIcon className="w-4 h-4" />;
        case 'workspace_created': return <BuildingOfficeIcon className="w-4 h-4" />;
        case 'payment_processed': return <BanknotesIcon className="w-4 h-4" />;
        case 'feature_enabled': return <CheckCircleIcon className="w-4 h-4" />;
        default: return <DocumentTextIcon className="w-4 h-4" />;
      }
    };

    const getColor = (type) => {
      switch (type) {
        case 'user_registered': return 'text-green-600';
        case 'subscription_upgraded': return 'text-blue-600';
        case 'workspace_created': return 'text-purple-600';
        case 'payment_processed': return 'text-emerald-600';
        case 'feature_enabled': return 'text-orange-600';
        default: return 'text-gray-600';
      }
    };

    return (
      <div className="flex items-start space-x-3 p-3 hover:bg-hover rounded-lg transition-colors">
        <div className={`mt-0.5 ${getColor(activity.type)}`}>
          {getIcon(activity.type)}
        </div>
        <div className="flex-1 min-w-0">
          <p className="text-sm text-primary">{activity.description}</p>
          <p className="text-xs text-secondary mt-1">{activity.timestamp}</p>
        </div>
      </div>
    );
  };

  const PlanCard = ({ plan }) => (
    <motion.div
      whileHover={{ scale: 1.02 }}
      className={`bg-card border rounded-xl p-6 ${plan.is_popular ? 'border-accent-primary ring-2 ring-accent-primary/20' : 'border-default'}`}
    >
      {plan.is_popular && (
        <div className="bg-accent-primary text-white text-xs px-2 py-1 rounded-full inline-block mb-2">
          Most Popular
        </div>
      )}
      <h3 className="text-lg font-semibold text-primary">{plan.name}</h3>
      <p className="text-secondary text-sm mt-1">{plan.description}</p>
      <div className="mt-4">
        <div className="flex items-baseline space-x-2">
          {plan.price_monthly > 0 ? (
            <>
              <span className="text-2xl font-bold text-primary">${plan.price_monthly}</span>
              <span className="text-secondary text-sm">/feature/month</span>
            </>
          ) : (
            <span className="text-2xl font-bold text-primary">Free</span>
          )}
        </div>
        {plan.price_yearly > 0 && (
          <p className="text-sm text-secondary mt-1">
            or ${plan.price_yearly}/feature/year (save 17%)
          </p>
        )}
      </div>
      <div className="mt-4">
        <p className="text-sm font-medium text-primary">Features:</p>
        <ul className="text-xs text-secondary mt-2 space-y-1">
          {plan.features.slice(0, 3).map((feature, index) => (
            <li key={index}>• {feature}</li>
          ))}
          {plan.features.length > 3 && (
            <li className="text-accent-primary">• +{plan.features.length - 3} more</li>
          )}
        </ul>
      </div>
    </motion.div>
  );

  if (loading) {
    return (
      <div className="min-h-screen bg-app flex items-center justify-center">
        <div className="animate-pulse text-primary">Loading admin dashboard...</div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-app">
      <div className="max-w-7xl mx-auto px-4 py-8">
        {/* Header */}
        <div className="mb-8">
          <h1 className="text-3xl font-bold text-primary">Advanced Admin Dashboard</h1>
          <p className="text-secondary mt-2">Monitor and manage your Mewayz platform</p>
        </div>

        {/* Stats Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <StatCard
            title="Total Users"
            value={stats.totalUsers.toLocaleString()}
            icon={UsersIcon}
            change="+12% from last month"
            color="blue"
          />
          <StatCard
            title="Active Subscriptions"
            value={stats.activeSubscriptions.toLocaleString()}
            icon={CreditCardIcon}
            change="+8% from last month"
            color="green"
          />
          <StatCard
            title="Monthly Revenue"
            value={`$${stats.monthlyRevenue.toLocaleString()}`}
            icon={BanknotesIcon}
            change="+23% from last month"
            color="emerald"
          />
          <StatCard
            title="Total Workspaces"
            value={stats.totalWorkspaces.toLocaleString()}
            icon={BuildingOfficeIcon}
            change="+15% from last month"
            color="purple"
          />
        </div>

        {/* Content Grid */}
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
          {/* Recent Activity */}
          <div className="lg:col-span-2">
            <div className="bg-card border border-default rounded-xl p-6">
              <h3 className="text-xl font-semibold text-primary mb-4 flex items-center">
                <ChartBarIcon className="w-5 h-5 mr-2" />
                Recent Activity
              </h3>
              <div className="space-y-2">
                {recentActivity.map(activity => (
                  <ActivityItem key={activity.id} activity={activity} />
                ))}
              </div>
              <div className="mt-4 pt-4 border-t border-default">
                <button className="text-accent-primary hover:opacity-80 text-sm font-medium">
                  View all activity →
                </button>
              </div>
            </div>
          </div>

          {/* System Status */}
          <div className="space-y-6">
            <div className="bg-card border border-default rounded-xl p-6">
              <h3 className="text-xl font-semibold text-primary mb-4 flex items-center">
                <ShieldCheckIcon className="w-5 h-5 mr-2" />
                System Status
              </h3>
              <div className="space-y-4">
                <div className="flex items-center justify-between">
                  <span className="text-sm text-secondary">API Health</span>
                  <div className="flex items-center space-x-2">
                    <div className="w-2 h-2 bg-green-500 rounded-full"></div>
                    <span className="text-sm text-green-600">Healthy</span>
                  </div>
                </div>
                <div className="flex items-center justify-between">
                  <span className="text-sm text-secondary">Database</span>
                  <div className="flex items-center space-x-2">
                    <div className="w-2 h-2 bg-green-500 rounded-full"></div>
                    <span className="text-sm text-green-600">Connected</span>
                  </div>
                </div>
                <div className="flex items-center justify-between">
                  <span className="text-sm text-secondary">Payment Gateway</span>
                  <div className="flex items-center space-x-2">
                    <div className="w-2 h-2 bg-green-500 rounded-full"></div>
                    <span className="text-sm text-green-600">Active</span>
                  </div>
                </div>
                <div className="flex items-center justify-between">
                  <span className="text-sm text-secondary">OAuth Services</span>
                  <div className="flex items-center space-x-2">
                    <div className="w-2 h-2 bg-green-500 rounded-full"></div>
                    <span className="text-sm text-green-600">Connected</span>
                  </div>
                </div>
              </div>
            </div>

            {/* Quick Actions */}
            <div className="bg-card border border-default rounded-xl p-6">
              <h3 className="text-xl font-semibold text-primary mb-4 flex items-center">
                <CogIcon className="w-5 h-5 mr-2" />
                Quick Actions
              </h3>
              <div className="space-y-3">
                <button className="w-full text-left p-3 hover:bg-hover rounded-lg transition-colors">
                  <div className="flex items-center space-x-3">
                    <UsersIcon className="w-4 h-4 text-blue-600" />
                    <span className="text-sm text-primary">Manage Users</span>
                  </div>
                </button>
                <button className="w-full text-left p-3 hover:bg-hover rounded-lg transition-colors">
                  <div className="flex items-center space-x-3">
                    <CreditCardIcon className="w-4 h-4 text-green-600" />
                    <span className="text-sm text-primary">View Payments</span>
                  </div>
                </button>
                <button className="w-full text-left p-3 hover:bg-hover rounded-lg transition-colors">
                  <div className="flex items-center space-x-3">
                    <DocumentTextIcon className="w-4 h-4 text-purple-600" />
                    <span className="text-sm text-primary">System Logs</span>
                  </div>
                </button>
                <button className="w-full text-left p-3 hover:bg-hover rounded-lg transition-colors">
                  <div className="flex items-center space-x-3">
                    <CogIcon className="w-4 h-4 text-orange-600" />
                    <span className="text-sm text-primary">Platform Settings</span>
                  </div>
                </button>
              </div>
            </div>
          </div>
        </div>

        {/* Subscription Plans */}
        <div className="mb-8">
          <h3 className="text-2xl font-semibold text-primary mb-6">Subscription Plans Management</h3>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            {subscriptionPlans.map(plan => (
              <PlanCard key={plan.plan_id} plan={plan} />
            ))}
          </div>
        </div>

        {/* Performance Metrics */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <div className="bg-card border border-default rounded-xl p-6">
            <h3 className="text-xl font-semibold text-primary mb-4">Performance Metrics</h3>
            <div className="space-y-4">
              <div>
                <div className="flex justify-between text-sm mb-1">
                  <span className="text-secondary">API Response Time</span>
                  <span className="text-primary">124ms avg</span>
                </div>
                <div className="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                  <div className="bg-green-500 h-2 rounded-full" style={{width: '85%'}}></div>
                </div>
              </div>
              <div>
                <div className="flex justify-between text-sm mb-1">
                  <span className="text-secondary">Database Performance</span>
                  <span className="text-primary">97% uptime</span>
                </div>
                <div className="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                  <div className="bg-blue-500 h-2 rounded-full" style={{width: '97%'}}></div>
                </div>
              </div>
              <div>
                <div className="flex justify-between text-sm mb-1">
                  <span className="text-secondary">User Satisfaction</span>
                  <span className="text-primary">4.8/5.0</span>
                </div>
                <div className="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                  <div className="bg-emerald-500 h-2 rounded-full" style={{width: '96%'}}></div>
                </div>
              </div>
            </div>
          </div>

          <div className="bg-card border border-default rounded-xl p-6">
            <h3 className="text-xl font-semibold text-primary mb-4">Revenue Analytics</h3>
            <div className="space-y-4">
              <div className="flex items-center justify-between">
                <span className="text-secondary">Total Revenue (YTD)</span>
                <span className="text-xl font-bold text-primary">$147,230</span>
              </div>
              <div className="flex items-center justify-between">
                <span className="text-secondary">Average Revenue per User</span>
                <span className="text-lg font-semibold text-primary">$118.05</span>
              </div>
              <div className="flex items-center justify-between">
                <span className="text-secondary">Churn Rate</span>
                <span className="text-lg font-semibold text-green-600">2.3%</span>
              </div>
              <div className="flex items-center justify-between">
                <span className="text-secondary">Growth Rate</span>
                <span className="text-lg font-semibold text-blue-600">+23.4%</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default AdvancedAdminDashboard;